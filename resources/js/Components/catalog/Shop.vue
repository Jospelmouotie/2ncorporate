<template>
  <div class="min-h-screen bg-gray-50 pb-20 font-sans">
    <div class="bg-white border-b border-gray-100 py-10 mb-8">
      <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-6xl font-black text-gray-900 uppercase italic tracking-tighter">
          Le <span class="text-orange-500">Catalogue</span>
        </h1>
        <p class="text-gray-500 font-medium mt-2">Découvrez notre sélection exclusive de produits.</p>
      </div>
    </div>



    <div class="bg-black text-green-400 p-4 mb-4 rounded-xl font-mono text-xs overflow-auto max-h-40">
  <p>DEBUG CONSOLE :</p>
  <ul>
    <li>Chargement : {{ loading }}</li>
    <li>Nb de produits : {{ products ? products.length : '0' }}</li>
    <li>Filtre Cat : {{ filters.category || 'Aucune' }}</li>
  </ul>
  <pre v-if="products && products.length > 0">{{ products[0].name }} - {{ products[0].price }} F</pre>
</div>

<div v-if="products && products.length > 0" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
    <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
    />
</div>

    <div class="container mx-auto px-4">
      <div class="flex flex-col lg:flex-row gap-8">

        <aside class="w-full lg:w-1/4">
          <FilterSidebar
            @filter="handleFilters"
            :initial-filters="filters"
          />
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

          <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 animate-pulse">
            <div v-for="i in 8" :key="i" class="h-80 bg-gray-200 rounded-[2.5rem]"></div>
          </div>

          <div v-else-if="products.length > 0">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
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
            <p class="text-gray-500 mt-2">Essayez de modifier vos filtres ou votre recherche.</p>
            <button @click="resetAllFilters" class="mt-6 text-orange-500 font-bold hover:underline">
              Réinitialiser tout
            </button>
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
import _ from 'lodash' // Utiliser lodash pour le debounce de la recherche

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

// État des filtres (initialisé avec les params de l'URL si présents)
const filters = reactive({
  search: route.query.search || '',
  category: route.query.category || '',
  min_price: route.query.min_price || '',
  max_price: route.query.max_price || '',
  sort: route.query.sort || 'newest',
  page: route.query.page || 1
})
const fetchProducts = async () => {
  loading.value = true
  console.log("--- DÉBUT FETCH ---");

  try {
    const params = { ...filters }
    console.log("Paramètres envoyés à Laravel:", params);

    const response = await axios.get('/api/products', { params })

    // DEBUG 1 : Voir la structure brute
    console.log("Structure brute de la réponse:", response);

    // Vérification de la présence de données
    if (response.data && response.data.data) {
        console.log("Nombre de produits reçus:", response.data.data.length);

        // FORÇAGE : On vide puis on remplit pour forcer Vue à réagir
        products.value = [];

        // Utilisation de nextTick pour forcer la réactivité
        setTimeout(() => {
            products.value = response.data.data;
            paginationData.value = response.data;
            console.log("Variable 'products' mise à jour avec succès");
        }, 100);

    } else {
        console.warn("L'API a répondu mais la clé 'data' est absente ou vide", response.data);
        alert("Attention: L'API ne renvoie pas de tableau 'data'. Vérifiez votre Controller.");
    }

    router.push({ query: params })
  } catch (error) {
    console.error("ERREUR CRITIQUE API:", error);
    alert("Erreur lors de l'appel API. Regardez la console (F12)");
  } finally {
    loading.value = false
    console.log("--- FIN FETCH ---");
  }
}
// Recherche avec délai (Debounce) pour ne pas harceler le serveur à chaque lettre
const debouncedFetch = _.debounce(() => {
  filters.page = 1
  fetchProducts()
}, 500)

const handleFilters = (newFilters) => {
  Object.assign(filters, newFilters)
  filters.page = 1
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
  filters.min_price = ''
  filters.max_price = ''
  filters.sort = 'newest'
  filters.page = 1
  fetchProducts()
}

onMounted(() => {
  fetchProducts()
})

// Surveiller les changements de route (ex: clic sur une catégorie dans le menu header)
watch(() => route.query.category, (newCat) => {
  if (newCat) {
    filters.category = newCat
    fetchProducts()
  }
})
</script>
