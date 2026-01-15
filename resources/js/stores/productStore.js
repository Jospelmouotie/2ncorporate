import { defineStore } from 'pinia'
import axios from 'axios'

const API_BASE = import.meta.env.VITE_API_BASE_URL;

export const useProductStore = defineStore('product', {
  state: () => ({
    products: [],
    pagination: null,
    loading: false,
    currentCategoryId: null // On garde trace de la catégorie filtrée
  }),

  actions: {
    async fetchProducts(page = 1, categoryId = null) {
      this.loading = true;
      this.currentCategoryId = categoryId; // On stocke la catégorie actuelle

      try {
        // Construction de l'URL avec les paramètres page et category_id
        let url = `${API_BASE}/admin/products?page=${page}`;
        if (categoryId) {
          url += `&category_id=${categoryId}`;
        }

        const res = await axios.get(url);

        if (res.data.success) {
          const paginatedData = res.data.data;
          this.products = paginatedData.data; // Les 10 produits
          this.pagination = {
            current_page: paginatedData.current_page,
            last_page: paginatedData.last_page,
            total: paginatedData.total
          };
        }
      } catch (err) {
        console.error("Erreur chargement produits:", err);
      } finally {
        this.loading = false;
      }
    },

    async deleteProduct(id) {
      try {
        await axios.delete(`${API_BASE}/admin/products/${id}`);
        // Refresh la page actuelle après suppression
        await this.fetchProducts(this.pagination?.current_page || 1);
        return true;
      } catch (err) {
        throw new Error("Échec de la suppression");
      }
    },

    async updateProduct(id, formData) {
      this.loading = true;
      try {
        // Astuce Laravel : On simule un PUT via un POST pour supporter les fichiers (Multipart)
        if (formData instanceof FormData) {
          formData.append('_method', 'PUT');
        }

        const res = await axios.post(`${API_BASE}/admin/products/${id}`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        // Rafraîchir la liste pour avoir les données à jour (image, prix, etc.)
        await this.fetchProducts();
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
