<template>
  <div class="p-4 md:p-8 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto space-y-8">

      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl md:text-4xl font-black text-slate-800 tracking-tight">Marketing</h1>
          <p class="text-slate-500 text-sm">Gérez vos bannières et vidéos publicitaires</p>
        </div>
        <div class="self-start sm:self-center">
          <span class="bg-purple-600 text-white px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-purple-200">
            Admin Panel
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-4 order-2 lg:order-1">
          <div class="sticky top-8">
            <AdvertisementForm @saved="adStore.fetchAds()" />
          </div>
        </div>

        <div class="lg:col-span-8 order-1 lg:order-2">
          <AdvertisementList />
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import AdvertisementForm from './AdvertisementForm.vue'
import AdvertisementList from './AdvertisementList.vue'
import { useAdStore } from '@/stores/advertisementStore'

const adStore = useAdStore()

const onAdSaved = () => {
  adStore.fetchAds() // Recharge la liste quand une pub est créée
}
</script>
