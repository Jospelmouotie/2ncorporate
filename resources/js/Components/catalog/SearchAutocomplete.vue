<template>
  <div class="relative">
    <input
      v-model="query"
      @input="fetchSuggestions"
      type="search"
      placeholder="Rechercher..."
      class="w-full px-4 py-3 rounded-xl border
             focus:border-orange-500 focus:outline-none"
    />

    <ul
      v-if="suggestions.length"
      class="absolute z-50 bg-white shadow rounded-xl w-full mt-2"
    >
      <li
        v-for="item in suggestions"
        :key="item.id"
        @click="select(item)"
        class="px-4 py-2 hover:bg-orange-100 cursor-pointer"
      >
        {{ item.name }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/services/api'

const query = ref('')
const suggestions = ref([])
const emit = defineEmits(['select'])

const fetchSuggestions = async () => {
  if (query.value.length < 2) {
    suggestions.value = []
    return
  }

  const res = await api.get('/search', {
    params: { q: query.value }
  })

  suggestions.value = res.data.data
}

const select = (item) => {
  suggestions.value = []
  emit('select', item)
}
</script>
