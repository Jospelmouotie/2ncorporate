<template>
  <div class="min-h-screen bg-white py-8 px-4 md:px-10 mt-16 md:mt-24">
    <div v-if="loading" class="max-w-[1400px] mx-auto animate-pulse grid grid-cols-1 lg:grid-cols-12 gap-10">
      <div class="lg:col-span-5 h-[500px] bg-slate-50 rounded-3xl"></div>
      <div class="lg:col-span-7 space-y-6">
        <div class="h-12 bg-slate-50 w-3/4 rounded-lg"></div>
        <div class="h-32 bg-slate-50 w-full rounded-lg"></div>
      </div>
    </div>

    <div v-else-if="product" class="max-w-[1400px] mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">

        <div class="lg:col-span-5 lg:sticky lg:top-32 z-40">
          <div
            class="relative aspect-square rounded-3xl overflow-hidden bg-white border border-slate-100 group cursor-zoom-in shadow-sm"
            @mousemove="handleZoom"
            @mouseleave="isZooming = false"
            ref="zoomContainer"
          >
            <img
              :src="selectedImage || product.image_url"
              class="w-full h-full object-contain p-4 transition-opacity duration-200"
              :class="{'opacity-0': isZooming && !isMobile}"
            />

            <div v-if="isZooming && !isMobile"
                 class="absolute border border-slate-300 bg-white/20 pointer-events-none z-10 shadow-xl"
                 :style="lensStyle"></div>

            <div v-if="hasDiscount" class="absolute top-4 left-4 bg-orange-600 text-white px-4 py-1 rounded-lg font-black text-[10px] uppercase italic z-20">
              -{{ calculateDiscount(product.price, product.final_price) }}%
            </div>
          </div>

          <div v-if="allImages.length > 1" class="flex gap-3 mt-6 overflow-x-auto pb-2 scrollbar-hide">
            <button v-for="(img, idx) in allImages" :key="idx"
              @mouseenter="selectedImage = img"
              @click="selectedImage = img"
              class="w-20 h-20 rounded-xl border-2 transition-all flex-shrink-0 bg-white overflow-hidden"
              :class="selectedImage === img ? 'border-orange-500 shadow-md scale-105' : 'border-slate-100 opacity-60 hover:opacity-100'">
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <div class="lg:col-span-7 space-y-8 relative">
          <div
            v-if="isZooming && !isMobile"
            class="absolute inset-0 z-[100] bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden pointer-events-none"
            :style="zoomResultStyle"
          ></div>

          <div class="space-y-4">
            <h1 class="text-4xl md:text-6xl font-black text-slate-900 leading-[0.9] uppercase italic tracking-tighter">
              {{ product.name }}
            </h1>

            <div class="flex items-center gap-4">
              <div class="flex items-center text-orange-400">
                <Star v-for="i in 5" :key="i" :class="i <= Math.round(product.avg_rating || 4) ? 'fill-orange-400' : 'text-slate-200'" class="w-5 h-5" />
                <span class="ml-2 text-slate-900 font-black text-sm">{{ product.avg_rating || '4.0' }}</span>
              </div>
              <span class="text-slate-200">|</span>
              <span class="text-blue-600 hover:underline cursor-pointer text-xs font-bold uppercase">{{ product.reviews_count || 0 }} avis clients</span>
            </div>

            <hr class="border-slate-100" />

            <div class="flex items-baseline gap-4">
              <span class="text-5xl font-black text-orange-600 tracking-tighter italic">
                {{ formatPrice(product.final_price || product.price) }} <small class="text-2xl not-italic">F</small>
              </span>
              <span v-if="hasDiscount" class="text-2xl text-slate-300 line-through font-bold">
                {{ formatPrice(product.price) }} F
              </span>
            </div>

            <div class="bg-slate-50 p-6 rounded-[2rem] border-l-4 border-orange-500">
                <p class="text-slate-600 italic leading-relaxed">{{ product.description }}</p>
            </div>
          </div>

          <div class="p-8 bg-white rounded-[2.5rem] border-2 border-slate-100 shadow-xl space-y-6">
             <div class="flex items-center justify-between">
                <span class="font-black text-slate-900 uppercase text-xs tracking-widest">Disponibilité :</span>
                <span class="text-green-600 font-black uppercase text-[10px] flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span> En Stock (Cameroun)
                </span>
             </div>

             <div class="flex items-center justify-between bg-slate-50 p-2 rounded-2xl">
                <span class="ml-4 font-black text-slate-900 text-xs uppercase tracking-widest">Quantité</span>
                <div class="flex items-center bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                  <button @click="qty > 1 ? qty-- : null" class="w-12 h-12 hover:bg-orange-50 text-xl font-black">−</button>
                  <span class="w-12 text-center font-black">{{ qty }}</span>
                  <button @click="qty++" class="w-12 h-12 hover:bg-orange-50 text-xl font-black">+</button>
                </div>
             </div>

             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button @click="handleAddToCart" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-orange-600 transition-all shadow-lg active:scale-95 flex items-center justify-center gap-3">
                  <ShoppingCart class="w-5 h-5" /> Ajouter au panier
                </button>
                <button @click="handleAddToWishlist" class="w-full border-2 border-slate-200 text-slate-900 py-5 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-slate-50 transition-all active:scale-95 flex items-center justify-center gap-3">
                  <Heart class="w-5 h-5" /> Favoris
                </button>
             </div>
          </div>
        </div>
      </div>

      <div class="mt-32 border-t border-slate-100 pt-20">
        <h2 class="text-4xl font-black text-slate-900 uppercase italic mb-12 tracking-tighter">
            Expériences <span class="text-orange-500">Clients</span>
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
          <div class="lg:col-span-4 space-y-8">
            <div class="bg-slate-50 p-8 rounded-[2.5rem]">
                <div class="flex items-center gap-4 mb-6">
                    <span class="text-7xl font-black text-slate-900">{{ product.avg_rating || '4.0' }}</span>
                    <div class="flex flex-col text-orange-400">
                        <div class="flex"><Star v-for="i in 5" :key="i" class="w-5 h-5 fill-current" /></div>
                        <span class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-1">Sur {{ product.reviews?.length || 0 }} avis</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div v-for="i in [5,4,3,2,1]" :key="i" class="flex items-center gap-4 text-[9px] font-black uppercase text-slate-500">
                        <span class="w-14">{{ i }} étoiles</span>
                        <div class="flex-1 h-2.5 bg-white rounded-full overflow-hidden border border-slate-100">
                            <div class="bg-orange-500 h-full rounded-full" :style="{width: (i === 5 ? 80 : 15) + '%'}"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION AVIS - Affiche selon le statut de connexion -->
            <div v-if="isAuthenticated" class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl">
              <h4 class="font-black italic uppercase text-xs mb-6 tracking-widest text-orange-500">Donnez votre avis</h4>
              <div class="flex gap-2 mb-6">
                <Star v-for="i in 5" :key="i"
                      @click="reviewData.rating = i"
                      :class="i <= reviewData.rating ? 'fill-orange-500 text-orange-500' : 'text-slate-700'"
                      class="w-10 h-10 cursor-pointer hover:scale-110 transition-transform" />
              </div>
              <textarea v-model="reviewData.comment"
                        class="w-full bg-slate-800 border-none rounded-2xl p-5 text-sm focus:ring-2 focus:ring-orange-500 transition-all placeholder:text-slate-500"
                        rows="4"
                        placeholder="Racontez votre expérience..."></textarea>
              <button @click="submitReview" class="mt-6 w-full bg-orange-500 py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.3em] hover:bg-white hover:text-slate-900 transition-all">
                Publier
              </button>
            </div>

            <div v-else class="p-8 bg-orange-50 rounded-[2.5rem] border-2 border-orange-100 text-center">
              <p class="text-slate-700 font-bold italic mb-6">Seuls les membres 2Ncorporate peuvent laisser un avis.</p>
              <router-link to="/login" class="inline-block bg-slate-900 text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-orange-600 transition-colors">
                Se connecter
              </router-link>
              <p class="mt-4 text-slate-600 text-sm">
                Pas encore membre ?
                <router-link to="/register" class="text-orange-600 font-bold hover:underline">
                  Créer un compte
                </router-link>
              </p>
            </div>
          </div>

          <div class="lg:col-span-8 space-y-10">
            <div v-if="product.reviews && product.reviews.length > 0">
                <div v-for="review in product.reviews" :key="review.id" class="border-b border-slate-100 pb-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-black text-slate-400 border border-slate-200 uppercase">
                                {{ review.user?.name?.charAt(0) || 'U' }}
                            </div>
                            <div>
                                <h5 class="font-black text-slate-900 uppercase text-[10px] tracking-widest">{{ review.user?.name || 'Utilisateur' }}</h5>
                                <div class="flex text-orange-400 mt-1">
                                  <Star v-for="i in 5" :key="i"
                                        :class="i <= review.rating ? 'fill-current' : 'text-slate-200'"
                                        class="w-3 h-3" />
                                </div>
                                <p class="text-xs text-slate-400 mt-1">
                                  {{ formatDate(review.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-slate-600 italic leading-relaxed text-lg font-medium">"{{ review.comment }}"</p>
                </div>
            </div>
            <div v-else class="text-center py-20 bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
                <p class="text-slate-400 font-black uppercase italic tracking-widest">Soyez le premier à noter ce produit</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de succès pour avis -->
    <div v-if="reviewSuccess" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[1000] p-4">
      <div class="bg-white rounded-3xl p-8 max-w-md w-full text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <CheckCircle class="w-8 h-8 text-green-600" />
        </div>
        <h3 class="text-2xl font-black text-slate-900 mb-4">Merci pour votre avis !</h3>
        <p class="text-slate-600 mb-6">Votre évaluation a été enregistrée et sera publiée après modération.</p>
        <button @click="reviewSuccess = false" class="bg-orange-500 text-white px-8 py-3 rounded-2xl font-bold hover:bg-orange-600 transition-colors">
          Continuer mes achats
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { Heart, ShoppingCart, Star, CheckCircle } from 'lucide-vue-next'
import { useCartStore } from '@/stores/cartStore'
import { useWishlistStore } from '@/stores/wishlistStore'
import { useModalStore } from '@/stores/modalStore'

const route = useRoute()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const modal = useModalStore()

// Gestion de l'authentification
const isAuthenticated = computed(() => {
  // Vérifie plusieurs sources possibles de données utilisateur
  return !!window.user ||
         !!window.Laravel?.user ||
         !!localStorage.getItem('user_token')
})

const user = computed(() => {
  // Récupère l'utilisateur depuis différentes sources
  if (window.user) return window.user
  if (window.Laravel?.user) return window.Laravel.user

  // Essaye de récupérer depuis localStorage
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      return JSON.parse(storedUser)
    } catch (e) {
      return null
    }
  }

  return null
})

const product = ref(null)
const loading = ref(true)
const selectedImage = ref(null)
const qty = ref(1)
const reviewData = reactive({ rating: 5, comment: '' })
const reviewSuccess = ref(false)

// --- ZOOM LOGIC ---
const isZooming = ref(false)
const zoomContainer = ref(null)
const lensStyle = ref({})
const zoomResultStyle = ref({})
const isMobile = ref(false)

const handleZoom = (e) => {
  if (isMobile.value || !zoomContainer.value) return
  isZooming.value = true
  const rect = zoomContainer.value.getBoundingClientRect()
  const x = e.clientX - rect.left
  const y = e.clientY - rect.top
  const lensSize = 150
  let posX = Math.max(0, Math.min(x - lensSize / 2, rect.width - lensSize))
  let posY = Math.max(0, Math.min(y - lensSize / 2, rect.height - lensSize))

  lensStyle.value = { left: `${posX}px`, top: `${posY}px`, width: `${lensSize}px`, height: `${lensSize}px` }

  const ratio = 2.5
  zoomResultStyle.value = {
    backgroundImage: `url(${selectedImage.value || product.value.image_url})`,
    backgroundPosition: `-${posX * ratio}px -${posY * ratio}px`,
    backgroundSize: `${rect.width * ratio}px ${rect.height * ratio}px`,
    backgroundRepeat: 'no-repeat'
  }
}

// --- API ACTIONS ---
const fetchProduct = async () => {
  try {
    const res = await axios.get(`/api/products/${route.params.id}`)
    product.value = res.data.data
    selectedImage.value = product.value.image_url
  } catch (error) {
    console.error('Error fetching product:', error)
  } finally {
    loading.value = false
  }
}

const handleAddToCart = async () => {
  if (!isAuthenticated.value) {
    // Rediriger vers login ou afficher modal
    modal.open('Connectez-vous pour ajouter au panier', null, 'info', {
      action: 'login',
      redirectTo: route.fullPath
    })
    return
  }

  const success = await cartStore.addToCart(product.value.id, qty.value)
  if (success) modal.open(`Ajouté ! ${product.value.name} est au panier.`, product.value, 'cart')
}

const handleAddToWishlist = async () => {
  if (!isAuthenticated.value) {
    modal.open('Connectez-vous pour ajouter aux favoris', null, 'info', {
      action: 'login',
      redirectTo: route.fullPath
    })
    return
  }

  const success = await wishlistStore.addToWishlist(product.value.id)
  if (success) modal.open("Ajouté aux favoris !", product.value, 'wishlist')
}

const submitReview = async () => {
  if (!isAuthenticated.value) {
    modal.open('Connectez-vous pour laisser un avis', null, 'info', {
      action: 'login',
      redirectTo: route.fullPath
    })
    return
  }

  if (!reviewData.comment.trim()) {
    modal.open('Veuillez saisir un commentaire', null, 'error')
    return
  }

  try {
    const response = await axios.post(`/api/products/${product.value.id}/reviews`, reviewData)

    if (response.data.success) {
      reviewSuccess.value = true
      reviewData.comment = ''
      reviewData.rating = 5

      // Rafraîchir les avis
      fetchProduct()

      // Fermer automatiquement après 3 secondes
      setTimeout(() => {
        reviewSuccess.value = false
      }, 3000)
    }
  } catch (error) {
    console.error('Error submitting review:', error)
    if (error.response?.status === 401) {
      modal.open('Session expirée. Veuillez vous reconnecter.', null, 'error', {
        action: 'login',
        redirectTo: route.fullPath
      })
    } else {
      modal.open(
        error.response?.data?.message || 'Une erreur est survenue',
        null,
        'error'
      )
    }
  }
}

// --- HELPERS ---
const formatPrice = (price) => {
  if (!price) return '0'
  return new Intl.NumberFormat('fr-FR').format(price)
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const hasDiscount = computed(() => {
  return product.value &&
         product.value.final_price &&
         product.value.price &&
         product.value.final_price < product.value.price
})

const calculateDiscount = (oldPrice, currentPrice) => {
  if (!oldPrice || !currentPrice) return 0
  return Math.round(((oldPrice - currentPrice) / oldPrice) * 100)
}

const allImages = computed(() => {
  if (!product.value) return []
  const imgs = [product.value.image_url]

  // Ajouter les images supplémentaires
  if (product.value.images) {
    product.value.images.forEach(img => {
      if (typeof img === 'object' && img.url) {
        imgs.push(img.url)
      } else if (typeof img === 'string') {
        imgs.push(img)
      }
    })
  }

  // Éliminer les doublons et les valeurs nulles
  return [...new Set(imgs.filter(img => img))]
})

const checkMobile = () => {
  isMobile.value = window.innerWidth < 1024
}

// Récupérer l'utilisateur au montage si besoin
const fetchUserIfNeeded = async () => {
  if (!window.user && !window.Laravel?.user) {
    try {
      const response = await axios.get('/api/user')
      window.user = response.data
    } catch (error) {
      // Pas connecté, c'est normal
    }
  }
}

onMounted(async () => {
  await fetchUserIfNeeded()
  await fetchProduct()
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Animation pour le bouton */
@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.pulse-animation {
  animation: pulse 2s infinite;
}
</style>
