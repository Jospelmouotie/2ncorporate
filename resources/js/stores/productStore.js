import { defineStore } from 'pinia'
import axios from 'axios'

// On récupère la base URL du .env (http://localhost:8000/api par exemple)
const API_BASE = import.meta.env.VITE_API_BASE_URL;

export const useProductStore = defineStore('product', {
  state: () => ({
    products: [],
    product: null,
    loading: false,
    error: null
  }),

  actions: {
async fetchProducts(page = 1) {
      this.loading = true;
      try {
        // On passe le numéro de la page à l'API Laravel
        const res = await axios.get(`${API_BASE}/admin/products?page=${page}`);

        if (res.data.success) {
          // Structure Laravel Paginate : res.data.data contient l'objet complet de pagination
          // res.data.data.data contient le tableau d'objets produits
          this.products = res.data.data.data;
          this.pagination = {
            current_page: res.data.data.current_page,
            last_page: res.data.data.last_page,
            prev_page_url: res.data.data.prev_page_url,
            next_page_url: res.data.data.next_page_url,
            total: res.data.data.total
          };
        }
      } catch (err) {
        console.error("Erreur de chargement :", err);
        this.error = "Impossible de charger les produits";
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
