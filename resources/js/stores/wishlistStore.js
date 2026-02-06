import { defineStore } from 'pinia'
import api from '@/services/api' // Utilise ton instance axios configurée

export const useWishlistStore = defineStore('wishlist', {
  state: () => ({
    items: [],
    loading: false,
  }),

  getters: {
    count: (state) => state.items.length
  },

  actions: {
    async fetchWishlist() {
      this.loading = true;
      try {
        const res = await api.get('/wishlist'); // Utilise l'URL relative
        this.items = res.data.data || [];
      } catch (err) {
        console.error("❌ Erreur wishlist:", err.message);
      } finally {
        this.loading = false;
      }
    },

    async addToWishlist(productId) {
      try {
        const res = await api.post('/wishlist/add', { product_id: productId });
        if (res.data.success) {
          await this.fetchWishlist();
          return { success: true, message: res.data.message };
        }
      } catch (err) {
        return { success: false, message: "Erreur lors de l'ajout" };
      }
    },

    async removeFromWishlist(productId) {
      try {
        await api.delete(`/wishlist/remove/${productId}`);
        this.items = this.items.filter(i => i.id !== productId);
      } catch (err) {
        console.error("❌ Erreur retrait:", err.message);
      }
    }
  }
})
