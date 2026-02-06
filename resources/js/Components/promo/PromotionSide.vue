<script setup>
const props = defineProps({
  promo: { type: Object, required: true }
})

// Formatage pour l'affichage monétaire
const formatPrice = (val) => new Intl.NumberFormat('fr-FR').format(val)
</script>

<template>
  <div class="bg-white flex flex-col h-full overflow-hidden border-4 border-orange-50/50 hover:shadow-2xl transition-shadow duration-300">

    <div class="p-5 bg-orange-500 text-white flex justify-between items-center">
      <div class="flex items-center gap-2">
        <span class="animate-bounce">⚡</span>
        <h3 class="font-black text-sm uppercase tracking-widest">Offre Spéciale</h3>
      </div>
    </div>

    <router-link :to="{ name: 'product.detail', params: { id: promo.product.id } }" class="relative h-[300px] md:h-[400px] block overflow-hidden group">
      <img :src="promo.product.image_url"
           class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />

      <div class="absolute bottom-4 right-4 bg-red-600 text-white font-black text-xl px-4 py-2 rounded-xl shadow-2xl rotate-3">
        PROMO !
      </div>
    </router-link>

    <div class="p-6 bg-white flex-grow flex flex-col">
      <h4 class="font-black text-gray-800 text-2xl uppercase mb-2 line-clamp-2 leading-tight">
        {{ promo.product.name }}
      </h4>

      <div class="flex flex-col mb-6">
        <span class="text-gray-400 line-through font-bold text-sm">{{ formatPrice(promo.product.price) }} FCFA</span>
        <span class="text-red-600 text-4xl font-black tracking-tighter">
          {{ formatPrice(promo.sale_price) }} <small class="text-lg">FCFA</small>
        </span>
      </div>

      <router-link :to="{ name: 'product.detail', params: { id: promo.product.id } }"
        class="mt-auto block w-full bg-black text-white text-center py-5 rounded-2xl font-black uppercase tracking-[0.2em] hover:bg-orange-600 transition-colors shadow-lg active:scale-95">
        Profiter du deal
      </router-link>
    </div>
  </div>
</template>
