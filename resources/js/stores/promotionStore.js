import { defineStore } from 'pinia'
import api from '@/services/api'

export const usePromotionStore = defineStore('promotion', {
  state: () => ({
    promotions: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchPromotions() {
      this.loading = true
      try {
        const res = await api.get('/promotions')
        // On descend dans res.data.data car Laravel imbrique les objets
        this.promotions = res.data?.data || []
      } catch (err) {
        this.error = "Erreur de chargement"
        console.error(err)
      } finally {
        this.loading = false
      }
    },

    async createPromotion(formData) {
      try {
        const res = await api.post('/admin/promotions', formData)
        const newPromo = res.data?.data
        if (newPromo) {
          this.promotions.unshift(newPromo)
        }
        return res.data
      } catch (err) {
        throw err // On laisse le composant gérer l'alerte 422
      }
    },

    async deletePromotion(id) {
      try {
        await api.delete(`/admin/promotions/${id}`)
        this.promotions = this.promotions.filter(p => p.id !== id)
      } catch (err) {
        console.error("Erreur suppression", err)
      }
    }
  }
})
