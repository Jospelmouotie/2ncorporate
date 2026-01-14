<template>
  <select
    v-model="value"
    @change="$emit('sort', value)"
    class="border rounded-lg px-3 py-2 focus:outline-none
           focus:border-orange-500"
  >
    <option value="">Trier par</option>
    <option value="price_asc">Prix ↑</option>
    <option value="price_desc">Prix ↓</option>
    <option value="newest">Nouveautés</option>
  </select>
</template>

<script setup>
import { ref } from 'vue'
const value = ref('')
</script>
