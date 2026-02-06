import { defineStore } from 'pinia'
import api from '@/services/api'

export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [],
    category: null,
    loading: false,
    error: null,
  }),

  actions: {
    // ✅ MODIFIÉ : Utilise la route publique pour la vue client
    async fetchCategories() {
      this.loading = true
      try {
        // Suppression du préfixe /admin pour l'accès public
        const res = await api.get('/categories')
        // Vérifie si ton API retourne res.data.data ou juste res.data
        this.categories = res.data.data || res.data
      } catch (err) {
        this.error = err.message
      } finally {
        this.loading = false
      }
    },

    // ✅ Récupérer une catégorie précise (Public)
    async fetchCategory(id) {
      this.loading = true
      try {
        const res = await api.get(`/categories/${id}`)
        this.category = res.data.data || res.data
      } catch (err) {
        this.error = err.message
      } finally {
        this.loading = false
      }
    },

    // --- LES MÉTHODES CI-DESSOUS GARDENT /ADMIN CAR ELLES SONT RÉSERVÉES À L'ADMIN ---

    async createCategory(formData) {
      this.loading = true
      try {
        const res = await api.post('/admin/categories', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        this.categories.push(res.data.data)
      } finally {
        this.loading = false
      }
    },

    async updateCategory(id, formData) {
      this.loading = true
      try {
        if (!formData.has('_method')) formData.append('_method', 'PUT')
        const res = await api.post(`/admin/categories/${id}`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        const index = this.categories.findIndex(c => c.id === id)
        if (index !== -1) this.categories[index] = res.data.data
        return res.data.data
      } finally {
        this.loading = false
      }
    },

    async deleteCategory(id) {
      this.loading = true
      try {
        await api.delete(`/admin/categories/${id}`)
        this.categories = this.categories.filter(c => c.id !== id)
      } finally {
        this.loading = false
      }
    }
  }
})
