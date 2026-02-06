<template>
  <div class="font-mono">
    <div v-if="!isExpired" class="flex items-center gap-1.5">
      <div class="flex gap-0.5 items-center">
        <div class="bg-black/80 backdrop-blur-md text-white w-8 h-8 flex items-center justify-center rounded-lg text-[11px] font-black border border-white/10 shadow-xl">
          {{ timeLeft.h }}
        </div>
        <span class="text-white font-bold animate-pulse text-xs">:</span>

        <div class="bg-black/80 backdrop-blur-md text-white w-8 h-8 flex items-center justify-center rounded-lg text-[11px] font-black border border-white/10 shadow-xl">
          {{ timeLeft.m }}
        </div>
        <span class="text-white font-bold animate-pulse text-xs">:</span>

        <div class="bg-orange-600 text-white w-8 h-8 flex items-center justify-center rounded-lg text-[11px] font-black shadow-lg shadow-orange-600/40 border border-orange-400/30">
          {{ timeLeft.s }}
        </div>
      </div>
    </div>

    <div v-else class="flex items-center">
      <div class="bg-red-700 text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-500 shadow-lg animate-bounce">
        Fin de promo
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  deadline: {
    type: String,
    required: true
  }
})

const timeLeft = ref({ h: '00', m: '00', s: '00' })
const isExpired = ref(false)
let timer = null

const updateTimer = () => {
  if (!props.deadline) {
    isExpired.value = true
    return
  }

  // Formatage pour assurer la compatibilité (remplace l'espace par T)
  const targetDate = new Date(props.deadline.replace(' ', 'T'))
  const now = new Date()
  const diff = targetDate.getTime() - now.getTime()

  if (diff <= 0 || isNaN(diff)) {
    timeLeft.value = { h: '00', m: '00', s: '00' }
    isExpired.value = true
    if (timer) clearInterval(timer)
    return
  }

  // Si on arrive ici, la promo est encore active
  isExpired.value = false

  const h = Math.floor(diff / (1000 * 60 * 60))
  const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  const s = Math.floor((diff % (1000 * 60)) / 1000)

  timeLeft.value = {
    h: h.toString().padStart(2, '0'),
    m: m.toString().padStart(2, '0'),
    s: s.toString().padStart(2, '0')
  }
}

// Relancer le timer si la deadline change (changement de produit dans le Hero)
watch(() => props.deadline, () => {
  if (timer) clearInterval(timer)
  updateTimer()
  timer = setInterval(updateTimer, 1000)
}, { immediate: true })

onMounted(() => {
  updateTimer()
  timer = setInterval(updateTimer, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>
