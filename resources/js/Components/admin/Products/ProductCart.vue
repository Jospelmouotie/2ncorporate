<template>
  <div
    class="bg-white rounded-xl shadow-md hover:shadow-2xl transition-shadow p-4 flex flex-col"
  >
    <!-- Image -->
    <img
      :src="product.images?.[0]?.url || 'https://picsum.photos/400/300?random=' + product.id"
      alt="Image produit"
      class="h-48 w-full object-cover rounded-md mb-4"
    />

    <!-- Infos -->
    <h2 class="text-lg font-semibold text-gray-700">{{ product.name }}</h2>
    <p class="text-orange-600 font-bold">{{ formatPrice(product.price) }}</p>

    <!-- Actions -->
    <div class="mt-auto flex justify-between items-center">
      <button
        @click="$emit('edit', product)"
        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-1 rounded-lg transition-transform hover:scale-105"
      >
        Modifier
      </button>
      <button
        @click="$emit('delete', product.id)"
        class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded-lg transition-transform hover:scale-105"
      >
        Supprimer
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  product: Object
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(price)
}
</script>
