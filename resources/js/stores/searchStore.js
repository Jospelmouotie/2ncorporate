import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useSearchStore = defineStore('search', () => {
  const searchTerm = ref('')
  const results = ref([])
  const isLoading = ref(false)
  const hasSearched = ref(false)

  const isSearching = computed(() => {
    return hasSearched.value && searchTerm.value.length > 0
  })

  const setSearchTerm = (term) => {
    searchTerm.value = term
    hasSearched.value = true
    if (term.length >= 2) {
      performSearch()
    } else {
      results.value = []
    }
  }

  const performSearch = async () => {
    if (searchTerm.value.length < 2) {
      results.value = []
      return
    }

    isLoading.value = true
    try {
      const response = await axios.get('/api/products', {
        params: {
          search: searchTerm.value,
          limit: 50
        }
      })

      if (response.data.data) {
        results.value = Array.isArray(response.data.data)
          ? response.data.data
          : response.data.data.data || []
      }
    } catch (error) {
      console.error('Search error:', error)
      results.value = []
    } finally {
      isLoading.value = false
    }
  }

  const clearSearch = () => {
    searchTerm.value = ''
    results.value = []
    hasSearched.value = false
  }

  return {
    searchTerm,
    results,
    isLoading,
    hasSearched,
    isSearching,
    setSearchTerm,
    performSearch,
    clearSearch
  }
})
