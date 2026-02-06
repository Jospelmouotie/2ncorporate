<template>
  <div class="min-h-screen bg-slate-50 py-24 px-4 md:px-10">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-4xl md:text-6xl font-black uppercase italic tracking-tighter text-slate-900 mb-12">
        Mon Panier <span class="text-orange-500">({{ cartStore.totalItems }})</span>
      </h1>

      <div v-if="cartStore.loading && cartStore.items.length === 0" class="space-y-4">
        <div v-for="i in 3" :key="i" class="h-40 bg-white animate-pulse rounded-[2.5rem] border border-slate-100"></div>
      </div>

      <div v-else-if="cartStore.items.length > 0" class="flex flex-col lg:flex-row gap-10">

        <div class="flex-grow space-y-4">
          <div v-for="item in cartStore.items" :key="item.id"
               class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center gap-6 transition-all hover:shadow-md">

            <div class="w-32 h-32 bg-slate-50 rounded-2xl overflow-hidden flex-shrink-0 border border-slate-100">
                <img :src="item.product?.image_url"
                     @error="(e) => e.target.src = '/storage/placeholder.png'"
                     class="w-full h-full object-contain p-2"
                     :alt="item.product?.name" />
            </div>

            <div class="flex-grow text-center md:text-left">
              <h3 class="font-black uppercase text-xl text-slate-800 leading-tight">
                {{ item.product?.name || 'Produit sans nom' }}
              </h3>
              <p class="text-orange-600 font-black text-lg">
                {{ formatPrice(item.product?.final_price || 0) }} FCFA
              </p>
            </div>

            <div class="flex items-center bg-slate-100 rounded-2xl p-1 border-2 border-slate-200">
              <button
                @click="updateQty(item.product_id, item.quantity - 1)"
                class="w-12 h-12 font-black text-xl hover:bg-white rounded-xl transition-all disabled:opacity-30"
                :disabled="cartStore.loading"
              >−</button>

              <span class="w-10 text-center font-black text-lg">
                <template v-if="cartStore.loading">...</template>
                <template v-else>{{ item.quantity }}</template>
              </span>

              <button
                @click="updateQty(item.product_id, item.quantity + 1)"
                class="w-12 h-12 font-black text-xl hover:bg-white rounded-xl transition-all disabled:opacity-30"
                :disabled="cartStore.loading"
              >+</button>
            </div>

            <div class="text-right min-w-[150px]">
              <p class="font-black text-2xl text-slate-900 italic">
                {{ formatPrice((item.product?.final_price || 0) * item.quantity) }} F
              </p>
              <button
                @click="removeItem(item.product_id)"
                class="text-red-500 font-black text-[10px] uppercase tracking-tighter mt-1 hover:underline disabled:opacity-50"
                :disabled="cartStore.loading"
              >
                Supprimer l'article
              </button>
            </div>
          </div>
        </div>

        <div class="w-full lg:w-[400px]">
          <div class="bg-slate-900 text-white rounded-[3.5rem] p-10 sticky top-32 shadow-2xl overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 rounded-full -mr-16 -mt-16"></div>

            <h2 class="text-3xl font-black uppercase italic mb-8 relative">Résumé</h2>

            <div class="space-y-4 border-b border-white/10 pb-8 mb-8">
              <div class="flex justify-between text-white/60 font-bold uppercase text-sm">
                <span>Articles ({{ cartStore.totalItems }})</span>
                <span>{{ formatPrice(cartStore.totalPrice) }} F</span>
              </div>
              <div class="flex justify-between text-white/60 font-bold uppercase text-sm">
                <span>Livraison</span>
                <span class="text-green-400">Gratuite</span>
              </div>
            </div>

            <div class="flex justify-between items-end mb-10">
              <div class="flex flex-col">
                <span class="font-black uppercase text-xs tracking-widest text-orange-500">Total Net</span>
                <span class="text-[10px] text-white/40 uppercase">TVA Incluse</span>
              </div>
              <span class="text-5xl font-black italic tracking-tighter">{{ formatPrice(cartStore.totalPrice) }} F</span>
            </div>

            <button
              class="w-full bg-orange-500 hover:bg-orange-600 text-white py-6 rounded-[2rem] font-black uppercase tracking-widest transition-all shadow-xl hover:scale-[1.02] active:scale-95 disabled:bg-slate-700"
              :disabled="cartStore.items.length === 0"
            >
              Confirmer l'achat
            </button>

            <p class="text-[10px] text-center text-white/30 uppercase mt-6 tracking-widest">
              Paiement sécurisé via 2N Corporate
            </p>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-32 bg-white rounded-[4rem] border-4 border-dashed border-slate-200">
        <div class="bg-slate-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8">
            <span class="text-5xl">🛒</span>
        </div>
        <h2 class="text-3xl font-black text-slate-300 uppercase italic mb-8">Votre panier est vide</h2>
        <router-link to="/shop" class="bg-slate-900 text-white px-12 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-500 transition-all shadow-xl inline-block">
          Découvrir nos produits
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useCartStore } from '@/stores/cartStore'

const cartStore = useCartStore()

// Formatage des prix (ex: 10 000)
const formatPrice = (p) => new Intl.NumberFormat('fr-FR').format(p)

// Mettre à jour la quantité
const updateQty = async (productId, newQuantity) => {
    if (newQuantity <= 0) {
        await removeItem(productId)
    } else {
        // On appelle l'action updateQuantity du store (que nous avons créée dans l'étape précédente)
        await cartStore.updateQuantity(productId, newQuantity)
    }
}

// Supprimer un article
const removeItem = async (productId) => {
    if (confirm("Voulez-vous retirer cet article du panier ?")) {
        await cartStore.removeItem(productId)
    }
}

// Charger le panier au montage du composant
onMounted(() => {
    cartStore.fetchCart()
})
</script>

<style scoped>
/* Optionnel : transition douce pour les listes */
.flex-grow {
  transition: all 0.3s ease;
}
</style>
