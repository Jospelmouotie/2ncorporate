import { defineStore } from 'pinia'

export const useModalStore = defineStore('modal', {
  state: () => ({
    isOpen: false,
    message: '',
    product: null,
    type: 'cart' // 'cart' ou 'wishlist'
  }),
  actions: {
    open(message, product, type = 'cart') {
      this.message = message
      this.product = product
      this.type = type
      this.isOpen = true
    },
    close() {
      this.isOpen = false
    }
  }
})
