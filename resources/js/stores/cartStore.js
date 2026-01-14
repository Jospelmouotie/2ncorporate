import { defineStore } from 'pinia'
import axios from 'axios'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    cartId: null, // Stockage de l'ID du panier pour le checkout
    loading: false
  }),
  getters: {
    totalItems: (state) => state.items.reduce((sum, item) => sum + (item.quantity || 0), 0),
    totalPrice: (state) => state.items.reduce((sum, item) => {
      const price = item.product?.final_price || item.product?.price || 0
      return sum + (price * item.quantity)
    }, 0)
  },
  actions: {
    async fetchCart() {
      this.loading = true
      try {
        const res = await axios.get('/api/cart')
        this.items = res.data.data?.items || []
        this.cartId = res.data.data?.id || null
      } finally { this.loading = false }
    },

    async addToCart(productId, quantity = 1) {
      try {
        const res = await axios.post('/api/cart/add', {
          product_id: productId,
          quantity: quantity
        })
        if (res.data.success) {
          await this.fetchCart()
          return true
        }
      } catch (e) { return false }
    },

    async updateQuantity(productId, newQty) {
      if (newQty < 1) return
      try {
        await axios.post('/api/cart/add', {
          product_id: productId,
          quantity: newQty,
          override_quantity: true
        })
        await this.fetchCart()
      } catch (e) { console.error(e) }
    },

    async removeItem(productId) {
      try {
        await axios.delete(`/api/cart/remove/${productId}`)
        await this.fetchCart()
      } catch (e) { console.error(e) }
    },

    clearCart() {
      this.items = []
      this.cartId = null
    }
  }
})
