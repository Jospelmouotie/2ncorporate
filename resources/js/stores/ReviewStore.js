import { defineStore } from 'pinia'
import api from '@/services/api'

export const useReviewStore = defineStore('review', {
  state: () => ({
    reviews: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchReviews(productId) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get(`/products/${productId}/reviews`)
        this.reviews = res.data.data
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      } finally {
        this.loading = false
      }
    },
    async addReview(productId, reviewData) {
      this.loading = true
      this.error = null
      try {
        const res = await api.post(`/products/${productId}/reviews`, reviewData)
        this.reviews.push(res.data.data)
        return res.data.data
      } catch (err) {
        this.error = err.response?.data?.message || err.message
        throw err
      } finally {
        this.loading = false
      }
    },
    async updateReview(reviewId, reviewData) {
      this.loading = true
      this.error = null
      try {
        const res = await api.put(`/reviews/${reviewId}`, reviewData)
        const index = this.reviews.findIndex(r => r.id === reviewId)
        if (index !== -1) this.reviews[index] = res.data.data
        return res.data.data
      } catch (err) {
        this.error = err.response?.data?.message || err.message
        throw err
      } finally {
        this.loading = false
      }
    },
    async deleteReview(reviewId) {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/reviews/${reviewId}`)
        this.reviews = this.reviews.filter(r => r.id !== reviewId)
      } catch (err) {
        this.error = err.response?.data?.message || err.message
        throw err
      } finally {
        this.loading = false
      }
    }
  }
})
