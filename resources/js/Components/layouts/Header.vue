<template>
  <header class="fixed top-0 left-0 w-full z-[60] bg-white shadow-md" @mouseleave="isMegaMenuOpen = false">
    <div class="bg-slate-900 text-white py-1.5 text-[11px] border-b border-orange-500/10">
      <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
          <span class="flex items-center gap-1.5 text-slate-400 font-medium">
            <Phone class="w-3 h-3 text-orange-500" /> Support: 237...
          </span>
        </div>
        <div class="flex items-center gap-2 font-black uppercase tracking-widest text-[10px] text-orange-400">
          <Truck class="w-4 h-4" /> Livraison Express Cameroun
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-3 flex items-center gap-4 md:gap-8">
      <div class="flex items-center gap-3">
        <button @click="$emit('open-menu')" class="lg:hidden p-2 hover:bg-slate-100 rounded-xl transition">
          <Menu class="w-7 h-7 text-slate-800" />
        </button>
        <router-link to="/" class="flex flex-col group">
          <span class="font-black text-2xl md:text-3xl tracking-tighter text-slate-900 leading-none">
            2N<span class="text-orange-500">Corporate</span>
          </span>
          <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em]">Business differently</span>
        </router-link>
      </div>

      <div class="hidden lg:flex flex-1 max-w-2xl px-4">
        <div class="w-full flex items-center bg-slate-100 rounded-2xl px-4 py-1 focus-within:bg-white focus-within:ring-2 focus-within:ring-orange-500/20 transition-all border border-transparent focus-within:border-orange-500/50">
          <SearchAutocomplete class="flex-1" />
          <Search class="w-5 h-5 text-slate-400" />
        </div>
      </div>

      <div class="flex items-center gap-1 md:gap-3">
        <router-link to="/wishlist" class="flex items-center gap-2 p-2.5 rounded-2xl hover:bg-orange-50 transition group relative">
          <Heart class="w-6 h-6 text-slate-700 group-hover:text-orange-500 group-hover:fill-orange-500" />
          <span v-if="wishlistCount > 0" class="absolute top-1 right-1 bg-orange-500 text-white text-[10px] font-black rounded-full w-4 h-4 flex items-center justify-center border-2 border-white">
            {{ wishlistCount }}
          </span>
        </router-link>

        <router-link to="/cart" class="flex items-center gap-2 bg-slate-900 text-white p-2.5 md:px-5 rounded-2xl hover:bg-orange-600 transition shadow-lg shadow-slate-900/10">
          <div class="relative">
            <ShoppingCart class="w-6 h-6" />
            <span v-if="cartCount > 0" class="absolute -top-3 -right-3 bg-orange-500 text-white text-[10px] font-black rounded-full w-5 h-5 flex items-center justify-center border-2 border-slate-900">
              {{ cartCount }}
            </span>
          </div>
          <span class="hidden md:block font-black text-xs uppercase tracking-widest">Panier</span>
        </router-link>
      </div>
    </div>

    <nav class="hidden lg:block bg-white border-t border-slate-100 relative">
      <div class="container mx-auto px-4 flex items-center">

        <div class="relative">
          <button
            @mouseenter="isMegaMenuOpen = true"
            class="flex items-center gap-3 px-8 py-4 bg-orange-500 text-white font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition"
          >
            <Grid class="w-4 h-4" /> Rayons
          </button>

          <transition name="fade">
            <div
              v-if="isMegaMenuOpen"
              class="absolute top-full left-0 w-[800px] bg-white shadow-2xl rounded-b-[2rem] p-8 grid grid-cols-3 gap-6 border border-slate-100 z-[70]"
              @mouseleave="isMegaMenuOpen = false"
            >
              <div v-for="cat in categories" :key="cat.id">
                <router-link
                  :to="{ name: 'category', params: { id: cat.id, slug: cat.slug || 'shop' } }"
                  class="flex items-center gap-3 p-3 rounded-xl hover:bg-orange-50 text-slate-700 hover:text-orange-600 transition-all group"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-slate-200 group-hover:bg-orange-500"></span>
                  <span class="font-black text-[11px] uppercase italic tracking-tight">{{ cat.name }}</span>
                </router-link>
              </div>
            </div>
          </transition>
        </div>

        <div class="flex items-center ml-4">
          <router-link
            v-for="cat in categories.slice(0, 6)"
            :key="cat.id"
            :to="{ name: 'category', params: { id: cat.id, slug: cat.slug || 'shop' } }"
            class="px-5 py-4 text-[11px] font-black text-slate-500 hover:text-orange-500 uppercase tracking-tighter transition group flex items-center gap-1"
          >
            {{ cat.name }}
            <span class="w-1 h-1 bg-slate-200 rounded-full group-hover:bg-orange-400"></span>
          </router-link>
        </div>

        <router-link to="/admin/products" class="ml-auto flex items-center gap-2 text-[10px] font-black text-slate-400 hover:text-slate-900 transition uppercase tracking-widest">
          <Settings class="w-3.5 h-3.5" /> Dashboard
        </router-link>
      </div>
    </nav>
  </header>
  <div class="h-[140px] lg:h-[160px]"></div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Phone, Truck, User, Heart, ShoppingCart, Menu, Search, Grid, Settings } from 'lucide-vue-next'
import { useCartStore } from '@/stores/cartStore'
import { useCategoryStore } from '@/stores/categoryStore'
import { useWishlistStore } from '@/stores/wishlistStore'
import SearchAutocomplete from '@/Components/catalog/SearchAutocomplete.vue'

defineEmits(['open-menu'])

const categoryStore = useCategoryStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

const isMegaMenuOpen = ref(false)
const categories = computed(() => categoryStore.categories)
const cartCount = computed(() => cartStore.totalItems)
const wishlistCount = computed(() => wishlistStore.items.length)
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: all 0.2s ease-out;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
