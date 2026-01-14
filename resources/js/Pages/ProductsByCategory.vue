<template>
  <div class="min-h-screen bg-gray-50 pb-20 mt-28">
    <div class="container mx-auto px-4">
      <div class="flex items-center gap-5 mb-12">
        <div class="h-20 w-3 bg-orange-500 rounded-full"></div>
        <h1 class="text-5xl md:text-7xl font-black text-gray-900 uppercase italic tracking-tighter">
          Univers <span class="text-orange-500">{{ categoryName }}</span>
        </h1>
      </div>

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

          <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 gap-6 animate-pulse">
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

          <div v-else class="text-center py-32 bg-white rounded-[3rem] shadow-sm border border-dashed border-gray-200">
            <span class="text-6xl block mb-4">📦</span>
            <h2 class="text-2xl font-black uppercase text-gray-400">Aucun produit dans cette catégorie</h2>
            <button @click="resetFilters" class="mt-6 text-orange-500 font-bold underline">Réinitialiser les filtres</button>
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

// Imports des composants identiques au Shop
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
const categoryName = ref('')

// On initialise les filtres avec l'ID de la catégorie provenant de l'URL
const filters = reactive({
  search: '',
  category: route.params.id, // Fixé sur la catégorie actuelle
  max_price: '',
  onSale: false,
  sort: 'newest',
  page: 1
})

const fetchProducts = async () => {
  loading.value = true
  try {
    // Nettoyage des paramètres
    const cleanParams = {}
    Object.keys(filters).forEach(key => {
      if (filters[key] !== '' && filters[key] !== null) {
        cleanParams[key] = filters[key]
      }
    })

    const response = await axios.get('/api/products', { params: cleanParams })

    products.value = response.data.data
    paginationData.value = response.data

    // Mise à jour du titre
    if (route.params.slug) {
      categoryName.value = route.params.slug.replace(/-/g, ' ')
    }
  } catch (error) {
    console.error("Erreur chargement catégorie:", error)
  } finally {
    loading.value = false
  }
}

// Logique de recherche fluide
const debouncedFetch = _.debounce(() => {
  filters.page = 1
  fetchProducts()
}, 500)

const handleFilters = (newFilters) => {
  // On fusionne les nouveaux filtres MAIS on garde l'ID de catégorie actuel
  Object.assign(filters, { ...newFilters, category: route.params.id })
  filters.page = 1
  fetchProducts()
}

const handlePagination = (page) => {
  filters.page = page
  fetchProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const resetFilters = () => {
  filters.search = ''
  filters.max_price = ''
  filters.onSale = false
  filters.page = 1
  fetchProducts()
}

// Si on change de catégorie via le menu alors qu'on est déjà sur cette page
watch(() => route.params.id, (newId) => {
  filters.category = newId
  filters.page = 1
  fetchProducts()
})

onMounted(fetchProducts)
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
