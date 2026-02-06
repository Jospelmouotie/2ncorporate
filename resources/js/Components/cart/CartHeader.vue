<template>
  <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-orange-400 to-orange-500 text-white">
    <h2 class="text-lg font-semibold">Panier ({{ count }})</h2>
    <button @click="$emit('close')" class="text-white hover:text-gray-200 transition-colors">
      ✕
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  count: { type: Number, default: 0 }
})
</script>
