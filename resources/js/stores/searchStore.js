import { defineStore } from 'pinia'

export const useSearchStore = defineStore('search', {
  state: () => ({
    results: [],
    isSearching: false,
    query: ''
  }),
  actions: {
    setResults(products) {
      this.results = products
      this.isSearching = products.length > 0 || this.query.length > 0
    },
    clearSearch() {
      this.results = []
      this.isSearching = false
      this.query = ''
    }
  }
})
