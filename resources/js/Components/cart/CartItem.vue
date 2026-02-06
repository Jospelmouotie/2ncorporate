<template>
  <div class="flex items-center gap-4 p-3 border rounded-xl hover:bg-gray-50 transition-all group">
    <div class="w-20 h-20 shrink-0 rounded-lg overflow-hidden border">
      <img
        :src="product.image_url || product.image || '/images/placeholder.jpg'"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
      />
    </div>

    <div class="flex-1 min-w-0">
      <h3 class="font-bold text-gray-900 truncate uppercase text-sm italic">{{ product.name }}</h3>
      <p class="text-orange-600 font-black">{{ formatPrice(product.price) }}</p>
      <div class="flex items-center gap-2 mt-1">
        <span class="text-xs text-gray-400 font-medium">Qté: {{ product.quantity }}</span>
      </div>
    </div>

    <button @click="$emit('remove')" class="p-2 text-gray-300 hover:text-red-500 transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
      </svg>
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  product: { type: Object, required: true }
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(price)
}
</script>
