<template>
  <div class="min-h-screen bg-slate-50 py-24 px-4">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-5xl font-black uppercase italic italic tracking-tighter mb-10">
        Mes Favoris <span class="text-orange-500">({{ wishlistStore.count }})</span>
      </h1>

      <div v-if="wishlistStore.items.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        <div v-for="product in wishlistStore.items" :key="product.id"
             class="bg-white rounded-[2.5rem] p-6 shadow-xl border border-slate-100 group relative">

          <button @click="wishlistStore.removeFromWishlist(product.id)"
                  class="absolute top-4 right-4 z-10 bg-white shadow-lg p-2 rounded-full text-red-500 hover:bg-red-500 hover:text-white transition">
            <Trash2 class="w-5 h-5" />
          </button>

          <router-link :to="{ name: 'product.detail', params: { id: product.id }}">
            <div class="aspect-square bg-slate-50 rounded-3xl mb-6 overflow-hidden">
                <img :src="product.image_url" class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform" />
            </div>
            <h3 class="font-black uppercase text-slate-800 mb-2">{{ product.name }}</h3>
            <p class="text-orange-600 font-black text-xl">{{ product.final_price }} FCFA</p>
          </router-link>
        </div>
      </div>

      <div v-else class="text-center py-20 bg-white rounded-[3rem] border-4 border-dashed border-slate-200">
        <p class="text-slate-400 font-bold uppercase italic">Ta liste de favoris est vide pour le moment.</p>
        <router-link to="/shop" class="inline-block mt-6 bg-slate-900 text-white px-8 py-3 rounded-xl font-bold uppercase tracking-widest">Aller au shop</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useWishlistStore } from '@/stores/wishlistStore'
import { Trash2 } from 'lucide-vue-next'

const wishlistStore = useWishlistStore()

onMounted(() => {
  wishlistStore.fetchWishlist()
})
</script>
