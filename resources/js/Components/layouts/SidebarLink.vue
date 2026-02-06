<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  // L'URL cible définie dans ton router/index.js (ex: '/admin/products')
  to: {
    type: String,
    required: true
  },
  // L'objet icône importé de lucide-vue-next
  icon: {
    type: Object,
    required: true
  }
})

const route = useRoute()

/**
 * Calcul de l'état actif :
 * Le lien s'allume en orange si le chemin actuel est identique au "to"
 * ou s'il commence par le "to" (ex: /admin/products/123)
 */
const isActive = computed(() => {
  if (!route || !route.path) return false
  return route.path === props.to || route.path.startsWith(props.to + '/')
})
</script>

<template>
  <router-link
    :to="to"
    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group no-underline outline-none"
    :class="[
      isActive
        ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20'
        : 'text-slate-400 hover:text-white hover:bg-slate-800'
    ]"
  >
    <component
      :is="icon"
      class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"
      :class="isActive ? 'text-white' : 'text-slate-500 group-hover:text-orange-500'"
    />

    <span class="font-bold text-sm uppercase italic tracking-wide">
      <slot />
    </span>
  </router-link>
</template>

<style scoped>
.no-underline {
  text-decoration: none;
}

/* Transition fluide pour le changement d'état */
.transition-all {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
