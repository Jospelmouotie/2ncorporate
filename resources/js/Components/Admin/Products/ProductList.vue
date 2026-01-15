<template>
  <div class="p-4 md:p-8 bg-slate-50 min-h-screen font-sans text-slate-900">

    <div v-if="!selectedCategory" class="max-w-6xl mx-auto">
      <header class="mb-12">
        <h2 class="text-3xl md:text-5xl font-black text-slate-800 tracking-tight">Catalogue Admin</h2>
        <p class="text-slate-500 mt-2 font-medium">Sélectionnez une catégorie pour gérer les produits</p>
      </header>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="cat in categoryStore.categories"
          :key="cat.id"
          @click="selectCategory(cat)"
          class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 hover:border-orange-500 hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer group"
        >
          <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 group-hover:bg-orange-500 group-hover:text-white transition-colors">
            📁
          </div>
          <h3 class="font-bold text-slate-800 capitalize text-center text-lg">{{ cat.name }}</h3>
        </div>
      </div>
    </div>

    <div v-else class="max-w-7xl mx-auto">

      <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <button @click="backToCategories" class="flex items-center gap-2 text-orange-600 font-black uppercase text-[10px] tracking-widest hover:gap-4 transition-all">
          <span>←</span> Retour aux catégories
        </button>

        <button @click="openCreate" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg">
          + Ajouter Produit
        </button>
      </div>

      <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">

        <div class="p-6 md:p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
          <h2 class="text-xl md:text-2xl font-black text-slate-800 uppercase tracking-tighter">{{ selectedCategory?.name }}</h2>
          <span v-if="productStore.pagination" class="text-xs font-bold text-slate-400 uppercase tracking-widest">
            Total : {{ productStore.pagination.total }} produits
          </span>
        </div>

        <div v-if="productStore.loading" class="py-24 text-center">
          <div class="inline-block animate-spin w-10 h-10 border-4 border-orange-500 border-t-transparent rounded-full mb-4"></div>
          <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">Récupération des données serveur...</p>
        </div>

        <div v-else-if="productStore.products.length > 0" class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                <th class="px-6 py-4">Produit</th>
                <th class="px-6 py-4">Prix</th>
                <th class="px-6 py-4">Stock</th>
                <th class="px-6 py-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="p in productStore.products" :key="p.id" class="hover:bg-orange-50/20 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                      <img v-if="p.images?.length" :src="p.images[0].url" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full flex items-center justify-center text-xl">📦</div>
                    </div>
                    <div>
                      <p class="font-black text-slate-800 truncate max-w-[250px]">{{ p.name }}</p>
                      <p class="text-[10px] text-slate-400 uppercase font-bold">Ref: #{{ p.id }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 font-black text-slate-700">
                  {{ formatPrice(p.price) }} <span class="text-[10px] text-slate-400 ml-1">FCFA</span>
                </td>
                <td class="px-6 py-4">
                  <span :class="p.stock > 0 ? 'text-green-600' : 'text-red-500'" class="text-xs font-bold">
                    {{ p.stock }} en stock
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end gap-2">
                    <button @click="openEdit(p)" class="p-2.5 bg-slate-100 rounded-lg hover:bg-blue-600 hover:text-white transition-all">✏️</button>
                    <button @click="confirmDelete(p.id)" class="p-2.5 bg-slate-100 rounded-lg hover:bg-red-600 hover:text-white transition-all">🗑️</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="py-24 text-center">
          <div class="text-4xl mb-4">🔍</div>
          <p class="text-slate-400 font-bold uppercase text-xs">Aucun article dans cette catégorie</p>
        </div>

        <div v-if="productStore.pagination && productStore.pagination.last_page > 1" class="p-6 border-t border-slate-100 flex justify-between items-center bg-slate-50/30">
          <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
            Page {{ productStore.pagination.current_page }} sur {{ productStore.pagination.last_page }}
          </span>

          <div class="flex gap-2">
            <button
              @click="changePage(productStore.pagination.current_page - 1)"
              :disabled="productStore.pagination.current_page === 1"
              class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase disabled:opacity-30 hover:bg-slate-50 transition-all shadow-sm"
            >
              Précédent
            </button>
            <button
              @click="changePage(productStore.pagination.current_page + 1)"
              :disabled="productStore.pagination.current_page === productStore.pagination.last_page"
              class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase disabled:opacity-30 hover:bg-slate-50 transition-all shadow-sm"
            >
              Suivant
            </button>
          </div>
        </div>
      </div>
    </div>

    <ProductForm v-model="showForm" :product="editingProduct" @saved="refreshData" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useProductStore } from '@/stores/productStore'
import { useCategoryStore } from '@/stores/categoryStore'
import ProductForm from './ProductForm.vue'

const productStore = useProductStore()
const categoryStore = useCategoryStore()

const selectedCategory = ref(null)
const showForm = ref(false)
const editingProduct = ref(null)

// --- Chargement initial (Toutes catégories) ---
onMounted(async () => {
  await categoryStore.fetchCategories()
  // On ne charge pas forcément les produits ici, on attend que l'user choisisse une catégorie
  // Ou on charge tout par défaut :
  await productStore.fetchProducts(1)
})

// --- Navigation ---
const selectCategory = async (cat) => {
  selectedCategory.value = cat
  await productStore.fetchProducts(1, cat.id) // Charge la page 1 de la catégorie X
}

const backToCategories = () => {
  selectedCategory.value = null
  productStore.fetchProducts(1) // Optionnel : recharger tous les produits
}

const changePage = async (page) => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
  // On passe l'id de la catégorie actuelle pour que Laravel filtre correctement la page suivante
  await productStore.fetchProducts(page, selectedCategory.value?.id)
}

// --- CRUD ---
const refreshData = async () => {
  await productStore.fetchProducts(productStore.pagination?.current_page || 1, selectedCategory.value?.id)
}

const openCreate = () => {
  editingProduct.value = null
  showForm.value = true
}

const openEdit = (product) => {
  editingProduct.value = { ...product }
  showForm.value = true
}

const confirmDelete = async (id) => {
  if (confirm('Supprimer définitivement ce produit ?')) {
    await productStore.deleteProduct(id)
    await refreshData()
  }
}

const formatPrice = (p) => new Intl.NumberFormat('fr-FR').format(p || 0)
</script>

<style scoped>
/* Ajout d'une transition douce pour le survol des lignes */
tr {
  transition: all 0.2s ease-in-out;
}
button:disabled {
  cursor: not-allowed;
}
</style>
