<template>
  <div class="relative" @mouseleave="open = false">
    <button @click="toggle" class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
      <slot name="button">Menu</slot>
      <span>▼</span>
    </button>
    <div v-if="open" class="absolute mt-1 bg-white border rounded shadow-md w-48 z-10">
      <slot name="items"></slot>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const open = ref(false)

const toggle = () => {
  open.value = !open.value
}
</script>
