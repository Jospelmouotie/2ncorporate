<template>
  <div class="p-6 md:p-10 bg-slate-50 min-h-screen">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
      <div>
        <h1 class="text-3xl font-black text-slate-900 italic tracking-tighter uppercase">
          Tableau de <span class="text-orange-500">Bord</span>
        </h1>
        <p class="text-slate-500 font-medium italic">Bienvenue sur votre espace de gestion 2Ncorporate</p>
      </div>
      <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
        <div class="bg-orange-100 p-2 rounded-xl text-orange-600">
          <Calendar class="w-5 h-5" />
        </div>
        <span class="font-black text-slate-700 text-sm tracking-tight uppercase">
          {{ currentDate }}
        </span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl flex items-center gap-5 transition-transform hover:scale-[1.02]">
        <div class="p-4 rounded-2xl bg-white/10 text-orange-400">
          <DollarSign class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Revenu Total</p>
          <h4 class="text-xl font-black italic text-white">{{ formatPrice(stats.total_revenue) }}</h4>
        </div>
      </div>

      <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 transition-transform hover:scale-[1.02]">
        <div class="p-4 rounded-2xl bg-orange-50 text-orange-500">
          <ShoppingBag class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Commandes</p>
          <h4 class="text-xl font-black italic text-slate-900">{{ stats.total_orders }}</h4>
        </div>
      </div>

      <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 transition-transform hover:scale-[1.02]">
        <div class="p-4 rounded-2xl bg-slate-50 text-slate-400">
          <Package class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Produits</p>
          <h4 class="text-xl font-black italic text-slate-900">{{ stats.total_products }}</h4>
        </div>
      </div>

      <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 transition-transform hover:scale-[1.02]">
        <div class="p-4 rounded-2xl bg-slate-50 text-slate-400">
          <Users class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Utilisateurs</p>
          <h4 class="text-xl font-black italic text-slate-900">{{ stats.total_users }}</h4>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
        <h3 class="text-slate-900 font-black uppercase tracking-widest text-xs mb-8 flex items-center gap-2">
          <Activity class="w-4 h-4 text-orange-500" /> État des commandes
        </h3>

        <div class="space-y-6">
          <div class="space-y-2">
            <div class="flex justify-between items-end">
              <span class="text-xs font-black uppercase text-slate-500 italic">En attente</span>
              <span class="text-sm font-black text-slate-900">{{ stats.pending_orders }}</span>
            </div>
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full bg-orange-500 transition-all duration-1000" :style="{ width: calculatePercent(stats.pending_orders) }"></div>
            </div>
          </div>

          <div class="space-y-2">
            <div class="flex justify-between items-end">
              <span class="text-xs font-black uppercase text-slate-500 italic">En cours</span>
              <span class="text-sm font-black text-slate-900">{{ stats.processing_orders }}</span>
            </div>
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full bg-blue-500 transition-all duration-1000" :style="{ width: calculatePercent(stats.processing_orders) }"></div>
            </div>
          </div>

          <div class="space-y-2">
            <div class="flex justify-between items-end">
              <span class="text-xs font-black uppercase text-slate-500 italic">Livré</span>
              <span class="text-sm font-black text-slate-900">{{ stats.delivered_orders }}</span>
            </div>
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
              <div class="h-full bg-emerald-500 transition-all duration-1000" :style="{ width: calculatePercent(stats.delivered_orders) }"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="relative z-10 h-full flex flex-col justify-between">
          <div>
            <h3 class="text-orange-400 font-black uppercase tracking-widest text-[10px] mb-2">Actions rapides</h3>
            <p class="text-xl font-black italic leading-tight mb-6">Mettre à jour votre catalogue produits</p>
          </div>
          <router-link
            to="/admin/products"
            class="inline-flex items-center justify-center gap-3 bg-orange-500 text-slate-900 font-black px-6 py-4 rounded-xl uppercase text-[10px] tracking-widest hover:bg-white transition-all w-full"
          >
            Gérer l'inventaire <ArrowRight class="w-4 h-4" />
          </router-link>
        </div>
        <Package class="absolute -right-8 -bottom-8 w-40 h-40 text-white/5 rotate-12" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import {
  Calendar, DollarSign, ShoppingBag, Package,
  Users, Activity, ArrowRight
} from 'lucide-vue-next'

const stats = ref({
  total_orders: 0,
  total_revenue: 0,
  pending_orders: 0,
  processing_orders: 0,
  delivered_orders: 0,
  total_products: 0,
  total_users: 0
})

const currentDate = new Date().toLocaleDateString('fr-FR', {
  day: 'numeric', month: 'long', year: 'numeric'
})
console.log(window.user);
const formatPrice = (value) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XAF',
    minimumFractionDigits: 0
  }).format(value || 0)
}

const calculatePercent = (count) => {
  if (!stats.value.total_orders || stats.value.total_orders === 0) return '0%'
  return (count / stats.value.total_orders * 100) + '%'
}

const fetchStats = async () => {
  try {
    // Note: Assure-toi que withCredentials est configuré globalement pour axios
    const response = await axios.get('/api/admin/dashboard/stats')
    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (error) {
    console.error("Erreur dashboard:", error)
  }
}

onMounted(fetchStats)
</script>

<style scoped>
/* Suppression des scrollbars pour un look plus propre */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
