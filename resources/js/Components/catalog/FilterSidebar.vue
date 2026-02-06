<template>
  <aside class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-8">
    <div class="flex items-center justify-between">
      <h3 class="font-black text-slate-800 uppercase text-xs tracking-widest">Filtres Avancés</h3>
      <button @click="resetFilters" class="text-[10px] text-orange-500 font-bold hover:underline">Effacer</button>
    </div>

    <div class="space-y-4">
      <label class="text-xs font-black text-slate-400 uppercase">Univers</label>
      <div class="flex flex-col gap-2">
        <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-3 cursor-pointer group">
          <input
            type="radio"
            name="category"
            :value="cat.id"
            v-model="selectedCategory"
            class="w-4 h-4 accent-orange-500"
          />
          <span class="text-sm font-medium text-slate-600 group-hover:text-orange-500 transition-colors">
            {{ cat.name }}
          </span>
        </label>
        <label class="flex items-center gap-3 cursor-pointer group">
          <input type="radio" name="category" value="" v-model="selectedCategory" class="w-4 h-4 accent-orange-500" />
          <span class="text-sm font-medium text-slate-600 group-hover:text-orange-500 transition-colors">Toutes les catégories</span>
        </label>
      </div>
    </div>

    <div class="space-y-4">
      <div class="flex justify-between items-end">
        <label class="text-xs font-black text-slate-400 uppercase">Budget Max</label>
        <span class="text-sm font-bold text-slate-700">{{ formatPrice(maxPrice) }} <small>F</small></span>
      </div>
      <input
        type="range"
        min="0"
        max="2000000"
        step="10000"
        v-model="maxPrice"
        class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-orange-500"
      />
    </div>

    <div class="pt-2 border-t border-slate-50 pt-4">
      <label class="relative flex items-center gap-3 cursor-pointer group">
        <div class="relative">
          <input type="checkbox" v-model="onSale" class="sr-only peer" />
          <div class="w-10 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
        </div>
        <span class="text-sm font-bold text-slate-600 group-hover:text-orange-500 transition-colors">En Promotion uniquement</span>
      </label>
    </div>

    <button
      @click="apply"
      class="w-full bg-slate-900 hover:bg-orange-600 text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest transition-all active:scale-95 shadow-lg shadow-slate-200"
    >
      Appliquer les filtres
    </button>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const emit = defineEmits(['filter'])

const categories = ref([])
const selectedCategory = ref('')
const maxPrice = ref(2000000)
const onSale = ref(false)

const formatPrice = (p) => new Intl.NumberFormat('fr-FR').format(p)

const fetchCategories = async () => {
  try {
    const res = await axios.get('/api/categories')
    categories.value = res.data.data || []
  } catch (e) {
    console.error("Erreur chargement catégories", e)
  }
}

const apply = () => {
  // IMPORTANT : On utilise les clés attendues par le ProductController.php
  emit('filter', {
    category: selectedCategory.value,
    max_price: maxPrice.value,
    onSale: onSale.value, // Envoyé en tant que booléen
  })
}

const resetFilters = () => {
  selectedCategory.value = ''
  maxPrice.value = 2000000
  onSale.value = false
  apply()
}

onMounted(fetchCategories)
</script>
