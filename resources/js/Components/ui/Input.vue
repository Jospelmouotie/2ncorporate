<template>
  <div class="flex flex-col w-full">
    <label v-if="label" class="mb-1 text-sm font-medium text-gray-700">{{ label }}</label>
    <input
      :type="type"
      :placeholder="placeholder"
      v-model="modelValue"
      :class="[
        'border rounded-md px-4 py-2 focus:outline-none focus:ring-2',
        error ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500'
      ]"
      @input="$emit('update:modelValue', modelValue)"
    />
    <span v-if="error" class="text-red-500 text-xs mt-1">{{ error }}</span>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: [String, Number],
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  label: { type: String, default: '' },
  error: { type: String, default: '' }
})

const modelValue = ref(props.modelValue)

watch(() => props.modelValue, (val) => {
  modelValue.value = val
})
</script>
