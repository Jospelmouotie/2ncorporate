<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['paginate'])

// Laravel fournit les liens dans props.data.links
const links = computed(() => props.data.links || [])

const changePage = (url) => {
  if (!url) return

  // Extraire le numéro de page depuis l'URL (ex: /api/products?page=2)
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')

  if (page) {
    emit('paginate', page)
  }
}
</script>

<template>
  <nav v-if="links.length > 3" class="flex items-center justify-center gap-2">
    <button
      v-for="(link, k) in links"
      :key="k"
      v-html="link.label"
      @click="changePage(link.url)"
      :disabled="!link.url || link.active"
      class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
      :class="[
        link.active
          ? 'bg-orange-500 text-white shadow-lg shadow-orange-200'
          : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-100',
        !link.url ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'
      ]"
    />
  </nav>
</template>
