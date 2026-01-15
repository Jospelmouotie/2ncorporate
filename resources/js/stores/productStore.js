import { defineStore } from 'pinia'
import axios from 'axios'

const API_BASE = import.meta.env.VITE_API_BASE_URL;

export const useProductStore = defineStore('product', {
  state: () => ({
    products: [],
    pagination: null,
    loading: false,
    currentCategoryId: null
  }),

  actions: {
    async fetchProducts(page = 1, categoryId = null) {
      this.loading = true;
      this.currentCategoryId = categoryId;

      try {
        // Ajout des headers pour forcer la réponse JSON
        const config = {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          params: {
            page: page
          }
        };

        // Si on a une catégorie, on l'ajoute aux paramètres
        if (categoryId) {
          config.params.category_id = categoryId;
        }

        console.log(`Requête vers: ${API_BASE}/admin/products`, config.params);

        const res = await axios.get(`${API_BASE}/admin/products`, config);

        if (res.data.success) {
          const paginatedData = res.data.data;
          this.products = paginatedData.data || [];
          this.pagination = {
            current_page: paginatedData.current_page,
            last_page: paginatedData.last_page,
            total: paginatedData.total
          };
        }
      } catch (err) {
        console.error("Erreur chargement produits:", err.response?.data || err.message);
      } finally {
        this.loading = false;
      }
    },

    async deleteProduct(id) {
      try {
        await axios.delete(`${API_BASE}/admin/products/${id}`, {
          headers: { 'Accept': 'application/json' }
        });
        // On rafraîchit avec le filtre actuel
        await this.fetchProducts(this.pagination?.current_page || 1, this.currentCategoryId);
        return true;
      } catch (err) {
        console.error("Erreur suppression:", err);
        throw new Error("Échec de la suppression");
      }
    },

    async updateProduct(id, formData) {
      this.loading = true;
      try {
        if (formData instanceof FormData) {
          formData.append('_method', 'PUT');
        }

        const res = await axios.post(`${API_BASE}/admin/products/${id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
          }
        });

        await this.fetchProducts(this.pagination?.current_page || 1, this.currentCategoryId);
        return res.data.data;
      } catch (err) {
        console.error("Erreur update :", err);
        throw err.response?.data || err;
      } finally {
        this.loading = false;
      }
    }
  }
})
