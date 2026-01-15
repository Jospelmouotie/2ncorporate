<template>
  <div class="p-4 md:p-8 bg-slate-50 min-h-screen">

    <div v-if="!selectedCategory" class="max-w-6xl mx-auto">
      <header class="mb-12">
        <h2 class="text-4xl font-black text-slate-800">Catalogue Admin</h2>
        <p class="text-slate-500">Sélectionnez une catégorie pour voir les produits</p>
      </header>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="cat in categoryStore.categories" :key="cat.id"
             @click="selectCategory(cat)"
             class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 hover:border-orange-500 hover:shadow-xl transition-all cursor-pointer text-center group">
          <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 group-hover:bg-orange-500 transition-colors">📁</div>
          <h3 class="font-bold text-slate-800 capitalize">{{ cat.name }}</h3>
        </div>
      </div>
    </div>

    <div v-else class="max-w-7xl mx-auto">
      <div class="flex items-center justify-between mb-8">
        <button @click="selectedCategory = null" class="text-orange-600 font-black text-xs tracking-widest uppercase">
          ← Retour
        </button>
        <button @click="openCreate" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-sm">
          + Ajouter un produit
        </button>
      </div>

      <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
              <th class="px-6 py-4">Produit</th>
              <th class="px-6 py-4">Prix</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="p in filteredProducts" :key="p.id" class="hover:bg-slate-50/50">
              <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                  <img :src="p.images?.[0]?.url || '/placeholder.png'" class="w-12 h-12 rounded-lg object-cover border" />
                  <div>
                    <p class="font-black text-slate-800">{{ p.name }}</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">ID: #{{ p.id }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 font-black">{{ p.price }} FCFA</td>
              <td class="px-6 py-4 text-right">
                <button @click="openEdit(p)" class="mr-2 p-2 bg-blue-50 text-blue-600 rounded-lg">✏️</button>
                <button @click="confirmDelete(p.id)" class="p-2 bg-red-50 text-red-600 rounded-lg">🗑️</button>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="productStore.pagination" class="p-6 border-t border-slate-100 flex justify-between items-center bg-slate-50/50">
          <span class="text-xs font-bold text-slate-500">
            Page {{ productStore.pagination.current_page }} sur {{ productStore.pagination.last_page }}
          </span>
          <div class="flex gap-2">
            <button
              :disabled="productStore.pagination.current_page === 1"
              @click="productStore.fetchProducts(productStore.pagination.current_page - 1)"
              class="px-4 py-2 bg-white border rounded-lg text-sm disabled:opacity-50">Précédent</button>
            <button
              :disabled="productStore.pagination.current_page === productStore.pagination.last_page"
              @click="productStore.fetchProducts(productStore.pagination.current_page + 1)"
              class="px-4 py-2 bg-white border rounded-lg text-sm disabled:opacity-50">Suivant</button>
          </div>
        </div>
      </div>
    </div>

    <ProductForm v-model="showForm" :product="editingProduct" @saved="productStore.fetchProducts()" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useProductStore } from '@/stores/productStore'
import { useCategoryStore } from '@/stores/categoryStore'
import ProductForm from './ProductForm.vue'

const productStore = useProductStore()
const categoryStore = useCategoryStore()
const selectedCategory = ref(null)
const showForm = ref(false)
const editingProduct = ref(null)

onMounted(() => {
  productStore.fetchProducts()
  categoryStore.fetchCategories()
})

const selectCategory = (cat) => { selectedCategory.value = cat }
const openCreate = () => { editingProduct.value = null; showForm.value = true }
const openEdit = (p) => { editingProduct.value = p; showForm.value = true }

const filteredProducts = computed(() => {
  if (!selectedCategory.value) return []
  // Comparaison robuste : on vérifie category_id ou l'objet category
  return productStore.products.filter(p => {
    const catId = p.category_id || p.category?.id;
    return Number(catId) === Number(selectedCategory.value.id);
  })
})

const confirmDelete = async (id) => {
  if(confirm('Supprimer ?')) await productStore.deleteProduct(id)
}
</script>
