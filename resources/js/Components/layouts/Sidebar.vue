<script setup>
import { onMounted } from 'vue'
import { useCategoryStore } from '@/stores/categoryStore.js'
import { Package, Grid, Zap, Tv, ChevronRight, LayoutDashboard, Settings } from 'lucide-vue-next'
import SidebarLink from './SidebarLink.vue'

const categoryStore = useCategoryStore()

onMounted(() => {
  categoryStore.fetchCategories()
})
</script>

<template>
  <aside class="bg-slate-900 w-72 min-h-screen border-r border-slate-800 hidden lg:flex flex-col sticky top-0">
    <div class="p-8">
       <div class="flex flex-col">
         <span class="font-black text-2xl tracking-tighter text-white">
           2N<span class="text-orange-500">Corporate</span>
         </span>
         <div class="flex items-center gap-2">
           <span class="h-[1px] w-8 bg-orange-500"></span>
           <span class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Espace Admin</span>
         </div>
       </div>
    </div>

    <nav class="flex-1 px-4 space-y-1.5">
      <div class="mb-4">
        <h4 class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Gestion Boutique</h4>

        <SidebarLink to="/admin/dashboard" :icon="LayoutDashboard">Tableau de bord</SidebarLink>
        <SidebarLink to="/admin/products" :icon="Package">Produits</SidebarLink>
        <SidebarLink to="/admin/categories" :icon="Grid">Catégories</SidebarLink>
        <SidebarLink to="/admin/promotions" :icon="Zap">Promotions</SidebarLink>
        <SidebarLink to="/admin/advertisements" :icon="Tv">Publicités</SidebarLink>
      </div>

      <div class="my-6 border-t border-slate-800 pt-6">
        <h4 class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Vue Client (Rayons)</h4>
        <div class="max-h-[300px] overflow-y-auto scrollbar-hide">
          <router-link
            v-for="cat in categoryStore.categories"
            :key="cat.id"
            :to="`/shop?category=${cat.id}`"
            class="flex items-center justify-between px-4 py-3 text-slate-400 font-bold hover:text-orange-500 hover:bg-slate-800/50 rounded-xl transition-all group mb-1"
          >
            <span class="text-sm italic">{{ cat.name }}</span>
            <ChevronRight class="w-4 h-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
          </router-link>
        </div>
      </div>
    </nav>

    <div class="p-4 border-t border-slate-800">
      <router-link to="/admin/settings" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white transition-colors">
        <Settings class="w-5 h-5" />
        <span class="font-bold text-sm uppercase italic">Paramètres</span>
      </router-link>
    </div>
  </aside>
</template>

<style scoped>
/* Pour cacher la scrollbar du menu des catégories si elle est trop longue */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
