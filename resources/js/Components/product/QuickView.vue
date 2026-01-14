<script setup>
import { computed } from 'vue'
import ProductGallery from './ProductGallery.vue'
import ProductRating from './ProductRating.vue'

const props = defineProps({
  modelValue: Boolean,
  product: Object
})

const emit = defineEmits(['update:modelValue'])

const close = () => emit('update:modelValue', false)
</script>

<template>
  <teleport to="body">
    <div v-if="modelValue" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">

      <div class="bg-white max-w-4xl w-full rounded-2xl p-6 relative animate-scale">

        <button @click="close" class="absolute top-4 right-4 text-xl">✕</button>

        <div class="grid md:grid-cols-2 gap-6">
          <ProductGallery :images="product.images" />

          <div>
            <h2 class="text-2xl font-black">{{ product.name }}</h2>
            <ProductRating :rating="product.rating" />
            <p class="text-xl font-black mt-4">{{ product.price }} FCFA</p>

            <button class="mt-6 bg-orange-600 text-white px-6 py-3 rounded-xl font-bold">
              Ajouter au panier
            </button>
          </div>
        </div>

      </div>
    </div>
  </teleport>
</template>

<style scoped>
.animate-scale {
  animation: scale .2s ease;
}
@keyframes scale {
  from { transform: scale(.95); opacity: 0 }
  to { transform: scale(1); opacity: 1 }
}
</style>
