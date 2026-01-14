<template>
  <div class="min-h-screen bg-gray-50 pb-20 font-sans">
    <div class="bg-white border-b border-gray-100 py-10 mb-8">
      <div class="container mx-auto px-4">
        <h3 class="text-3xl md:text-xl font-black text-gray-900 uppercase italic tracking-tighter flex justify-center items-center">
          tous  <span class="text-orange-500">nos produits</span>
        </h3>
        <p class="text-gray-500 font-medium mt-2">Découvrez notre sélection exclusive.</p>
      </div>
    </div>

    <div class="container mx-auto px-4">
      <div class="flex flex-col lg:flex-row gap-8">
        <aside class="w-full lg:w-1/4">
          <FilterSidebar @filter="handleFilters" :initial-filters="filters" />
        </aside>

        <main class="w-full lg:w-3/4">
          <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-8 bg-white p-4 rounded-3xl shadow-sm">
            <div class="w-full md:w-1/2">
              <SearchBar v-model="filters.search" @update:modelValue="debouncedFetch" />
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
              <span class="text-xs font-bold text-gray-400 uppercase hidden md:block">Trier par:</span>
              <SortDropdown v-model="filters.sort" @change="fetchProducts" />
            </div>
          </div>

          <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 gap-4 animate-pulse">
            <div v-for="i in 6" :key="i" class="h-80 bg-gray-200 rounded-[2.5rem]"></div>
          </div>

          <div v-else-if="products.length > 0">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
              <ProductCard
                v-for="product in products"
                :key="product.id"
                :product="product"
              />
            </div>

            <div class="mt-12 flex justify-center">
              <Pagination
                :data="paginationData"
                @paginate="handlePagination"
              />
            </div>
          </div>

          <div v-else class="text-center py-20 bg-white rounded-[3rem] shadow-sm">
            <div class="text-6xl mb-4">🔍</div>
            <h3 class="text-xl font-black uppercase italic">Aucun produit trouvé</h3>
            <button @click="resetAllFilters" class="mt-6 text-orange-500 font-bold">Réinitialiser</button>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import _ from 'lodash'

import FilterSidebar from '@/Components/catalog/FilterSidebar.vue'
import SearchBar from '@/Components/catalog/SearchBar.vue'
import SortDropdown from '@/Components/catalog/SortDropdown.vue'
import ProductCard from '@/Components/product/ProductCard.vue'
import Pagination from '@/Components/catalog/Pagination.vue'

const route = useRoute()
const router = useRouter()

const products = ref([])
const paginationData = ref({})
const loading = ref(true)

const filters = reactive({
  search: route.query.search || '',
  category: route.query.category || '',
  max_price: route.query.max_price || '',
  onSale: route.query.onSale || false,
  sort: route.query.sort || 'newest',
  page: route.query.page || 1
})

const fetchProducts = async () => {
  loading.value = true
  try {
    // On nettoie les filtres pour ne pas envoyer de valeurs vides à l'API
    const cleanParams = {}
    Object.keys(filters).forEach(key => {
      if (filters[key] !== '' && filters[key] !== null) {
        cleanParams[key] = filters[key]
      }
    })

    const response = await axios.get('/api/products', { params: cleanParams })

    // Laravel Paginate retourne les produits dans .data
    products.value = response.data.data
    // On stocke toute la réponse pour les liens de pagination (current_page, last_page, etc.)
    paginationData.value = response.data

    // Mettre à jour l'URL sans recharger la page
    router.push({ query: cleanParams })
  } catch (error) {
    console.error("Erreur chargement:", error)
  } finally {
    loading.value = false
  }
}

const debouncedFetch = _.debounce(() => {
  filters.page = 1
  fetchProducts()
}, 500)

const handleFilters = (newFilters) => {
  Object.assign(filters, newFilters)
  filters.page = 1 // Revenir à la page 1 lors d'un nouveau filtrage
  fetchProducts()
}

const handlePagination = (page) => {
  filters.page = page
  fetchProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const resetAllFilters = () => {
  filters.search = ''
  filters.category = ''
  filters.max_price = ''
  filters.onSale = false
  filters.page = 1
  fetchProducts()
}

onMounted(fetchProducts)

watch(() => route.query.category, (newVal) => {
  if (newVal !== undefined) {
    filters.category = newVal
    fetchProducts()
  }
})
</script>
