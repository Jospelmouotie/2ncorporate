<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '@/stores/productStore'

const route = useRoute()
const productStore = useProductStore()

// Références
const product = computed(() => productStore.product)
const selectedImage = ref('')
const qty = ref(1)
const isZooming = ref(false)
const container = ref(null)

// Style dynamique pour l'effet loupe
const zoomLensStyle = reactive({
  top: '0px',
  left: '0px',
  backgroundPosition: '0% 0%',
  backgroundImage: '',
  backgroundSize: '250%' // Facteur de zoom
})

// Résolution URL (local ou externe)
const resolveImage = (url) => {
  if (!url) return '/images/placeholder.png'
  if (url.startsWith('http://') || url.startsWith('https://')) return url
  return `/storage/products/images/${url}`
}

onMounted(async () => {
  const productId = route.params.id
  if (productId) {
    await productStore.fetchProduct(productId)
    if (product.value?.images?.length > 0) {
      selectedImage.value = resolveImage(product.value.images[0].url)
    }
  }
})

// Logique de la loupe
const handleZoom = (e) => {
  if (!container.value) return
  isZooming.value = true

  const { left, top, width, height } = container.value.getBoundingClientRect()
  const x = ((e.clientX - left) / width) * 100
  const y = ((e.clientY - top) / height) * 100

  zoomLensStyle.backgroundImage = `url(${selectedImage.value})`
  zoomLensStyle.backgroundPosition = `${x}% ${y}%`
  zoomLensStyle.left = `${e.clientX - left - 80}px`
  zoomLensStyle.top = `${e.clientY - top - 80}px`
}

// Changement d'image
const selectImage = (img) => {
  selectedImage.value = resolveImage(img.url)
}
</script>

<template>
  <div v-if="product" class="min-h-screen bg-slate-50 p-4 md:p-12 font-sans">
    <div class="max-w-7xl mx-auto">

      <router-link to="/shop" class="inline-flex items-center gap-2 text-slate-500 font-bold hover:text-orange-600 transition-colors mb-8 group">
        <span class="group-hover:-translate-x-1 transition-transform">←</span> Retour à la boutique
      </router-link>

      <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden grid grid-cols-1 lg:grid-cols-2">

        <!-- Images -->
        <div class="p-8 lg:p-12 bg-slate-50/30 space-y-6">
          <div
            ref="container"
            class="relative aspect-square rounded-[2.5rem] bg-white border border-slate-200 overflow-hidden cursor-crosshair group"
            @mousemove="handleZoom"
            @mouseleave="isZooming = false"
          >
            <img
              :src="selectedImage"
              class="w-full h-full object-contain p-8 transition-transform duration-500"
              :class="{ 'opacity-0': isZooming }"
            />

            <div
              v-show="isZooming"
              class="absolute pointer-events-none border-4 border-white shadow-2xl rounded-full w-40 h-40 bg-white bg-no-repeat z-10"
              :style="zoomLensStyle"
            ></div>
          </div>

          <div class="flex gap-4 overflow-x-auto py-2">
            <button
              v-for="(img, idx) in product.images" :key="idx"
              @click="selectImage(img)"
              class="w-24 h-24 rounded-2xl bg-white border-2 shrink-0 transition-all overflow-hidden"
              :class="selectedImage === resolveImage(img.url) ? 'border-orange-500 scale-105 shadow-lg' : 'border-transparent opacity-60 hover:opacity-100'"
            >
              <img :src="resolveImage(img.url)" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <!-- Infos produit -->
        <div class="p-8 lg:p-16 flex flex-col justify-center">
          <div class="space-y-6">
            <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-600 text-[10px] font-black uppercase tracking-[0.2em] rounded-full">
              {{ product.category?.name || 'Général' }}
            </span>

            <h1 class="text-5xl font-black text-slate-900 leading-tight">
              {{ product.name }}
            </h1>

            <div class="flex items-baseline gap-2">
              <span class="text-4xl font-black text-slate-900">{{ new Intl.NumberFormat().format(product.price) }}</span>
              <span class="text-lg font-bold text-slate-400">FCFA</span>
            </div>

            <p class="text-slate-500 leading-relaxed text-lg border-l-4 border-orange-500 pl-6 italic">
              {{ product.description || 'Aucune description disponible pour cet article.' }}
            </p>

            <div class="pt-8 space-y-4">
              <div class="flex items-center gap-6">
                <div class="flex items-center bg-slate-100 rounded-2xl p-1 border border-slate-200">
                  <button @click="qty > 1 ? qty-- : null" class="w-12 h-12 flex items-center justify-center font-bold hover:bg-white rounded-xl transition-colors">-</button>
                  <span class="w-12 text-center font-black">{{ qty }}</span>
                  <button @click="qty++" class="w-12 h-12 flex items-center justify-center font-bold hover:bg-white rounded-xl transition-colors">+</button>
                </div>

                <button class="flex-1 bg-slate-900 text-white py-5 rounded-[1.5rem] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl active:scale-95">
                  Mettre à jour le stock
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div v-else class="h-screen flex flex-col items-center justify-center gap-4">
    <div class="w-12 h-12 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
    <p class="font-black text-slate-400 uppercase tracking-widest text-xs">Chargement du produit...</p>
  </div>
</template>
