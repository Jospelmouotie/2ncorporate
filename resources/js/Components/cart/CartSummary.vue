<template>
  <div class="p-4 border-t border-gray-200 bg-gray-50 space-y-3">
    <div class="flex justify-between font-semibold text-gray-700">
      <span>Total:</span>
      <span>{{ totalPrice.toLocaleString() }} FCFA</span>
    </div>
    <button
      class="w-full py-3 bg-gradient-to-r from-orange-400 to-orange-500 text-white rounded-lg font-bold hover:shadow-xl transition-all"
      @click="checkout"
    >
      Passer à la caisse
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useCartStore } from '@/stores/cartStore'

const cart = useCartStore()
const totalPrice = computed(() =>
  cart.items.reduce((sum, item) => sum + item.price * (item.quantity || 1), 0)
)

const checkout = () => {
  alert('Redirection vers la page de paiement...')
}
</script>
