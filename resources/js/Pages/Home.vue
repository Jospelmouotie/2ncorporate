<template>
  <div class="w-full bg-gradient-to-b from-white to-gray-50 pb-20 font-sans overflow-x-hidden">
    <div v-if="searchStore.isSearching" class="fixed inset-0 bg-white z-50 pt-32 overflow-auto">
      <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8 sticky top-0 bg-white py-4 z-10">
          <div class="flex items-center gap-4">
            <div class="h-10 w-2 bg-gradient-to-b from-orange-500 to-orange-600 rounded-full"></div>
            <div>
              <h2 class="text-3xl md:text-5xl font-black text-gray-900 uppercase italic tracking-tighter">
                Résultats pour "<span class="text-orange-500">{{ searchStore.searchTerm }}</span>"
              </h2>
              <p class="text-gray-500 text-sm mt-2">{{ searchStore.results.length }} produit(s) trouvé(s)</p>
            </div>
          </div>
          <button
            @click="searchStore.clearSearch()"
            class="group flex items-center gap-3 font-black text-sm uppercase italic text-gray-400 hover:text-orange-500 transition-all"
          >
            Fermer
            <span class="w-10 h-10 rounded-full bg-gradient-to-r from-gray-100 to-gray-200 group-hover:from-orange-500 group-hover:to-orange-600 group-hover:text-white flex items-center justify-center transition-all text-lg shadow-sm">✕</span>
          </button>
        </div>

        <div v-if="searchStore.results.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6 pb-20">
          <div v-for="p in searchStore.results" :key="p.id" class="animate-fade-in">
            <ProductCard :product="p" />
          </div>
        </div>

        <div v-else class="text-center py-20 bg-gradient-to-br from-white to-gray-50 rounded-[3rem] shadow-sm border border-dashed border-gray-200">
          <div class="text-7xl mb-6">🔍</div>
          <h3 class="text-2xl font-black uppercase text-gray-400 mb-4">Aucun produit trouvé</h3>
          <p class="text-gray-500 max-w-md mx-auto mb-8">Essayez avec d'autres termes ou explorez nos catégories</p>
          <button @click="searchStore.clearSearch()" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-3 rounded-2xl font-black uppercase text-sm tracking-widest hover:shadow-lg transition-all">
            Retour à l'accueil
          </button>
        </div>
      </div>
    </div>

    <div v-else class="animate-fade-in">
      <div class="w-full px-4 pt-4 md:pt-12">
        <div class="flex flex-col lg:flex-row items-start relative min-h-fit lg:h-[850px] gap-8 lg:gap-0">
          <div class="w-full lg:w-[70%] h-[350px] md:h-[650px] z-10 shadow-2xl rounded-[2.5rem] md:rounded-[4rem] overflow-hidden border-4 border-white relative group">
            <Advertisement v-if="advertisements.length > 0" :ads="advertisements" />
            <div class="absolute inset-0 bg-gradient-to-tr from-black/20 via-transparent to-transparent pointer-events-none"></div>
          </div>

          <div class="w-full lg:w-[40%] lg:-ml-[15%] lg:mt-0 mt-[-40px] relative z-20 lg:top-[320px] px-2 md:px-0">
            <Transition name="flip-card" mode="out-in">
              <div v-if="promoStore.promotions.length > 0" :key="currentPromoIndex" class="w-full">
                <div class="w-full shadow-[0_30px_80px_rgba(249,115,22,0.25)] rounded-[2.5rem] md:rounded-[4rem] overflow-hidden border-[8px] md:border-[15px] border-white relative h-[500px] md:h-[600px] bg-gradient-to-br from-white to-gray-50">
                  <PromotionSide :promo="promoStore.promotions[currentPromoIndex]" />
                  <div class="absolute top-24 right-8 z-30 scale-90">
                    <Countdown :deadline="promoStore.promotions[currentPromoIndex].end_at" />
                  </div>
                </div>
              </div>
            </Transition>
          </div>

          <div class="mt-12 lg:mt-0 lg:absolute lg:bottom-12 lg:left-12 z-10 max-w-xl px-2 animate-slide-up">
            <div class="inline-flex items-center gap-3 mb-6">
              <div class="h-12 w-2 bg-gradient-to-b from-orange-500 to-orange-600 rounded-full"></div>
              <span class="text-sm font-black uppercase tracking-[0.3em] text-orange-500">Élite du Business</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-gray-900 leading-[0.85] mb-6 uppercase italic tracking-tighter">
              L'Excellence <span class="bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-transparent">2NCorporate</span>
            </h1>
            <p class="text-gray-600 text-lg md:text-xl font-medium max-w-md leading-relaxed">
              L'élite des tendances mondiales, livrée avec passion. Découvrez l'excellence dans chaque détail.
            </p>
            <router-link to="/shop" class="inline-flex items-center gap-3 mt-8 bg-gradient-to-r from-slate-900 to-gray-800 text-white px-8 py-4 rounded-2xl font-black uppercase text-sm tracking-widest hover:from-orange-600 hover:to-orange-700 transition-all shadow-xl hover:shadow-2xl animate-pulse">
              Explorer la boutique
              <span class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">→</span>
            </router-link>
          </div>
        </div>
      </div>

      <div class="mt-32 bg-gradient-to-b from-white to-gray-50 py-20 md:py-32 shadow-inner relative overflow-hidden">
        <div class="absolute inset-0 opacity-50"
     :style="{
       backgroundImage: `url('data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z\' fill=\'%23f97316\' fill-opacity=\'0.03\' fill-rule=\'evenodd\'/%3E%3C/svg%3E')`,
       backgroundRepeat: 'repeat',
       backgroundSize: '100px 100px'
     }">
</div>

        <div class="container mx-auto px-4 relative z-10">
          <div class="flex items-center justify-between mb-16">
            <div class="flex items-center gap-4">
              <div class="h-16 md:h-24 w-3 bg-gradient-to-b from-orange-500 to-red-500 rounded-full shadow-lg"></div>
              <h2 class="text-4xl md:text-7xl font-black text-gray-900 uppercase italic tracking-tighter">Nos <span class="bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-transparent">Univers</span></h2>
            </div>
            <router-link :to="{ name: 'shop' }" class="group flex items-center gap-3 text-orange-600 hover:text-orange-700 font-bold text-sm uppercase tracking-widest">
              Explorer tout
              <span class="w-10 h-10 rounded-full bg-gradient-to-r from-orange-100 to-orange-50 group-hover:from-orange-500 group-hover:to-orange-600 group-hover:text-white flex items-center justify-center transition-all shadow-sm group-hover:shadow-md">→</span>
            </router-link>
          </div>
          <CategorySection v-if="categories.length > 0" :categories="categories" />
        </div>
      </div>

      <section class="mt-20 bg-gradient-to-br from-white to-red-50 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-orange-500/10 to-red-500/10 rounded-full -translate-y-48 translate-x-48"></div>
        <div class="container mx-auto px-4 py-24 relative z-10">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16">
            <div>
              <div class="inline-flex items-center gap-3 mb-6">
                <div class="h-10 w-2 bg-gradient-to-b from-red-500 to-red-600 rounded-full"></div>
                <span class="text-sm font-black uppercase tracking-[0.3em] text-red-500 animate-pulse">⚠️ DERNIÈRE CHANCE</span>
              </div>
              <h2 class="text-5xl md:text-8xl font-black text-gray-900 italic uppercase leading-none tracking-tighter">
                Ventes <span class="bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">Flash</span>
              </h2>
              <p class="text-gray-600 text-lg mt-4 max-w-xl">Des offres exclusives qui s'épuisent rapidement</p>
            </div>
            <router-link :to="{ name: 'promotions' }" class="mt-6 md:mt-0 bg-gradient-to-r from-red-500 to-red-600 text-white px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:shadow-2xl hover:scale-105 transition-all shadow-lg">
              Voir toutes les promos
            </router-link>
          </div>

          <div class="relative">
            <div id="flash-sales-container" class="flex overflow-x-auto no-scrollbar pb-8 gap-8 snap-x snap-mandatory">
              <div v-for="promo in promoStore.promotions" :key="promo.id"
                   class="snap-center shrink-0 w-[300px] md:w-[380px] h-[450px] md:h-[550px] rounded-[2.5rem] overflow-hidden group shadow-2xl border-4 border-white relative cursor-pointer transition-transform hover:scale-[1.02]"
                   @click="$router.push({ name: 'product', params: { id: promo.product?.id } })">

                <div class="absolute inset-0 w-full h-full">
                  <img :src="promo.product?.image_url || '/images/placeholder.jpg'"
                       :alt="promo.product?.name"
                       class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>

                <div class="absolute top-6 left-6 bg-gradient-to-r from-red-500 to-red-600 text-white font-black px-5 py-2.5 rounded-xl text-lg shadow-lg animate-pulse z-20">
                  -{{ calculateDiscount(promo.product?.price, promo.sale_price) }}%
                </div>

                <div class="absolute bottom-0 left-0 right-0 p-8 z-20">
                  <h3 class="text-white font-black text-2xl uppercase italic mb-2 leading-tight drop-shadow-lg line-clamp-2">
                    {{ promo.product?.name }}
                  </h3>
                  <div class="flex items-end gap-3 mb-4">
                    <span class="text-red-300 font-black text-3xl tracking-tighter">{{ formatPrice(promo.sale_price) }} F</span>
                    <span class="text-gray-300 line-through text-sm font-bold mb-1">{{ formatPrice(promo.product?.price) }} F</span>
                  </div>
                  <div class="bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2 rounded-full w-fit">
                    <Countdown :deadline="promo.end_at" class="scale-75 origin-left text-white" />
                  </div>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-orange-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
              </div>
            </div>

            <div class="flex justify-center gap-2 mt-8">
              <div v-for="(promo, index) in promoStore.promotions.slice(0, 5)" :key="index"
                   class="w-2 h-2 rounded-full bg-gray-300 hover:bg-orange-500 transition-colors cursor-pointer"
                   @click="scrollToPromo(index)"></div>
            </div>
          </div>
        </div>
      </section>

      <section class="container mx-auto px-4 py-24">
        <div class="flex items-center justify-between mb-16">
          <div class="flex items-center gap-4">
            <div class="h-12 md:h-20 w-2 bg-gradient-to-b from-orange-500 to-orange-600 rounded-full shadow-lg"></div>
            <div>
              <h2 class="text-3xl md:text-6xl font-black text-gray-900 uppercase italic tracking-tighter">Nouveautés <span class="text-orange-500">Exclusives</span></h2>
              <p class="text-gray-500 text-sm mt-2">Découvrez les dernières arrivées dans notre collection</p>
            </div>
          </div>
          <router-link :to="{ name: 'shop', query: { sort: 'newest' } }" class="group flex items-center gap-3 font-black text-sm uppercase italic tracking-tighter text-orange-600 hover:text-orange-700">
            Voir toute la collection
            <span class="w-10 h-10 rounded-full bg-gradient-to-r from-orange-100 to-orange-50 group-hover:from-orange-500 group-hover:to-orange-600 group-hover:text-white flex items-center justify-center group-hover:translate-x-2 transition-all shadow-sm">→</span>
          </router-link>
        </div>

        <div v-if="latestProducts.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
          <div v-for="p in latestProducts" :key="p.id" class="reveal">
            <ProductCard :product="p" />
          </div>
        </div>
        <div v-else class="text-center py-20 bg-gradient-to-br from-white to-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200">
          <div class="text-6xl mb-6 animate-pulse">🔄</div>
          <h3 class="text-xl font-black uppercase text-gray-400 mb-4">Chargement des nouveautés...</h3>
          <p class="text-gray-500">Notre catalogue se met à jour</p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue'
import axios from 'axios'
import { usePromotionStore } from '@/stores/promotionStore'
import { useSearchStore } from '@/stores/searchStore'

// Components
import PromotionSide from '@/Components/promo/PromotionSide.vue'
import ProductCard from '@/Components/product/ProductCard.vue'
import CategorySection from '@/Components/catalog/CategorySection.vue'
import Advertisement from './Advertisement.vue'
import Countdown from '@/Components/promo/Countdown.vue'

const promoStore = usePromotionStore()
const searchStore = useSearchStore()

const advertisements = ref([])
const latestProducts = ref([])
const categories = ref([])
const currentPromoIndex = ref(0)
const windowWidth = ref(window.innerWidth)
const currentUser = ref(window.user || null)
let rotationTimer = null

const formatPrice = (val) => {
  if (!val) return '0'
  return new Intl.NumberFormat('fr-FR').format(val)
}

const calculateDiscount = (old, current) => {
  if (!old || !current) return 0
  return Math.round(((old - current) / old) * 100)
}

const scrollToPromo = (index) => {
  const container = document.getElementById('flash-sales-container')
  if (container) {
    const promoWidth = 300 // Largeur de base
    const gap = 32
    container.scrollTo({ left: index * (promoWidth + gap), behavior: 'smooth' })
  }
}

const observeScroll = () => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in-up')
        observer.unobserve(entry.target)
      }
    })
  }, { threshold: 0.1 })

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el))
}

const fetchData = async () => {
  try {
    const [resAds, resPrd, resCats] = await Promise.all([
      axios.get('/api/ads'),
      axios.get('/api/products?limit=10&sort=newest'),
      axios.get('/api/categories')
    ])

    advertisements.value = resAds.data?.data || resAds.data || []
    latestProducts.value = resPrd.data?.data || resPrd.data || []
    categories.value = resCats.data?.data || resCats.data || []

    await promoStore.fetchPromotions()

    if (promoStore.promotions.length > 0) {
      rotationTimer = setInterval(() => {
        currentPromoIndex.value = (currentPromoIndex.value + 1) % promoStore.promotions.length
      }, 8000)
    }

    await nextTick()
    observeScroll()
  } catch (e) {
    console.error("Erreur chargement Home:", e)
  }
}

const syncUser = () => {
  currentUser.value = window.user
}

onMounted(() => {
  fetchData()
  window.addEventListener('resize', () => {
    windowWidth.value = window.innerWidth
  })
  window.addEventListener('user-updated', syncUser)
})

onUnmounted(() => {
  if (rotationTimer) clearInterval(rotationTimer)
  window.removeEventListener('user-updated', syncUser)
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Animations */
.flip-card-enter-active { animation: flip-in 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
.flip-card-leave-active { animation: flip-out 0.8s cubic-bezier(0.4, 0, 0.2, 1); }

@keyframes flip-in {
  0% { opacity: 0; transform: rotateY(90deg) scale(0.8); }
  100% { opacity: 1; transform: rotateY(0) scale(1); }
}
@keyframes flip-out {
  0% { opacity: 1; transform: rotateY(0) scale(1); }
  100% { opacity: 0; transform: rotateY(-90deg) scale(0.8); }
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes slide-up {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in { animation: fade-in 0.6s ease-out forwards; }
.animate-fade-in-up { animation: fade-in-up 0.8s ease-out forwards; }
.animate-slide-up { animation: slide-up 1s ease-out forwards; }

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.reveal { opacity: 0; }
</style>
