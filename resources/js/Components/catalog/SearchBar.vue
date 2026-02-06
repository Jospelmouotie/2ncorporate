<script setup>
import { ref, watch } from 'vue'

const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])

const localQuery = ref(props.modelValue)

// Dès qu'on tape, on met à jour le parent (V-model)
watch(localQuery, (newVal) => {
  emit('update:modelValue', newVal)
})
</script>

<template>
  <div class="relative w-64">
    <input
      v-model="localQuery"
      type="text"
      placeholder="Recherche instantanée..."
      class="w-full pl-10 pr-4 py-2 rounded-lg border focus:ring-2 focus:ring-orange-500 outline-none"
    />
    <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
  </div>
</template>
