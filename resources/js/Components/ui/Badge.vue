<template>
  <span
    :class="[
      'inline-block text-xs font-semibold px-2 py-1 rounded-full',
      colorClass
    ]"
  >
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  color: { type: String, default: 'orange' } // orange | violet | gray | green | red
})

const COLORS = {
  orange: 'bg-orange-500 text-white',
  violet: 'bg-violet-600 text-white',
  gray: 'bg-gray-300 text-gray-800',
  green: 'bg-green-500 text-white',
  red: 'bg-red-500 text-white'
}

const colorClass = computed(() => COLORS[props.color] || COLORS.orange)
</script>
