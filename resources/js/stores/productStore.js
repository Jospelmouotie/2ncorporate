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
    async fetchProducts() {
      this.loading = true
      try {
        // Utilisation de la variable du .env
        const res = await axios.get(`${API_BASE}/admin/products`)

        // Gestion de la structure de données Laravel Paginate
        // On vérifie plusieurs niveaux pour être robuste
        const responseData = res.data.data;
        this.products = responseData?.data || responseData || []
      } catch (err) {
        console.error("Erreur de chargement :", err)
        this.error = "Impossible de charger les produits"
      } finally {
        this.loading = false
      }
    },

    async deleteProduct(id) {
      try {
        await axios.delete(`${API_BASE}/admin/products/${id}`)

        // Mise à jour réactive de l'interface
        this.products = this.products.filter(p => p.id !== id)
        return true
      } catch (err) {
        console.error("Erreur suppression :", err)
        throw new Error("Échec de la suppression")
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
