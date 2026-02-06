<template>
  <div
    :class="[
      'bg-white rounded-lg shadow-md p-4 transition-transform hover:scale-105',
      fullWidth ? 'w-full' : ''
    ]"
  >
    <slot />
  </div>
</template>

<script setup>
const props = defineProps({
  fullWidth: { type: Boolean, default: false }
})
</script>
