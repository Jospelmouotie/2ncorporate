<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { Heart, ShoppingCart, Star } from 'lucide-vue-next' // Ajout de Star
import { useModalStore } from '@/stores/modalStore'
import { useCartStore } from '@/stores/cartStore'
import { useWishlistStore } from '@/stores/wishlistStore'

const props = defineProps({ product: Object })
const router = useRouter()
const modal = useModalStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

// 1. NAVIGATION
const goToProduct = () => {
  router.push({ name: 'product.detail', params: { id: props.product.id } })
}

// 2. GESTION DES IMAGES (Slider)
const currentImgIndex = ref(0)
const allImages = computed(() => {
  const imgs = []
  if (props.product.image_url) imgs.push(props.product.image_url)
  if (props.product.images && props.product.images.length > 0) {
    props.product.images.forEach(img => {
      const url = typeof img === 'object' ? img.url : img
      if (url !== props.product.image_url) imgs.push(url)
    })
  }
  return imgs.length > 0 ? imgs : ['/storage/placeholder.png']
})

const nextImage = () => {
  if (allImages.value.length > 1) {
    currentImgIndex.value = (currentImgIndex.value + 1) % allImages.value.length
  }
}

// 3. CALCULS (Prix & Étoiles)
const formatPrice = (val) => new Intl.NumberFormat('fr-FR').format(val)
const averageRating = computed(() => Number(props.product.reviews_avg_rating) || 0)

// 4. FAVORIS
const isFavorite = computed(() => {
  return wishlistStore.items.some(item => item.id === props.product.id)
})

// 5. ACTIONS
const handleAddToCart = async () => {
  const success = await cartStore.addToCart(props.product.id, 1)
  if (success) {
    modal.open("Produit ajouté au panier !", props.product, 'cart')
  }
}

const handleAddToWishlist = async () => {
  if (isFavorite.value) {
    await wishlistStore.removeFromWishlist(props.product.id)
  } else {
    await wishlistStore.addToWishlist(props.product.id)
    modal.open("Ajouté à vos favoris !", props.product, 'wishlist')
  }
}

// 6. AUTO-SLIDER
let interval = null
onMounted(() => {
  if (allImages.value.length > 1) {
    interval = setInterval(nextImage, Math.random() * (5000 - 3000) + 3000)
  }
})
onUnmounted(() => clearInterval(interval))
</script>
<template>
  <div
    class="group relative bg-white rounded-xl md:rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-100 h-[300px] md:h-[400px] w-full flex flex-col cursor-pointer"
    @mouseenter="nextImage"
    @click="goToProduct"
  >
    <div class="absolute inset-0 w-full h-full bg-gray-100">
      <Transition name="fade-slide">
        <img
          :key="currentImgIndex"
          :src="allImages[currentImgIndex]"
          class="w-full h-full object-cover"
        />
      </Transition>
      <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
    </div>

    <div class="relative h-full w-full p-4 md:p-6 flex flex-col justify-between z-10">

      <div class="flex justify-between items-start">
        <div
          v-if="product.discount_info"
          class="bg-red-600 text-white font-black px-3 py-1.5 rounded-xl text-[10px] md:text-xs uppercase italic shadow-lg animate-pulse-badge"
        >
          {{ product.discount_info.label }}
        </div>
        <div v-else></div>

        <div class="flex flex-col gap-2
                    translate-x-0 md:translate-x-16 md:group-hover:translate-x-0
                    transition-transform duration-500">

          <button
            @click.stop="handleAddToWishlist"
            :class="isFavorite ? 'bg-red-500 text-white' : 'bg-white/20 backdrop-blur-md text-white border border-white/20 md:border-white/10'"
            class="p-2.5 md:p-3 rounded-xl shadow-xl transition-all hover:scale-110 active:scale-90"
          >
            <Heart class="h-5 w-5" :fill="isFavorite ? 'white' : 'none'" />
          </button>

          <button
            @click.stop="handleAddToCart"
            class="p-2.5 md:p-3 bg-orange-500 text-white rounded-xl shadow-xl hover:bg-orange-600 active:scale-90 transition-all"
          >
            <ShoppingCart class="h-5 w-5" />
          </button>
        </div>
      </div>

      <div class="space-y-2">
        <div v-if="averageRating > 0" class="flex items-center gap-1.5">
           <div class="flex gap-0.5">
             <Star v-for="i in 5" :key="i" class="h-3 w-3 md:h-4 md:w-4" :class="i <= Math.round(averageRating) ? 'fill-orange-400 text-orange-400' : 'text-gray-500 opacity-40'" />
           </div>
           <span class="text-[10px] md:text-xs font-black text-orange-400 italic">{{ averageRating.toFixed(1) }}</span>
        </div>

        <h3 class="text-white font-bold uppercase text-[12px] md:text-base leading-tight drop-shadow-lg tracking-tight line-clamp-2">
          {{ product.name }}
        </h3>

        <div class="flex flex-col">
          <span v-if="product.discount_info" class="text-gray-400 text-[10px] md:text-xs line-through font-bold opacity-90">
            {{ formatPrice(product.price) }} F
          </span>
          <div class="flex items-baseline gap-1">
            <span class="text-white text-2xl md:text-4xl font-black tracking-tighter italic glow-text">
              {{ formatPrice(product.final_price) }}
            </span>
            <span class="text-[10px] md:text-xs font-black text-orange-400 uppercase">FCFA</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation de transition d'image */
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: opacity 1s, transform 1.5s ease-out;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: scale(1.2) rotate(2deg);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: scale(1);
}

/* Badge animé */
@keyframes pulse-badge {
  0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
  50% { transform: scale(1.05); box-shadow: 0 0 15px 5px rgba(220, 38, 38, 0); }
}
.animate-pulse-badge {
  animation: pulse-badge 2s infinite ease-in-out;
}

/* Effets de texte Premium */
.drop-shadow-lg {
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.9));
}

.glow-text {
  text-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
}

/* Masquer la scrollbar pour les vignettes si nécessaire */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
