<template>
  <div class="lg:hidden">
    <transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="open" @click="$emit('close')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100]"></div>
    </transition>

    <transition
      enter-active-class="transition-transform duration-500 cubic-bezier(0.4, 0, 0.2, 1)"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-400 cubic-bezier(0.4, 0, 0.2, 1)"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <div v-if="open" class="fixed top-0 left-0 w-[85%] max-w-[340px] h-full bg-white z-[110] shadow-2xl flex flex-col">

        <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center border border-slate-700">
              <User class="w-6 h-6 text-orange-400" />
            </div>
            <div class="flex flex-col">
              <span class="text-[10px] text-orange-400 font-black uppercase tracking-widest leading-none">2Ncorporate</span>
              <span class="font-black text-lg italic tracking-tight">Bonjour, Admin</span>
            </div>
          </div>
          <button @click="$emit('close')" class="p-2 hover:bg-white/10 rounded-full transition-colors">
            <X class="w-6 h-6 text-white" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto scrollbar-hide">

          <div class="py-6 bg-slate-50/80 border-b border-slate-100">
            <h4 class="px-8 text-[10px] font-black text-orange-500 uppercase tracking-[0.2em] mb-4">Gestion 2Ncorporate</h4>
            <div class="px-4 space-y-1">
              <SidebarLink to="/admin/products" :icon="Package" @click="$emit('close')">Produits</SidebarLink>
              <SidebarLink to="/admin/categories" :icon="Grid" @click="$emit('close')">Catégories</SidebarLink>
              <SidebarLink to="/admin/promotions" :icon="Zap" @click="$emit('close')">Promotions</SidebarLink>
              <SidebarLink to="/admin/advertisements" :icon="Tv" @click="$emit('close')">Publicités</SidebarLink>
            </div>
          </div>

          <div class="py-6">
            <h4 class="px-8 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Navigation Shop</h4>
            <div class="px-4 space-y-1">
              <router-link to="/wishlist" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 group" @click="$emit('close')">
                <Heart class="w-5 h-5 text-slate-400 group-hover:text-orange-500" /> Mes Favoris
              </router-link>
              <router-link to="/cart" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 group" @click="$emit('close')">
                <ShoppingCart class="w-5 h-5 text-slate-400 group-hover:text-orange-500" /> Mon Panier
              </router-link>
            </div>
          </div>

          <hr class="mx-8 border-slate-100" />

          <div class="py-6">
            <h4 class="px-8 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Acheter par Rayon</h4>
            <div class="px-4">
              <router-link
                v-for="cat in categories"
                :key="cat.id"
                :to="{ name: 'category', params: { id: cat.id, slug: cat.slug || 'shop' } }"
                @click="$emit('close')"
                class="flex items-center justify-between px-4 py-3.5 rounded-2xl text-slate-600 font-bold hover:bg-orange-50 hover:text-orange-600 transition-all group"
              >
                <span class="text-sm uppercase tracking-tighter italic">{{ cat.name }}</span>
                <ChevronRight class="w-4 h-4 text-slate-300 group-hover:text-orange-400" />
              </router-link>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-slate-100">
          <button class="w-full flex items-center justify-center gap-2 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-orange-600 transition-all shadow-lg shadow-slate-200">
            <LogOut class="w-5 h-5 text-orange-400" /> Déconnexion
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { X, Package, Grid, Zap, Tv, ChevronRight, User, LogOut, Heart, ShoppingCart } from 'lucide-vue-next'
import SidebarLink from '@/Components/layouts/SidebarLink.vue'

defineProps({
  open: Boolean,
  categories: {
    type: Array,
    default: () => []
  }
})

defineEmits(['close'])
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
/* Animations de transition */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
