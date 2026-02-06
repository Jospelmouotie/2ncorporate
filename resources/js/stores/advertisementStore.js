import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAdStore = defineStore('ad', {
  state: () => ({
    ads: [],
    loading: false,
  }),

  actions: {
    async fetchAds() {
      this.loading = true
      try {
        const res = await api.get('/admin/advertisements')
        this.ads = res.data?.data || []
      } finally {
        this.loading = false
      }
    },

    async createAd(adData) {
      const formData = new FormData()
      formData.append('title', adData.title)
      formData.append('type', adData.type)
      formData.append('media', adData.media)
      formData.append('start_at', adData.start_at)
      if (adData.end_at) formData.append('end_at', adData.end_at)

      // L'URL doit être identique à celle définie dans api.php
      return await api.post('/admin/advertisements', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    },
    async deleteAd(id) {
      await api.delete(`/admin/advertisements/${id}`)
      this.ads = this.ads.filter(a => a.id !== id)
    }
  }
})
