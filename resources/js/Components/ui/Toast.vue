<template>
  <transition name="toast-fade">
    <div
      v-if="toast.show"
      :class="[
        /* Positionnement responsive : en bas au centre sur mobile, en bas à droite sur desktop */
        'fixed bottom-6 left-1/2 -translate-x-1/2 md:left-auto md:right-6 md:translate-x-0',
        /* Largeur : presque plein écran sur mobile (w-[90%]), auto sur desktop */
        'w-[90%] md:w-auto min-w-[280px] max-w-[400px]',
        /* Style et Z-index */
        'px-6 py-4 rounded-2xl shadow-2xl text-white font-bold z-[200]',
        'flex items-center gap-3 transition-all duration-300',
        typeClass
      ]"
    >
      <span class="text-xl">{{ toast.type === 'success' ? '✅' : '❌' }}</span>
      <span class="flex-1 text-sm md:text-base leading-tight">{{ toast.message }}</span>

      <button @click="toast.show = false" class="ml-2 opacity-50 hover:opacity-100 text-xs">✕</button>
    </div>
  </transition>
</template>

<script setup>
import { computed } from 'vue'
import { useToastStore } from '@/stores/useToastStore'

const toast = useToastStore()

const typeClass = computed(() => ({
  success: 'bg-green-600',
  error: 'bg-red-600',
  info: 'bg-slate-800'
}[toast.type] || 'bg-slate-800'))
</script>

<style scoped>
.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Animation adaptée : monte depuis le bas sur mobile */
.toast-fade-enter-from {
  opacity: 0;
  transform: translate(-50%, 20px);
}

/* Sur Desktop, on ajuste l'animation si nécessaire via media query ou on garde le translate-x original */
@media (min-width: 768px) {
  .toast-fade-enter-from {
    transform: translateY(20px);
  }
  .toast-fade-leave-to {
    opacity: 0;
    transform: translateX(20px);
  }
}

.toast-fade-leave-to {
  opacity: 0;
  transform: translate(-50%, 20px);
}
</style>
