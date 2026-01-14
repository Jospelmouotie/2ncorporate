<script setup>
defineProps({ rating: { type: Number, default: 0 } })
</script>

<template>
  <div class="flex gap-1">
    <span v-for="i in 5" :key="i">
      {{ i <= rating ? '⭐' : '☆' }}
    </span>
  </div>
</template>
