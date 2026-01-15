import { defineStore } from 'pinia'
import axios from 'axios'

/**
 * SOLUTION AU PROBLÈME UNDEFINED :
 * On récupère la variable VITE, mais si elle est absente (cas du build Render),
 * on utilise window.location.origin pour construire l'URL dynamiquement.
 */
const getApiBase = () => {
    if (import.meta.env.VITE_API_BASE_URL) {
        return import.meta.env.VITE_API_BASE_URL;
    }
    // Fallback dynamique si la variable est indéfinie
    return typeof window !== 'undefined' ? `${window.location.origin}/api` : '';
};

const API_BASE = getApiBase();

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
        const config = {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          params: {
            page: page
          }
        };

        if (categoryId) {
          config.params.category_id = categoryId;
        }

        // Log pour vérifier l'URL finale dans la console du navigateur
        console.log(`📡 Tentative de connexion à : ${API_BASE}/admin/products`);

        const res = await axios.get(`${API_BASE}/admin/products`, config);

        if (res.data.success) {
          const paginatedData = res.data.data;
          this.products = paginatedData.data || [];
          this.pagination = {
            current_page: paginatedData.current_page,
            last_page: paginatedData.last_page,
            total: paginatedData.total
          };
          console.log(`✅ ${this.products.length} produits chargés.`);
        }
      } catch (err) {
        console.error("❌ Erreur chargement produits:", err.response?.data || err.message);
      } finally {
        this.loading = false;
      }
    },

    async deleteProduct(id) {
      try {
        await axios.delete(`${API_BASE}/admin/products/${id}`, {
          headers: { 'Accept': 'application/json' }
        });
        // Rafraîchir avec les filtres actuels
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
