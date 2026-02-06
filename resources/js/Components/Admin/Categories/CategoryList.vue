<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h2 class="text-3xl font-black text-gray-800">Rayons & Catégories</h2>
          <p class="text-gray-500">Gérez l'organisation de votre catalogue produit</p>
        </div>
        <button
          @click="openForm(null)"
          class="px-6 py-3 bg-orange-500 text-white font-bold rounded-2xl hover:bg-orange-600 shadow-lg shadow-orange-200 transition-all flex items-center gap-2"
        >
          <span class="text-xl">+</span> Ajouter une catégorie
        </button>
      </div>

      <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
        <table class="min-w-full table-auto">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">ID</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Nom</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Slug</th>
              <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <CategoryItem
              v-for="category in categoryStore.categories"
              :key="category.id"
              :category="category"
              @edit="openForm"
              @delete="confirmDelete"
            />
          </tbody>
        </table>

        <div v-if="categoryStore.categories.length === 0" class="p-20 text-center">
          <div class="text-4xl mb-3">📁</div>
          <p class="text-gray-400 font-medium">Aucune catégorie trouvée.</p>
        </div>
      </div>
    </div>

    <CategoryForm
      v-if="showForm"
      :category="selectedCategory"
      @close="closeForm"
      @saved="fetchCategories"
    />

    <Toast />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCategoryStore } from '@/stores/categoryStore' // Chemin corrigé en minuscule
import { useToastStore } from '@/stores/useToastStore'    // Nom de fichier exact
import CategoryItem from './CategoryItem.vue'
import CategoryForm from './CategoryForm.vue'
import Toast from '@/Components/ui/Toast.vue'           // Utilisation de l'alias @ pour éviter les ../..

const categoryStore = useCategoryStore()
const toast = useToastStore()

const showForm = ref(false)
const selectedCategory = ref(null)

const openForm = (category) => {
  selectedCategory.value = category
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  selectedCategory.value = null
}

const fetchCategories = async () => {
  await categoryStore.fetchCategories()
}

const confirmDelete = async (categoryId) => {
  if (confirm('Voulez-vous vraiment supprimer cette catégorie ?')) {
    try {
      await categoryStore.deleteCategory(categoryId)
      toast.add('Catégorie supprimée avec succès', 'info')
    } catch (err) {
      toast.add('Erreur lors de la suppression', 'error')
    }
  }
}

onMounted(fetchCategories)
</script>
