<template>
  <div :class="['animate-pulse bg-gray-200 rounded', widthClass, heightClass]"></div>
</template>

<script setup>
const props = defineProps({
  width: { type: String, default: 'w-full' },
  height: { type: String, default: 'h-6' }
})

const widthClass = props.width
const heightClass = props.height
</script>
