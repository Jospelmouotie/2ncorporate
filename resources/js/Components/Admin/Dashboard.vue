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
      <StatCard
        title="Revenu Total"
        :value="formatPrice(stats.total_revenue)"
        icon="DollarSign"
        color="bg-slate-900"
        icon-color="text-orange-400"
      />
      <StatCard
        title="Commandes"
        :value="stats.total_orders"
        icon="ShoppingBag"
        color="bg-white"
        icon-color="text-orange-500"
      />
      <StatCard
        title="Produits"
        :value="stats.total_products"
        icon="Package"
        color="bg-white"
        icon-color="text-slate-400"
      />
      <StatCard
        title="Utilisateurs"
        :value="stats.total_users"
        icon="Users"
        color="bg-white"
        icon-color="text-slate-400"
      />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
        <h3 class="text-slate-900 font-black uppercase tracking-widest text-xs mb-8 flex items-center gap-2">
          <Activity class="w-4 h-4 text-orange-500" /> État des commandes
        </h3>

        <div class="space-y-6">
          <StatusRow label="En attente" :count="stats.pending_orders" color="bg-orange-500" :total="stats.total_orders" />
          <StatusRow label="En cours" :count="stats.processing_orders" color="bg-blue-500" :total="stats.total_orders" />
          <StatusRow label="Livré" :count="stats.delivered_orders" color="bg-emerald-500" :total="stats.total_orders" />
        </div>
      </div>

      <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="relative z-10">
          <h3 class="text-orange-400 font-black uppercase tracking-widest text-[10px] mb-2">Actions rapides</h3>
          <p class="text-xl font-black italic leading-tight mb-6">Mettre à jour votre catalogue produits</p>
          <router-link
            to="/admin/products"
            class="inline-flex items-center gap-3 bg-orange-500 text-slate-900 font-black px-6 py-3 rounded-xl uppercase text-[10px] tracking-widest hover:bg-white transition-all"
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

// Composants internes pour la clarté
import StatCard from '@/Components/Admin/Dashboard/StatCard.vue'
import StatusRow from '@/Components/Admin/Dashboard/StatusRow.vue'

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

const formatPrice = (value) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF' }).format(value)
}

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/admin/dashboard/stats')
    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (error) {
    console.error("Erreur lors de la récupération des stats", error)
  }
}

onMounted(fetchStats)
</script>
