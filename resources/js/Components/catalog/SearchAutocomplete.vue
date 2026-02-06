<template>
  <div class="relative w-full">
    <input
      ref="searchInput"
      v-model="localSearchTerm"
      type="text"
      placeholder="Rechercher un produit, une catégorie..."
      class="w-full bg-transparent border-none outline-none text-sm font-medium placeholder:text-gray-400 py-2"
      @input="debouncedSearch"
      @focus="showSuggestions = true"
      @blur="onBlur"
      @keyup.enter="performSearch"
    />

    <!-- Suggestions Dropdown -->
    <div v-if="showSuggestions && suggestions.length > 0"
         class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50">
      <div class="py-2 max-h-96 overflow-y-auto">
        <div v-for="item in suggestions" :key="item.id"
             class="flex items-center gap-4 p-4 hover:bg-orange-50 cursor-pointer transition-colors"
             @mousedown="selectSuggestion(item)">
          <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
            <img :src="item.image" class="w-full h-full object-cover" />
          </div>
          <div class="flex-1 min-w-0">
            <h4 class="font-bold text-sm text-gray-900 truncate">{{ item.name }}</h4>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-xs text-gray-500">{{ item.category }}</span>
              <span class="text-xs font-bold" :class="item.has_discount ? 'text-red-600' : 'text-gray-700'">
                {{ formatPrice(item.final_price) }} F
              </span>
              <span v-if="item.has_discount" class="text-xs text-gray-400 line-through">
                {{ formatPrice(item.price) }} F
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Button -->
      <div class="border-t border-gray-100 p-3 bg-gray-50">
        <button @click="performSearch"
                class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl font-bold text-sm uppercase tracking-widest hover:shadow-md transition-all">
          Voir tous les résultats
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-4 z-50">
      <div class="flex items-center justify-center gap-3">
        <div class="w-6 h-6 border-2 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-sm text-gray-600">Recherche en cours...</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useSearchStore } from '@/stores/searchStore'

const router = useRouter()
const searchStore = useSearchStore()

const localSearchTerm = ref('')
const suggestions = ref([])
const showSuggestions = ref(false)
const loading = ref(false)
const debounceTimeout = ref(null)
const searchInput = ref(null)

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price)
}

const debouncedSearch = () => {
  clearTimeout(debounceTimeout.value)

  if (localSearchTerm.value.length < 2) {
    suggestions.value = []
    showSuggestions.value = false
    return
  }

  loading.value = true
  debounceTimeout.value = setTimeout(async () => {
    await fetchSuggestions()
    loading.value = false
  }, 300)
}

const fetchSuggestions = async () => {
  try {
    const response = await axios.get('/api/products/search', {
      params: { q: localSearchTerm.value }
    })

    if (response.data.success) {
      suggestions.value = response.data.data
      showSuggestions.value = true
    }
  } catch (error) {
    console.error('Error fetching suggestions:', error)
    suggestions.value = []
  }
}

const selectSuggestion = (item) => {
  localSearchTerm.value = item.name
  suggestions.value = []
  showSuggestions.value = false
  router.push(item.url)
  emit('search', localSearchTerm.value)
}

const performSearch = () => {
  if (localSearchTerm.value.trim()) {
    searchStore.setSearchTerm(localSearchTerm.value.trim())
    showSuggestions.value = false
    suggestions.value = []
    emit('search', localSearchTerm.value.trim())

    if (router.currentRoute.value.name !== 'home') {
      router.push({ name: 'home' })
    }
  }
}

const onBlur = () => {
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}

const clearSearch = () => {
  localSearchTerm.value = ''
  suggestions.value = []
  showSuggestions.value = false
  searchStore.clearSearch()
  emit('clear')
}

const focus = () => {
  searchInput.value?.focus()
}

onMounted(() => {
  // Sync with store
  localSearchTerm.value = searchStore.searchTerm
})

onUnmounted(() => {
  clearTimeout(debounceTimeout.value)
})

defineExpose({
  focus,
  clearSearch
})

const emit = defineEmits(['search', 'clear'])
</script>
