<template>
  <div class="w-full bg-gray-50 pb-20 font-sans overflow-x-hidden">

    <div v-if="searchStore.isSearching" class="container mx-auto px-4 pt-32 min-h-screen">
      <div class="flex items-center justify-between mb-12">
        <div class="flex items-center gap-4">
          <div class="h-12 w-3 bg-orange-500 rounded-full"></div>
          <h2 class="text-4xl md:text-6xl font-black text-gray-900 uppercase italic">
            Résultats <span class="text-orange-500">({{ searchStore.results.length }})</span>
          </h2>
        </div>
        <button
          @click="searchStore.clearSearch()"
          class="group flex items-center gap-2 font-black text-sm uppercase italic text-gray-400 hover:text-orange-500 transition-colors"
        >
          Fermer la recherche
          <span class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-orange-500 group-hover:text-white flex items-center justify-center transition-all">✕</span>
        </button>
      </div>

      <div v-if="searchStore.results.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
        <div v-for="p in searchStore.results" :key="p.id" class="reveal-visible">
          <ProductCard :product="p" />
        </div>
      </div>

      <div v-else class="text-center py-20 bg-white rounded-[3rem] shadow-sm border border-dashed border-gray-200">
        <div class="text-6xl mb-4">🔍</div>
        <h3 class="text-xl font-black uppercase text-gray-400">Aucun produit ne correspond à votre recherche</h3>
      </div>
    </div>

    <div v-else>
      <div class="w-full px-4 pt-4 md:pt-12">
        <div class="flex flex-col lg:flex-row items-start relative min-h-fit lg:h-[850px]">

          <div class="w-full lg:w-[80%] h-[350px] md:h-[650px] z-10 shadow-2xl rounded-[2.5rem] md:rounded-[3.5rem] overflow-hidden border-4 border-white">
            <Advertisement v-if="advertisements.length > 0" :ads="advertisements" />
          </div>

          <div class="w-full lg:w-[40%] lg:-ml-[12%] lg:mt-0 mt-[-60px] relative z-20 lg:top-[280px] px-2 md:px-0">
            <Transition name="swap-card" mode="out-in">
              <div v-if="promoStore.promotions.length > 0" :key="currentPromoIndex" class="w-full">
                <div class="w-full shadow-[0_20px_60px_rgba(0,0,0,0.15)] rounded-[2.5rem] md:rounded-[4rem] overflow-hidden border-[8px] md:border-[15px] border-white relative h-[500px] md:h-[600px]">
                  <PromotionSide :promo="promoStore.promotions[currentPromoIndex]" />
                  <div class="absolute top-20 right-8 z-30 scale-90">
                    <Countdown :deadline="promoStore.promotions[currentPromoIndex].end_at" />
                  </div>
                </div>
              </div>
            </Transition>
          </div>

          <div class="mt-16 lg:mt-0 lg:absolute lg:bottom-10 lg:left-8 z-10 max-w-xl px-2">
            <h1 class="text-4xl md:text-6xl font-black text-gray-900 leading-[0.85] mb-4 uppercase italic">
              Bienvenue chez <span class="text-orange-500">2ncorporate</span>
            </h1>
            <p class="text-gray-500 text-sm md:text-lg font-medium max-w-xs">
              L'élite des tendances mondiales, livrée avec passion.
            </p>
          </div>
        </div>
      </div>

      <div class="mt-20 md:mt-40 bg-white py-12 md:py-24 shadow-inner">
        <div class="container mx-auto px-4">
          <div class="flex items-center justify-between mb-12">
            <div class="flex items-center gap-4">
              <div class="h-12 md:h-20 w-3 bg-orange-500 rounded-full"></div>
              <h2 class="text-4xl md:text-7xl font-black text-gray-900 uppercase italic tracking-tighter">Nos <span class="text-orange-500">Univers</span></h2>
            </div>
            <router-link :to="{ name: 'shop' }" class="text-orange-500 font-bold hover:underline hidden md:block">Voir tout le catalogue</router-link>
          </div>
          <CategorySection v-if="categories.length > 0" :categories="categories" />
        </div>
      </div>

      <section class="mt-20">
        <div class="bg-white py-16 overflow-hidden">
          <div class="container mx-auto px-4 mb-10 flex justify-between items-end">
            <h2 class="text-5xl md:text-8xl font-black text-gray-900 italic uppercase leading-none">Ventes <span class="text-red-600">Flash</span></h2>
            <router-link :to="{ name: 'shop', query: { onSale: true } }" class="bg-black text-white px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-500 transition-all">Tout voir</router-link>
          </div>

          <div class="relative flex overflow-x-auto lg:overflow-hidden no-scrollbar px-4 gap-6">
            <div :class="{'flex animate-marquee pause-on-hover': isDesktop, 'flex gap-6': !isDesktop}">
              <div v-for="promo in promoStore.promotions" :key="promo.id"
                   class="relative w-[300px] md:w-[380px] h-[450px] md:h-[550px] shrink-0 rounded-[2.5rem] overflow-hidden group shadow-2xl border-4 border-white mx-2 cursor-pointer"
                   @click="$router.push({ name: 'product', params: { id: promo.product.id } })">
                <div class="absolute inset-0 w-full h-full">
                  <Transition name="fade" mode="out-in">
                    <img :key="imageRotationIndex" :src="getRotatingImage(promo)" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                  </Transition>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute top-6 left-6 bg-red-600 text-white font-black px-4 py-2 rounded-xl text-lg animate-pulse z-20">-{{ calculateDiscount(promo.product.price, promo.sale_price) }}%</div>
                <div class="absolute bottom-0 left-0 right-0 p-8 z-20">
                  <h3 class="text-white font-black text-2xl uppercase italic mb-2 leading-none drop-shadow-lg">{{ promo.product.name }}</h3>
                  <div class="flex items-end gap-3 mb-4">
                    <span class="text-red-500 font-black text-3xl tracking-tighter">{{ formatPrice(promo.sale_price) }} F</span>
                    <span class="text-gray-300 line-through text-sm font-bold mb-1">{{ formatPrice(promo.product.price) }} F</span>
                  </div>
                  <div class="bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2 rounded-full w-fit">
                     <Countdown :deadline="promo.end_at" class="scale-75 origin-left text-white" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="container mx-auto px-2 md:px-4 py-20">
        <div class="reveal flex items-center justify-between mb-12">
          <div class="flex items-center gap-4">
            <div class="h-10 md:h-16 w-2 md:w-3 bg-orange-500 rounded-full"></div>
            <h2 class="text-3xl md:text-7xl font-black text-gray-900 uppercase italic">Nouveautés</h2>
          </div>
          <router-link :to="{ name: 'shop' }" class="group flex items-center gap-2 font-black text-sm uppercase italic tracking-tighter">
            Accéder à la boutique
            <span class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center group-hover:translate-x-2 transition-transform">→</span>
          </router-link>
        </div>

        <div v-if="latestProducts.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2 md:gap-4">
          <div v-for="p in latestProducts" :key="p.id" class="reveal">
            <ProductCard :product="p" />
          </div>
        </div>
        <div v-else class="text-center py-10 text-gray-400 font-bold uppercase tracking-widest animate-pulse">
          Mise à jour du catalogue...
        </div>
      </section>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue'
import axios from 'axios'
import { usePromotionStore } from '@/stores/promotionStore'
import { useSearchStore } from '@/stores/searchStore' // Store pour l'autocomplete

// Components
import PromotionSide from '@/Components/promo/PromotionSide.vue'
import ProductCard from '@/Components/product/ProductCard.vue'
import CategorySection from '@/Components/catalog/CategorySection.vue'
import Advertisement from './Advertisement.vue'
import Countdown from '@/Components/promo/Countdown.vue'

const promoStore = usePromotionStore()
const searchStore = useSearchStore() // Utilisation du store de recherche

const advertisements = ref([])
const latestProducts = ref([])
const categories = ref([])
const currentPromoIndex = ref(0)
const imageRotationIndex = ref(0)
const windowWidth = ref(window.innerWidth)
let rotationTimer = null
let imageTimer = null

const isDesktop = computed(() => windowWidth.value >= 1024)
const formatPrice = (val) => new Intl.NumberFormat('fr-FR').format(val)
const calculateDiscount = (old, current) => Math.round(((old - current) / old) * 100)

const getRotatingImage = (promo) => {
  const imgs = promo.product.images || []
  if (imgs.length === 0) return promo.product.image_url
  return imgs[imageRotationIndex.value % imgs.length].url || imgs[imageRotationIndex.value % imgs.length]
}

const observeScroll = () => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('reveal-visible')
      }
    })
  }, { threshold: 0.1 })
  document.querySelectorAll('.reveal').forEach(el => observer.observe(el))
}

const fetchData = async () => {
  try {
    const [resAds, resPrd, resCats] = await Promise.all([
      axios.get('/api/ads'),
      axios.get('/api/products?limit=15'),
      axios.get('/api/categories')
    ])

    advertisements.value = resAds.data?.data || []
    latestProducts.value = resPrd.data?.data || []
    categories.value = resCats.data?.data || []

    await promoStore.fetchPromotions()

    // Timer Rotation Promotions
    rotationTimer = setInterval(() => {
      if(promoStore.promotions.length > 0) {
        currentPromoIndex.value = (currentPromoIndex.value + 1) % promoStore.promotions.length
      }
    }, 6000)

    // Timer Rotation Images Produits
    imageTimer = setInterval(() => {
      imageRotationIndex.value++
    }, 3000)

    await nextTick()
    observeScroll()
  } catch (e) {
    console.error("Erreur chargement Home:", e)
  }
}

onMounted(() => {
  fetchData()
  window.addEventListener('resize', () => windowWidth.value = window.innerWidth)
})

onUnmounted(() => {
  if (rotationTimer) clearInterval(rotationTimer)
  if (imageTimer) clearInterval(imageTimer)
})
</script>

<style scoped>
/* Scrollbars */
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Animation Card Promo */
.swap-card-enter-active, .swap-card-leave-active { transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
.swap-card-enter-from { opacity: 0; transform: translateY(40px) scale(0.95); }
.swap-card-leave-to { opacity: 0; transform: translateY(-40px) scale(1.05); }

/* Fade Images */
.fade-enter-active, .fade-leave-active { transition: opacity 1s ease-in-out; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Marquee Ventes Flash */
.animate-marquee { display: flex; animation: marquee 45s linear infinite; width: max-content; }
.pause-on-hover:hover { animation-play-state: paused; }
@keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

/* Reveal Scroll */
.reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
.reveal-visible { opacity: 1 !important; transform: translateY(0) !important; }
</style>
