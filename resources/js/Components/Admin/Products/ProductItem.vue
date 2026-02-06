<template>
  <div class="max-w-6xl mx-auto p-6 bg-gray-50 min-h-screen">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

      <div class="relative">
        <div class="relative overflow-hidden rounded-xl shadow-lg bg-white">
          <img
            :src="selectedImage"
            alt="Produit"
            class="w-full h-[500px] object-contain cursor-zoom-in"
            @mousemove="zoomImage"
            @mouseleave="resetZoom"
            ref="mainImage"
          />

          <div
            v-if="zoomActive"
            class="loupe-zoom"
            :style="zoomStyle"
          ></div>
        </div>

        <div class="flex gap-3 mt-4 overflow-x-auto pb-2">
          <img
            v-for="(img, index) in product.images"
            :key="index"
            :src="img.url"
            class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 transition-all"
            :class="selectedImage === img.url ? 'border-orange-500 scale-105' : 'border-transparent hover:border-gray-300'"
            @click="selectedImage = img.url"
          />
        </div>
      </div>

      <div class="flex flex-col gap-6">
        <div>
          <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ product.name }}</h1>
          <p class="text-sm text-purple-600 font-semibold uppercase tracking-wider">
            {{ product.category?.name || 'Catégorie' }}
          </p>
        </div>

        <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>

        <div class="flex items-baseline gap-4">
          <span class="text-3xl font-bold text-orange-600">{{ formatPrice(product.price) }}</span>
          <span v-if="product.sale_price" class="text-xl text-gray-400 line-through">
            {{ formatPrice(product.sale_price) }}
          </span>
        </div>

        <hr class="border-gray-200" />

        <div class="flex flex-col gap-4">
          <label class="font-medium text-gray-700">Quantité</label>
          <div class="flex items-center gap-4">
            <input
              type="number"
              min="1"
              v-model.number="quantity"
              class="w-24 px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-orange-500 focus:outline-none transition-colors text-center font-bold"
            />
            <button
              @click="addToCart"
              class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-4 rounded-xl shadow-orange-200 shadow-lg transform active:scale-95 transition-all flex justify-center items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Ajouter au panier
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useCartStore } from '@/stores/cartStore'
import { useRoute } from 'vue-router'
import { useProductStore } from '@/stores/productStore'

const cartStore = useCartStore()
const productStore = useProductStore()
const route = useRoute()

const product = ref({ images: [] })
const selectedImage = ref('')
const quantity = ref(1)

// --- Logique du Zoom ---
const zoomActive = ref(false)
const mainImage = ref(null)
const zoomStyle = reactive({
  top: '0px',
  left: '0px',
  backgroundPosition: '0% 0%',
  backgroundImage: ''
})

const fetchProduct = async () => {
  await productStore.fetchProduct(route.params.id)
  product.value = productStore.product
  selectedImage.value = product.value.images?.[0]?.url || 'https://picsum.photos/600/400'
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XAF',
    maximumFractionDigits: 0
  }).format(price)
}

const addToCart = () => {
  cartStore.add({ ...product.value, quantity: quantity.value })
  alert('Produit ajouté au panier !')
}

const zoomImage = (e) => {
  zoomActive.value = true
  const rect = mainImage.value.getBoundingClientRect()

  // Calcul de la position relative de la souris (0 à 100%)
  const xPercent = ((e.clientX - rect.left) / rect.width) * 100
  const yPercent = ((e.clientY - rect.top) / rect.height) * 100

  // Positionnement de la loupe pour qu'elle soit centrée sur le curseur
  // 64px correspond à la moitié de la taille de la loupe (128px / 2)
  zoomStyle.left = `${e.clientX - rect.left - 64}px`
  zoomStyle.top = `${e.clientY - rect.top - 64}px`

  // Mise à jour de l'image de fond et de son décalage
  zoomStyle.backgroundImage = `url(${selectedImage.value})`
  zoomStyle.backgroundPosition = `${xPercent}% ${yPercent}%`
}

const resetZoom = () => {
  zoomActive.value = false
}

onMounted(() => {
  fetchProduct()
})
</script>

<style scoped>
.loupe-zoom {
  position: absolute;
  width: 128px;
  height: 128px;
  border: 3px solid white;
  border-radius: 50%;
  pointer-events: none;
  background-size: 800px; /* Ajustez pour un zoom plus ou moins fort */
  background-repeat: no-repeat;
  z-index: 50;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
  background-color: white;
}

/* Optionnel : transition douce pour le déplacement */
.loupe-zoom {
  transition: opacity 0.2s ease;
}
</style>
