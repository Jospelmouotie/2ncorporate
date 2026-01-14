<template>
  <button
    :class="[
      'px-4 py-2 rounded-lg font-semibold transition-all duration-200 shadow-sm',
      variantClass,
      sizeClass,
      fullWidth ? 'w-full' : ''
    ]"
    :disabled="disabled"
    @click="$emit('click')"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'primary' }, // primary | secondary | outline | ghost
  size: { type: String, default: 'md' },          // sm | md | lg
  disabled: { type: Boolean, default: false },
  fullWidth: { type: Boolean, default: false }
})

const COLORS = {
  violet: '#8B5CF6',
  violetDark: '#6D28D9',
  orange: '#FB923C',
  orangeDark: '#F97316',
  white: '#FFFFFF',
  grayLight: '#F3F4F6',
  grayMedium: '#9CA3AF',
  grayDark: '#374151',
}

const variantClass = computed(() => {
  switch (props.variant) {
    case 'secondary':
      return `bg-gray-100 text-gray-800 hover:bg-gray-200`
    case 'outline':
      return `bg-transparent border border-gray-300 text-gray-800 hover:bg-gray-50`
    case 'ghost':
      return `bg-transparent text-gray-800 hover:bg-gray-100`
    default: // primary
      return `bg-orange-500 text-white hover:bg-orange-600`
  }
})

const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'text-sm px-3 py-1'
    case 'lg': return 'text-lg px-6 py-3'
    default: return 'text-base'
  }
})
</script>
