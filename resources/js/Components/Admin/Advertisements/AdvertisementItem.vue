<template>
  <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-white hover:border-purple-200 transition-all duration-300 group">

    <div
      class="relative aspect-video bg-slate-900 overflow-hidden cursor-none"
      @mousemove="handleMagnifier"
      @mouseenter="showMagnifier = true"
      @mouseleave="showMagnifier = false"
    >
      <video v-if="ad.type === 'video'"
        :src="ad.media_url"
        class="w-full h-full object-cover"
        controls
      ></video>

      <img v-else
        :src="ad.media_url"
        class="w-full h-full object-cover"
      />

      <div
        v-if="ad.type !== 'video' && showMagnifier"
        class="magnifier-glass"
        :style="magnifierStyle"
      ></div>

      <div class="absolute top-4 left-4 pointer-events-none">
        <span :class="typeBadgeColor" class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider backdrop-blur-md bg-white/90 shadow-sm">
          {{ ad.type }}
        </span>
      </div>
    </div>

    <div class="p-6">
      <h3 class="text-lg font-black text-slate-800 line-clamp-1 mb-1">{{ ad.title }}</h3>
      <div class="flex items-center text-slate-400 text-[11px] font-bold mb-4">
        <span class="mr-2">📅</span>
        <span>{{ formatDate(ad.start_at) }} — {{ ad.end_at ? formatDate(ad.end_at) : 'Illimité' }}</span>
      </div>
      <button @click="deleteAd" class="w-full bg-red-50 text-red-500 py-3 rounded-2xl text-xs font-black hover:bg-red-500 hover:text-white transition-all">
        SUPPRIMER
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAdStore } from '@/stores/advertisementStore'

const props = defineProps({ ad: Object })
const adStore = useAdStore()

const showMagnifier = ref(false)
const magnifierPos = ref({ x: 0, y: 0, bgX: 0, bgY: 0 })

const handleMagnifier = (e) => {
  if (props.ad.type === 'video') return

  const { left, top, width, height } = e.currentTarget.getBoundingClientRect()

  // Position de la souris relative au conteneur
  const x = e.pageX - left - window.pageXOffset
  const y = e.pageY - top - window.pageYOffset

  // Calcul du pourcentage pour le background-position de la loupe
  const bgX = (x / width) * 100
  const bgY = (y / height) * 100

  magnifierPos.value = { x, y, bgX, bgY }
}

const magnifierStyle = computed(() => {
  const zoomLevel = 2.5; // Niveau de zoom interne à la loupe
  return {
    top: `${magnifierPos.value.y}px`,
    left: `${magnifierPos.value.x}px`,
    backgroundImage: `url(${props.ad.media_url})`,
    backgroundPosition: `${magnifierPos.value.bgX}% ${magnifierPos.value.bgY}%`,
    backgroundSize: `${100 * zoomLevel}%`
  }
})

// Logique de style et formatage (inchangée)
const typeBadgeColor = computed(() => {
  switch(props.ad.type) {
    case 'video': return 'text-blue-600 border border-blue-100'
    default: return 'text-orange-600 border border-orange-100'
  }
})
const formatDate = (date) => date ? new Date(date).toLocaleDateString() : ''
const deleteAd = async () => { if(confirm('Supprimer ?')) await adStore.deleteAd(props.ad.id) }
</script>

<style scoped>
.magnifier-glass {
  position: absolute;
  width: 120px; /* Taille de la loupe */
  height: 120px;
  border: 3px solid white;
  border-radius: 50%;
  pointer-events: none; /* Laisse passer les événements souris vers le conteneur */
  transform: translate(-50%, -50%); /* Centre la loupe sur le curseur */
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
  background-repeat: no-repeat;
  z-index: 10;
}
</style>
