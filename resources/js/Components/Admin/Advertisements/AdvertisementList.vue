<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
      <h2 class="text-xl font-bold text-slate-700">Campagnes actives</h2>
      <button @click="adStore.fetchAds()" class="p-2 hover:bg-white rounded-full transition">🔄</button>
    </div>

    <div v-if="adStore.loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="i in 2" :key="i" class="h-64 bg-slate-200 animate-pulse rounded-3xl"></div>
    </div>

    <div v-else-if="adStore.ads.length === 0" class="bg-white p-12 rounded-[2rem] text-center border-2 border-dashed border-slate-200">
      <p class="text-slate-400 font-medium">Aucun média disponible pour le moment.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-6">
      <AdvertisementItem
        v-for="ad in adStore.ads"
        :key="ad.id"
        :ad="ad"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAdStore } from '@/stores/advertisementStore' // Vérifie bien le chemin
import AdvertisementItem from './AdvertisementItem.vue'

const adStore = useAdStore()

// On définit une fonction pour rafraîchir
const refreshList = () => {
  adStore.fetchAds()
}

onMounted(() => {
  refreshList()
})
</script>
