<template>
  <nav class="container mx-auto px-4 py-4">
    <ol class="flex items-center gap-2 text-sm">
      <li>
        <router-link to="/" class="flex items-center gap-1 text-gray-500 hover:text-purple-600 transition-colors">
          <Home class="w-4 h-4" />
          Accueil
        </router-link>
      </li>
      <ChevronRight class="w-4 h-4 text-gray-400" />

      <li v-for="(item, index) in items" :key="index">
        <router-link
          v-if="index < items.length - 1"
          :to="item.url"
          class="text-gray-500 hover:text-purple-600 transition-colors"
        >
          {{ item.label }}
        </router-link>
        <span v-else class="text-gray-800 font-semibold">{{ item.label }}</span>

        <ChevronRight v-if="index < items.length - 1" class="w-4 h-4 text-gray-400 inline-block mx-2" />
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { Home, ChevronRight } from 'lucide-vue-next'

defineProps({
  items: {
    type: Array,
    default: () => []
  }
})
</script>
