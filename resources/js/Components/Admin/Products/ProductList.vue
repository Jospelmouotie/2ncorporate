<template>
  <div class="p-4 md:p-8 bg-slate-50 min-h-screen font-sans text-slate-900">

    <div v-if="!selectedCategory" class="max-w-6xl mx-auto">
      <header class="mb-8 md:mb-12">
        <h2 class="text-3xl md:text-5xl font-black text-slate-800 tracking-tight">Catalogue Admin</h2>
        <p class="text-slate-500 mt-2 font-medium">Sélectionnez une catégorie pour gérer l'inventaire</p>
      </header>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div
          v-for="cat in (categoryStore.categories?.data || categoryStore.categories || [])"
          :key="cat.id"
          @click="selectCategory(cat)"
          class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-slate-100 hover:border-orange-500 hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer group"
        >
          <div class="w-14 h-14 md:w-20 md:h-20 bg-orange-50 rounded-2xl md:rounded-3xl flex items-center justify-center text-3xl mx-auto mb-4 md:mb-6 group-hover:bg-orange-500 group-hover:text-white transition-colors">
            📁
          </div>
          <h3 class="font-bold text-slate-800 capitalize text-center text-lg">{{ cat.name }}</h3>
        </div>
      </div>
    </div>

    <div v-else class="max-w-7xl mx-auto">

      <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <button @click="selectedCategory = null" class="flex items-center gap-2 text-orange-600 font-black uppercase text-[10px] tracking-widest hover:gap-4 transition-all">
          <span>←</span> Retour aux catégories
        </button>

        <button @click="isFilterOpen = !isFilterOpen" class="lg:hidden bg-white px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold flex items-center gap-2">
          <span>{{ isFilterOpen ? 'Fermer' : 'Filtres' }}</span>
        </button>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">

        <aside :class="['lg:w-80 space-y-6 bg-white lg:bg-transparent p-4 lg:p-0 rounded-[2rem] lg:rounded-none shadow-xl lg:shadow-none border lg:border-none border-slate-100', isFilterOpen ? 'block' : 'hidden lg:block']">
          <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
            <label class="block font-black text-slate-400 mb-2 uppercase text-[10px] tracking-widest">Rechercher</label>
            <input v-model="search" type="text" placeholder="Nom du produit..." class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-orange-500" />
          </div>

          <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
            <label class="block font-black text-slate-400 mb-2 uppercase text-[10px] tracking-widest">Prix (FCFA)</label>
            <div class="grid grid-cols-2 gap-2">
              <input v-model.number="priceRange.min" type="number" placeholder="Min" class="w-full px-3 py-3 bg-slate-50 rounded-xl outline-none" />
              <input v-model.number="priceRange.max" type="number" placeholder="Max" class="w-full px-3 py-3 bg-slate-50 rounded-xl outline-none" />
            </div>
          </div>

          <button @click="editingProduct = null; showForm = true" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">
            + Ajouter Produit
          </button>
        </aside>

        <main class="flex-1 min-w-0">
          <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">

            <div class="p-6 md:p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
              <h2 class="text-xl md:text-2xl font-black text-slate-800">{{ selectedCategory?.name }}</h2>
              <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ filteredProducts.length }} Articles</span>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left">
                <thead>
                  <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-6 py-4">Produit</th>
                    <th class="px-6 py-4">Prix</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr v-for="p in filteredProducts" :key="p.id" class="hover:bg-orange-50/20 transition-colors">
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                          <img v-if="p.image_url" :src="p.image_url" class="w-full h-full object-cover" />
                          <div v-else class="w-full h-full flex items-center justify-center text-xl">📦</div>
                        </div>
                        <div>
                          <p class="font-black text-slate-800 truncate max-w-[200px]">{{ p.name }}</p>
                          <p class="text-[10px] text-slate-400 uppercase font-bold">ID: #{{ p.id }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 font-black text-slate-700">
                      {{ formatPrice(p.price) }} <span class="text-[10px] text-slate-400 ml-1">FCFA</span>
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

            <div v-if="filteredProducts.length === 0" class="py-20 text-center text-slate-400 font-bold uppercase text-xs">
              Aucun produit trouvé dans cette catégorie
            </div>
          </div>
        </main>
      </div>
    </div>

    <ProductForm v-model="showForm" :product="editingProduct" @saved="productStore.fetchProducts()" />
    <Toast />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useProductStore } from '@/stores/productStore'
import { useCategoryStore } from '@/stores/categoryStore'
import { useToastStore } from '@/stores/useToastStore'
import ProductForm from './ProductForm.vue'
import Toast from '../../ui/Toast.vue'

const productStore = useProductStore()
const categoryStore = useCategoryStore()
const toast = useToastStore()

const search = ref('')
const selectedCategory = ref(null)
const priceRange = ref({ min: null, max: null })
const sort = ref('newest')
const showForm = ref(false)
const editingProduct = ref(null)
const isFilterOpen = ref(false)

onMounted(async () => {
  await categoryStore.fetchCategories()
  await productStore.fetchProducts()
})

const filteredProducts = computed(() => {
  // ✅ On sécurise l'accès au tableau de produits
  const all = productStore.products || []
  let items = Array.isArray(all) ? [...all] : []

  // ✅ Filtrage Catégorie (Comparaison forcée en Number)
  if (selectedCategory.value) {
    items = items.filter(p => Number(p.category_id) === Number(selectedCategory.value.id))
  }

  // Recherche
  if (search.value) {
    const q = search.value.toLowerCase()
    items = items.filter(p => p.name?.toLowerCase().includes(q))
  }

  // Prix
  if (priceRange.value.min) items = items.filter(p => Number(p.price) >= priceRange.value.min)
  if (priceRange.value.max) items = items.filter(p => Number(p.price) <= priceRange.value.max)

  return items
})

const selectCategory = (cat) => { selectedCategory.value = cat }
const openEdit = (product) => { editingProduct.value = { ...product }; showForm.value = true }

const confirmDelete = async (id) => {
  if (confirm('Supprimer ce produit ?')) {
    try {
      await productStore.deleteProduct(id)
      toast.add('Produit supprimé avec succès', 'success')
    } catch (e) {
      toast.add('Erreur lors de la suppression', 'error')
    }
  }
}

const formatPrice = (p) => new Intl.NumberFormat('fr-FR').format(p || 0)
</script>
