<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4 md:px-10 mt-16 md:mt-20">

    <div v-if="loading" class="max-w-7xl mx-auto animate-pulse flex flex-col md:flex-row gap-12">
      <div class="w-full md:w-1/2 h-[400px] bg-gray-200 rounded-[3rem]"></div>
      <div class="w-full md:w-1/2 space-y-6">
        <div class="h-10 bg-gray-200 w-3/4 rounded-xl"></div>
        <div class="h-32 bg-gray-200 w-full rounded-xl"></div>
      </div>
    </div>

    <div v-else-if="product" class="max-w-7xl mx-auto">
      <div class="flex flex-col lg:flex-row gap-10 lg:gap-16 items-start">

        <div class="w-full lg:w-1/2 lg:sticky lg:top-32">
          <div
            class="relative aspect-square rounded-[2rem] md:rounded-[3.5rem] overflow-hidden bg-white shadow-2xl border-4 border-white group"
            @mousemove="handleZoom"
            @mouseleave="isZooming = false"
            @touchstart="handleMobileZoomStart"
            @touchmove="handleMobileZoomMove"
            @touchend="isZooming = false"
            ref="zoomContainer"
          >
            <img
              :src="selectedImage || product.image_url"
              class="w-full h-full object-contain p-6 transition-transform duration-200"
              :style="isMobile && isZooming ? mobileZoomStyle : (isZooming ? 'opacity: 0' : '')"
            />

            <div
              v-if="isZooming && !isMobile"
              class="absolute border-2 border-orange-500 bg-orange-500/10 pointer-events-none rounded-xl z-10"
              :style="lensStyle"
            ></div>

            <div v-if="hasDiscount" class="absolute top-6 left-6 bg-red-600 text-white px-5 py-2 rounded-2xl font-black text-xs md:text-sm uppercase italic z-20 shadow-xl animate-bounce">
              -{{ calculateDiscount(product.price, product.final_price) }}%
            </div>
          </div>

          <div v-if="allImages.length > 1" class="flex gap-4 mt-8 overflow-x-auto pb-4 scrollbar-hide px-2">
            <button
              v-for="(img, idx) in allImages" :key="idx"
              @mouseenter="selectedImage = img"
              @click="selectedImage = img"
              class="w-20 h-20 md:w-24 md:h-24 rounded-2xl overflow-hidden border-4 transition-all flex-shrink-0 bg-white"
              :class="selectedImage === img ? 'border-orange-500 scale-110 shadow-lg' : 'border-transparent opacity-60'"
            >
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <div class="w-full lg:w-1/2 relative">
          <div
            v-if="isZooming && !isMobile"
            class="absolute inset-0 z-50 bg-white rounded-[3rem] border-8 border-white shadow-2xl overflow-hidden pointer-events-none"
            :style="zoomResultStyle"
          ></div>

          <div class="space-y-8">
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full font-black text-[10px] uppercase tracking-tighter">
                  {{ product.category?.name || 'Nouveauté' }}
                </span>
                <div class="flex items-center gap-1">
                  <Star class="w-4 h-4 fill-orange-500 text-orange-500" />
                  <span class="font-black text-sm text-slate-900">{{ product.reviews_avg_rating || '5.0' }}</span>
                </div>
              </div>
              <h1 class="text-5xl md:text-7xl font-black text-slate-900 leading-[0.9] uppercase italic tracking-tighter">
                {{ product.name }}
              </h1>
            </div>

            <div class="flex items-baseline gap-6">
              <span class="text-6xl font-black text-orange-600 tracking-tighter italic">
                {{ formatPrice(product.final_price || product.price) }} <small class="text-2xl not-italic">F</small>
              </span>
              <span v-if="hasDiscount" class="text-2xl text-slate-300 line-through font-bold">
                {{ formatPrice(product.price) }} F
              </span>
            </div>

            <div class="p-8 bg-white rounded-[2.5rem] border-2 border-slate-100 shadow-sm border-l-orange-500 border-l-8">
               <p class="text-slate-600 text-lg leading-relaxed font-medium italic">
                "{{ product.description }}"
              </p>
            </div>

            <div class="space-y-6 pt-4">
              <div class="flex items-center gap-6">
                <div class="flex items-center border-4 border-slate-100 rounded-2xl p-1 bg-white">
                  <button @click="qty > 1 ? qty-- : null" class="w-12 h-12 flex items-center justify-center hover:bg-orange-50 rounded-xl transition-all font-black text-2xl">−</button>
                  <span class="w-12 text-center font-black text-xl">{{ qty }}</span>
                  <button @click="qty++" class="w-12 h-12 flex items-center justify-center hover:bg-orange-50 rounded-xl transition-all font-black text-2xl">+</button>
                </div>
                <div class="flex flex-col">
                  <span class="text-green-500 font-black uppercase text-[10px] tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span> Stock disponible
                  </span>
                  <span class="text-slate-400 text-[10px] font-bold">Livraison en 24h-48h</span>
                </div>
              </div>

              <div class="flex gap-4">
                <button @click="addToCart" class="flex-grow bg-slate-900 text-white py-6 rounded-[2rem] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-2xl flex items-center justify-center gap-4 active:scale-95">
                  <ShoppingCart class="w-6 h-6" /> Ajouter au panier
                </button>
                <button class="w-20 border-4 border-slate-100 rounded-[2rem] flex items-center justify-center transition-all group active:scale-90">
                  <Heart class="w-8 h-8 text-slate-300 group-hover:text-red-500 group-hover:fill-red-500 transition-colors" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <section class="mt-24 pt-16 border-t-2 border-slate-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
          <div>
            <h2 class="text-4xl font-black uppercase italic tracking-tighter text-slate-900">Avis Clients</h2>
            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest mt-2">Partagez votre expérience avec ce produit</p>
          </div>

          <div v-if="!isAuthenticated" class="flex items-center gap-3 bg-orange-50 px-6 py-3 rounded-full border border-orange-200">
            <Lock class="w-4 h-4 text-orange-600" />
            <span class="text-orange-600 font-black uppercase text-[10px]">Connectez-vous pour noter</span>
          </div>
        </div>

        <ReviewSection
          :productId="product.id"
          :reviews="product.reviews || []"
          :isAuthenticated="isAuthenticated"
          @review-added="handleNewReview"
        />

        <div v-if="!isAuthenticated" class="mt-12 bg-slate-900 rounded-[3rem] p-12 text-center text-white relative overflow-hidden shadow-2xl">
          <div class="relative z-10">
            <Star class="w-16 h-16 text-orange-500 mx-auto mb-6 animate-pulse" />
            <h3 class="text-3xl font-black uppercase italic mb-4 tracking-tight">Donnez votre avis !</h3>
            <p class="text-slate-400 mb-8 max-w-lg mx-auto font-medium">Seuls les membres de la communauté connectés peuvent laisser une note et un commentaire sur nos produits.</p>
            <router-link to="/login" class="inline-block bg-orange-500 text-white px-12 py-5 rounded-2xl font-black uppercase tracking-widest hover:scale-105 transition-all shadow-xl">
              Se connecter maintenant
            </router-link>
          </div>
          <div class="absolute -top-10 -right-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl"></div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { Heart, ShoppingCart, Truck, ShieldCheck, Star, Lock } from 'lucide-vue-next'
import ReviewSection from '@/Components/reviews/ReviewSection.vue'

const route = useRoute()
const router = useRouter()
const product = ref(null)
const loading = ref(true)
const selectedImage = ref(null)
const qty = ref(1)

// Auth
const isAuthenticated = computed(() => !!localStorage.getItem('token'))

// Zoom Logic
const isZooming = ref(false)
const lensStyle = ref({})
const zoomResultStyle = ref({})
const mobileZoomStyle = ref({})
const zoomContainer = ref(null)
const isMobile = ref(false)

const checkDevice = () => { isMobile.value = window.innerWidth < 1024 }

const fetchProduct = async () => {
  loading.value = true
  try {
    const res = await axios.get(`/api/products/${route.params.id}`)
    product.value = res.data.data
    selectedImage.value = product.value.image_url
  } catch (error) {
    if (error.response?.status === 404) router.push({ name: 'notfound' })
  } finally { loading.value = false }
}

/** * GESTION ZOOM BUREAU
 */
const handleZoom = (e) => {
  if (isMobile.value || !zoomContainer.value) return
  isZooming.value = true
  const rect = zoomContainer.value.getBoundingClientRect()
  const x = e.clientX - rect.left
  const y = e.clientY - rect.top
  const lensSize = 200

  let posX = x - lensSize / 2
  let posY = y - lensSize / 2
  posX = Math.max(0, Math.min(posX, rect.width - lensSize))
  posY = Math.max(0, Math.min(posY, rect.height - lensSize))

  lensStyle.value = { left: `${posX}px`, top: `${posY}px`, width: `${lensSize}px`, height: `${lensSize}px` }

  const ratioX = rect.width / lensSize
  const ratioY = rect.height / lensSize
  zoomResultStyle.value = {
    backgroundImage: `url(${selectedImage.value || product.value.image_url})`,
    backgroundPosition: `-${posX * ratioX}px -${posY * ratioY}px`,
    backgroundSize: `${rect.width * ratioX}px ${rect.height * ratioY}px`,
    backgroundRepeat: 'no-repeat'
  }
}

/** * GESTION ZOOM MOBILE (Tactile)
 */
const handleMobileZoomStart = () => { if (isMobile.value) isZooming.value = true }
const handleMobileZoomMove = (e) => {
  if (!isMobile.value || !zoomContainer.value) return
  const rect = zoomContainer.value.getBoundingClientRect()
  const touch = e.touches[0]
  const x = ((touch.clientX - rect.left) / rect.width) * 100
  const y = ((touch.clientY - rect.top) / rect.height) * 100
  mobileZoomStyle.value = { transformOrigin: `${x}% ${y}%`, transform: 'scale(2.5)' }
}

// Helpers
const hasDiscount = computed(() => product.value?.final_price < product.value?.price)
const calculateDiscount = (old, cur) => Math.round(((old - cur) / old) * 100)
const formatPrice = (p) => new Intl.NumberFormat('fr-FR').format(p)

const allImages = computed(() => {
  if (!product.value) return []
  const imgs = [product.value.image_url]
  if (product.value.images) {
    product.value.images.forEach(i => {
      const url = typeof i === 'object' ? i.url : i
      if (url && !imgs.includes(url)) imgs.push(url)
    })
  }
  return imgs.filter(Boolean)
})

const handleNewReview = (review) => {
  if (product.value) {
    if (!product.value.reviews) product.value.reviews = []
    product.value.reviews.unshift(review)
  }
}

watch(() => route.params.id, fetchProduct)
onMounted(() => {
  fetchProduct()
  checkDevice()
  window.addEventListener('resize', checkDevice)
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

/* Empêcher le défilement de la page pendant le zoom tactile sur mobile */
img { touch-action: none; }
</style>
