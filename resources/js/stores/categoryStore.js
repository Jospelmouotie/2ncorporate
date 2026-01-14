import { defineStore } from 'pinia'
import api from '@/services/api'

export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [],      // liste des catégories
    category: null,      // catégorie sélectionnée
    loading: false,
    error: null,
  }),

  actions: {
    // ✅ Récupérer toutes les catégories
    async fetchCategories() {
      this.loading = true
      this.error = null
      try {
        const res = await api.get('/admin/categories')
        this.categories = res.data.data
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      } finally {
        this.loading = false
      }
    },

    // ✅ Récupérer une catégorie précise
    async fetchCategory(id) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get(`/admin/categories/${id}`)
        this.category = res.data.data
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      } finally {
        this.loading = false
      }
    },

    // ✅ Créer une nouvelle catégorie
  async createCategory(formData) { // On reçoit un FormData ici
  this.loading = true
  try {
    const res = await api.post('/admin/categories', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    this.categories.push(res.data.data)
  } catch (err) {
    throw err
  } finally {
    this.loading = false
  }
},

    // ✅ Mettre à jour une catégorie
async updateCategory(id, formData) {
  this.loading = true
  try {
    // On s'assure que _method est bien présent
    if (!formData.has('_method')) {
        formData.append('_method', 'PUT');
    }

    // IMPORTANT : On utilise .post() car Laravel/PHP ne gère pas le multipart en .put()
    const res = await api.post(`/admin/categories/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    })

    const index = this.categories.findIndex(c => c.id === id)
    if (index !== -1) {
        this.categories[index] = res.data.data
    }
    return res.data.data
  } catch (err) {
    // On renvoie l'erreur pour la traiter dans le composant
    console.error("Détails erreur 422:", err.response?.data?.errors);
    throw err
  } finally {
    this.loading = false
  }
},
    // ✅ Supprimer une catégorie
    async deleteCategory(id) {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/categories/${id}`)
        this.categories = this.categories.filter(c => c.id !== id)
        if (this.category?.id === id) this.category = null
      } catch (err) {
        this.error = err.response?.data?.message || err.message
        throw err
      } finally {
        this.loading = false
      }
    }
  }
})
