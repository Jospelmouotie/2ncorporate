<template>
  <div class="flex flex-col lg:flex-row gap-6 p-6 bg-gray-100 min-h-screen">
    <div class="lg:w-2/3 bg-white rounded-[2rem] shadow-sm p-6">
      <h2 class="text-2xl font-black mb-6 text-slate-800">Promotions Actives</h2>

      <div v-if="promotionStore.loading" class="py-10 text-center text-slate-500">
        Chargement des promotions...
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50">
            <tr class="text-[10px] font-black uppercase text-slate-400 tracking-widest">
              <th class="px-4 py-3">Produit</th>
              <th class="px-4 py-3">Remise</th>
              <th class="px-4 py-3">Prix Final</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="promo in promotionStore.promotions" :key="promo.id" class="hover:bg-slate-50/50 transition">
              <td class="px-4 py-4">
                <div class="font-bold text-slate-800">{{ promo.product?.name || 'Produit inconnu' }}</div>
                <div class="text-[10px] text-slate-400">ID: #{{ promo.product_id }}</div>
              </td>
              <td class="px-4 py-4">
                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                  -{{ calculateDiscount(promo.product?.price, promo.sale_price) }}%
                </span>
              </td>
              <td class="px-4 py-4 font-black text-orange-600">{{ promo.sale_price }} FCFA</td>
              <td class="px-4 py-4 text-right">
                <button @click="handleDelete(promo.id)" class="p-2 bg-red-50 text-red-600 rounded-lg">🗑️</button>
              </td>
            </tr>
            <tr v-if="promotionStore.promotions.length === 0">
              <td colspan="4" class="py-10 text-center text-slate-400 text-sm italic">Aucune promotion active</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="lg:w-1/3 bg-white rounded-[2.5rem] shadow-xl p-8 sticky top-6 h-fit border border-orange-100">
      <h2 class="text-2xl font-black mb-6 text-orange-500">Nouvelle Promo</h2>

      <div class="space-y-6">
        <div>
          <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">1. Catégorie</label>
          <select v-model="selectedCategoryId" @change="loadProducts" class="w-full px-5 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500">
            <option value="">Sélectionner une catégorie</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>

        <div v-if="productsInCat.length > 0">
          <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">2. Produit</label>
          <select v-model="selectedProduct" @change="onProductSelect" class="w-full px-5 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500">
            <option :value="null">Choisir le produit...</option>
            <option v-for="p in productsInCat" :key="p.id" :value="p">{{ p.name }} ({{ p.price }} FCFA)</option>
          </select>
        </div>

        <form v-if="selectedProduct" @submit.prevent="handleSubmit" class="space-y-4 pt-4 border-t border-slate-100">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Remise (%)</label>
              <input v-model="discountInput" @input="updateByPercent" type="number" min="1" max="99" class="w-full px-5 py-4 bg-orange-50 rounded-2xl font-bold text-orange-600 outline-none" />
            </div>
            <div>
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Prix Final</label>
              <input v-model="form.sale_price" @input="updateByPrice" type="number" required class="w-full px-5 py-4 bg-slate-50 rounded-2xl font-black outline-none" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Début</label>
              <input v-model="form.start_at" type="date" required class="w-full px-4 py-3 bg-slate-50 rounded-xl outline-none text-xs" />
            </div>
            <div>
              <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Fin</label>
              <input v-model="form.end_at" type="date" required class="w-full px-4 py-3 bg-slate-50 rounded-xl outline-none text-xs" />
            </div>
          </div>

          <button type="submit" :disabled="loading" class="w-full bg-slate-900 text-white font-black py-5 rounded-2xl hover:bg-orange-600 transition-all shadow-lg disabled:opacity-50">
            {{ loading ? 'Traitement...' : 'Appliquer la promotion' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { usePromotionStore } from '@/stores/promotionStore'
import api from '@/services/api'

// 1. Déclatations des variables
const promotionStore = usePromotionStore()
const loading = ref(false)
const categories = ref([])
const productsInCat = ref([])
const selectedCategoryId = ref('')
const selectedProduct = ref(null)
const discountInput = ref(0)

const form = reactive({
  product_id: '',
  sale_price: '',
  start_at: new Date().toISOString().substr(0, 10),
  end_at: ''
})

// 2. Fonctions utilitaires (doivent être déclarées avant l'usage dans le template)
const calculateDiscount = (oldP, newP) => {
  if (!oldP || !newP) return 0
  return Math.round(((oldP - newP) / oldP) * 100)
}

const resetForm = () => {
  selectedProduct.value = null
  selectedCategoryId.value = ''
  productsInCat.value = []
  form.product_id = ''
  form.sale_price = ''
  discountInput.value = 0
}

// 3. Logique de chargement
onMounted(async () => {
  try {
    const res = await api.get('/admin/categories')
    categories.value = res.data?.data || res.data
    await promotionStore.fetchPromotions()
  } catch (e) {
    console.error("Erreur onMounted", e)
  }
})

const loadProducts = async () => {
  if (!selectedCategoryId.value) return
  try {
    loading.value = true
    const res = await api.get(`/admin/products`, { params: { category_id: selectedCategoryId.value } })
    // On extrait le tableau que ce soit une pagination ou un tableau simple
    const raw = res.data?.data || res.data
    productsInCat.value = Array.isArray(raw) ? raw : (raw.data || [])
    selectedProduct.value = null
  } catch (e) {
    console.error("Erreur chargement produits", e)
  } finally {
    loading.value = false
  }
}

// 4. Logique de calcul
const onProductSelect = () => {
  if (selectedProduct.value) {
    form.product_id = selectedProduct.value.id
    form.sale_price = selectedProduct.value.price
    discountInput.value = 0
  }
}

const updateByPercent = () => {
  if (selectedProduct.value && discountInput.value) {
    const reduction = (selectedProduct.value.price * discountInput.value) / 100
    form.sale_price = Math.round(selectedProduct.value.price - reduction)
  }
}

const updateByPrice = () => {
  if (selectedProduct.value && form.sale_price) {
    const diff = selectedProduct.value.price - form.sale_price
    discountInput.value = Math.round((diff / selectedProduct.value.price) * 100)
  }
}

// 5. Actions (Submit / Delete)
const handleSubmit = async () => {
  try {
    loading.value = true
    await promotionStore.createPromotion({ ...form })
    resetForm()
    alert("Promotion créée !")
  } catch (err) {
    console.error("Erreur 422 détails:", err.response?.data)
    const msg = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join('\n')
      : "Erreur de validation"
    alert(msg)
  } finally {
    loading.value = false
  }
}

const handleDelete = async (id) => {
  if (confirm('Supprimer cette promotion ?')) {
    await promotionStore.deletePromotion(id)
  }
}
</script>
