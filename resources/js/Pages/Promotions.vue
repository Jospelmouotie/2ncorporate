<template>
  <div class="container mx-auto p-4 min-h-screen mt-20">
    <h1
      class="text-4xl md:text-6xl font-black text-[rgba(251,146,60,1)] mb-10 italic uppercase tracking-tighter"
    >
      Promotions <span class="text-red-600 animate-pulse">Flash</span>
    </h1>

    <div v-if="loading" class="flex flex-col justify-center items-center py-20 gap-4">
      <div
        class="animate-spin rounded-full h-16 w-16 border-t-4 border-orange-500 border-r-4 border-r-transparent"
      ></div>
      <p class="text-slate-400 font-black uppercase italic animate-pulse">
        Recherche des pépites...
      </p>
    </div>

    <div
      v-else
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"
    >
      <router-link
        v-for="promo in promotions"
        :key="promo.id"
        :to="{ name: 'product.detail', params: { id: promo.product.id } }"
        class="relative h-[550px] rounded-[2.5rem] overflow-hidden shadow-2xl group border-4 border-white block transition-all hover:shadow-orange-200/50"
      >
        <div class="absolute inset-0 w-full h-full bg-slate-100">
          <Transition name="fade" mode="out-in">
            <img
              :key="getImageForPromo(promo)"
              :src="getImageForPromo(promo)"
              class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
            />
          </Transition>
        </div>

        <div
          class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-80 group-hover:opacity-90 transition-opacity"
        ></div>

        <div
          class="absolute top-6 left-6 bg-red-600 text-white font-black px-4 py-2 rounded-xl shadow-lg z-20 text-sm italic"
        >
          -{{ calculateDiscount(promo.product.price, promo.sale_price) }}%
        </div>

        <div
          class="absolute top-6 right-6 p-4 bg-orange-500 rounded-2xl text-white shadow-xl opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0 z-30"
        >
          <Eye class="h-6 w-6 stroke-[3px]" />
        </div>

        <div class="absolute bottom-0 left-0 right-0 p-8 z-20 text-white">
          <span
            class="text-orange-400 text-[10px] font-black uppercase tracking-[0.3em] mb-2 block"
          >
            {{ promo.product.category?.name || "Collection" }}
          </span>

          <h2
            class="font-black text-2xl uppercase italic leading-tight mb-4 drop-shadow-lg"
          >
            {{ promo.product.name }}
          </h2>

          <div class="flex flex-col gap-0 mb-6">
            <span class="text-slate-400 line-through text-sm font-bold italic opacity-70">
              {{ formatPrice(promo.product.price) }}
            </span>
            <div class="flex items-baseline gap-2">
              <span
                class="text-white font-black text-4xl tracking-tighter drop-shadow-xl"
              >
                {{ formatPrice(promo.sale_price) }}
              </span>
              <span class="text-xs font-bold text-orange-500 uppercase italic">FCFA</span>
            </div>
          </div>

          <div
            class="flex items-center gap-3 bg-white/10 backdrop-blur-xl w-fit px-5 py-2.5 rounded-2xl border border-white/20"
          >
            <Clock class="w-4 h-4 text-orange-400" />
            <div class="flex flex-col">
              <span
                class="text-[8px] font-black uppercase tracking-widest text-slate-300 leading-none"
                >Expire le</span
              >
              <span class="text-xs font-bold text-white">{{
                formatDate(promo.end_at)
              }}</span>
            </div>
          </div>
        </div>
      </router-link>
    </div>

    <div
      v-if="!promotions.length && !loading"
      class="text-center py-32 bg-white rounded-[3rem] border-4 border-dashed border-slate-100"
    >
      <div class="mb-6 opacity-20">
        <ShoppingCart class="w-20 h-20 mx-auto" />
      </div>
      <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-xl italic">
        Les offres flash arrivent bientôt...
      </p>
      <router-link
        to="/shop"
        class="inline-block mt-8 bg-slate-900 text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-500 transition-colors"
      >
        Voir tout le catalogue
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Eye, Clock, ShoppingCart } from "lucide-vue-next";
import api from "@/services/api";

// ÉTAT
const promotions = ref([]);
const loading = ref(false);
const imageIndex = ref(0);
let intervalId = null;

// RÉCUPÉRATION DES DONNÉES
const fetchPromotions = async () => {
  loading.value = true;
  try {
    // On utilise ta route API Laravel
    const res = await api.get("/products/sale");
    promotions.value = res.data.data;
  } catch (err) {
    console.error("Erreur lors du chargement des promos:", err);
  } finally {
    loading.value = false;
  }
};

// LOGIQUE D'ANIMATION DES IMAGES
const getImageForPromo = (promo) => {
  const images = promo.product?.images || [];
  // Image principale par défaut
  const mainImage = promo.product?.image_url || "https://via.placeholder.com/600x800";

  if (images.length === 0) return mainImage;

  // On crée un tableau avec toutes les images disponibles
  const allImgs = [mainImage, ...images.map((img) => img.url || img)];
  const idx = imageIndex.value % allImgs.length;
  return allImgs[idx];
};

// CALCUL DU % DE RÉDUCTION
const calculateDiscount = (oldPrice, newPrice) => {
  if (!oldPrice || !newPrice) return 0;
  const diff = oldPrice - newPrice;
  return Math.round((diff / oldPrice) * 100);
};

// FORMATTAGE PRIX (FCFA)
const formatPrice = (price) => {
  return new Intl.NumberFormat("fr-FR", {
    maximumFractionDigits: 0,
  }).format(price);
};

// FORMATTAGE DATE
const formatDate = (dateStr) => {
  if (!dateStr) return "Fin de semaine";
  return new Date(dateStr).toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "long",
  });
};

// CYCLES DE VIE
onMounted(() => {
  fetchPromotions();
  // Change d'image toutes les 3 secondes pour l'effet dynamique
  intervalId = setInterval(() => {
    imageIndex.value++;
  }, 3000);
});

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId);
});
</script>

<style scoped>
/* Animation de fondu entre les images */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.8s ease-in-out;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* On force le dégradé pour la lisibilité sur mobile */
.bg-gradient-to-t {
  background-image: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.95) 0%,
    rgba(0, 0, 0, 0.4) 40%,
    transparent 100%
  );
}

/* Masquer la scrollbar */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
