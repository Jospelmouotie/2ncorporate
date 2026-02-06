<template>
  <header
    class="fixed top-0 left-0 w-full z-[60] bg-white/95 backdrop-blur-md shadow-md border-b border-gray-100"
    @mouseleave="isMegaMenuOpen = false"
  >
    <!-- Top Bar -->
    <div class="bg-gradient-to-r from-slate-900 to-gray-900 text-white py-1.5 text-[11px] border-b border-orange-500/20">
      <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
          <span class="flex items-center gap-1.5 text-slate-300 font-medium hover:text-orange-400 transition-colors cursor-pointer">
            <Phone class="w-3 h-3 text-orange-500" /> Support: (+237) 123-456-789
          </span>
          <span class="hidden md:flex items-center gap-1.5 text-slate-300 font-medium hover:text-orange-400 transition-colors cursor-pointer">
            <Clock class="w-3 h-3 text-orange-500" /> 7j/7 • 8h-20h
          </span>
        </div>
        <div class="flex items-center gap-2 font-black uppercase tracking-widest text-[10px] text-orange-400 animate-pulse">
          <Truck class="w-4 h-4" /> Livraison Express • 48h Cameroun
        </div>
      </div>
    </div>

    <!-- Main Header -->
    <div class="container mx-auto px-4 py-3 flex items-center gap-4 md:gap-6">
      <!-- Logo & Mobile Menu -->
      <div class="flex items-center gap-3">
        <button @click="$emit('open-menu')" class="lg:hidden p-2 hover:bg-orange-50 rounded-xl transition">
          <Menu class="w-7 h-7 text-slate-800" />
        </button>
        <router-link to="/" class="flex flex-col group">
          <span class="font-black text-2xl md:text-3xl tracking-tighter text-slate-900 leading-none">
            2N<span class="text-orange-500 animate-pulse">Corporate</span>
          </span>
          <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em] group-hover:text-orange-500 transition-colors">Business differently</span>
        </router-link>
      </div>

      <!-- Search Bar -->
      <div class="hidden lg:flex flex-1 max-w-2xl">
        <div class="w-full flex items-center bg-white rounded-2xl px-4 py-1.5 focus-within:bg-white focus-within:ring-2 focus-within:ring-orange-500 transition-all border border-gray-200 focus-within:border-orange-500 shadow-sm">
          <SearchAutocomplete 
            class="flex-1" 
            @search="handleSearch"
            @clear="handleClearSearch"
          />
          <Search class="w-5 h-5 text-slate-400 ml-2" />
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-1 md:gap-3 ml-auto">
        <!-- Wishlist -->
        <router-link to="/wishlist" class="flex items-center gap-2 p-2.5 rounded-2xl hover:bg-orange-50 transition group relative" title="Ma liste d'envies">
          <Heart class="w-6 h-6 text-slate-700 group-hover:text-orange-500 group-hover:fill-orange-500 transition-all" />
          <span v-if="wishlistCount > 0" class="absolute top-1 right-1 bg-orange-500 text-white text-[10px] font-black rounded-full w-4 h-4 flex items-center justify-center border-2 border-white animate-bounce">
            {{ wishlistCount }}
          </span>
        </router-link>

        <!-- Cart -->
        <router-link to="/cart" class="flex items-center gap-2 bg-gradient-to-r from-slate-900 to-gray-800 text-white p-2.5 md:px-5 rounded-2xl hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg shadow-orange-500/20 hover:shadow-orange-500/30" title="Mon panier">
          <div class="relative">
            <ShoppingCart class="w-6 h-6" />
            <span v-if="cartCount > 0" class="absolute -top-3 -right-3 bg-orange-500 text-white text-[10px] font-black rounded-full w-5 h-5 flex items-center justify-center border-2 border-white animate-pulse">
              {{ cartCount }}
            </span>
          </div>
          <span class="hidden md:block font-black text-xs uppercase tracking-widest">Panier</span>
        </router-link>

        <!-- User -->
        <div class="relative ml-2 border-l border-gray-100 pl-3">
          <div v-if="currentUser" class="flex items-center gap-2">
            <div class="hidden md:flex flex-col items-end leading-none mr-1">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Bonjour,</span>
              <span class="text-xs font-black text-slate-900 truncate max-w-[120px]">{{ currentUser.name }}</span>
            </div>
            <div class="relative group">
              <div class="w-10 h-10 rounded-full bg-gradient-to-r from-orange-100 to-orange-50 border-2 border-orange-200 flex items-center justify-center overflow-hidden cursor-pointer">
                <User class="w-5 h-5 text-orange-600" />
                <div v-if="currentUser.is_admin" class="absolute -top-1 -right-1 w-4 h-4 bg-orange-500 rounded-full flex items-center justify-center">
                  <Shield class="w-2.5 h-2.5 text-white" />
                </div>
              </div>
              <!-- Dropdown Menu -->
              <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                <div class="p-4 border-b border-gray-100">
                  <p class="font-bold text-sm text-gray-900">{{ currentUser.name }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ currentUser.email }}</p>
                  <div v-if="currentUser.is_admin" class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-[10px] font-bold uppercase">
                    <Shield class="w-3 h-3" /> Admin
                  </div>
                </div>
                <div class="p-2">
                  <router-link to="/profile" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 rounded-lg transition">
                    <User class="w-4 h-4" /> Mon profil
                  </router-link>
                  <router-link v-if="currentUser.is_admin" to="/admin" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 rounded-lg transition">
                    <LayoutDashboard class="w-4 h-4" /> Dashboard Admin
                  </router-link>
                  <router-link to="/orders" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 rounded-lg transition">
                    <Package class="w-4 h-4" /> Mes commandes
                  </router-link>
                  <button @click="handleLogout" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition mt-2">
                    <LogOut class="w-4 h-4" /> Déconnexion
                  </button>
                </div>
              </div>
            </div>
          </div>

          <router-link v-else to="/login" class="flex items-center gap-2 p-2.5 bg-gradient-to-r from-orange-50 to-orange-100 text-orange-600 rounded-2xl hover:from-orange-100 hover:to-orange-200 transition shadow-sm hover:shadow-md">
            <User class="w-6 h-6" />
            <span class="hidden md:block font-black text-xs uppercase tracking-widest">Connexion</span>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="hidden lg:block bg-white border-t border-gray-100 relative">
      <div class="container mx-auto px-4 flex items-center">
        <!-- Mega Menu Button -->
        <div class="relative">
          <button @mouseenter="isMegaMenuOpen = true" class="flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-black text-xs uppercase tracking-widest hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg rounded-br-2xl group">
            <Grid class="w-4 h-4 transition-transform group-hover:rotate-90" /> Rayons
          </button>

          <transition name="fade">
            <div v-if="isMegaMenuOpen" class="absolute top-full left-0 w-[800px] bg-white shadow-2xl rounded-b-[2rem] p-8 grid grid-cols-3 gap-6 border border-gray-100 z-[70]" @mouseleave="isMegaMenuOpen = false">
              <div v-for="cat in categories" :key="cat.id">
                <router-link 
                  :to="{ name: 'category', params: { id: cat.id, slug: cat.slug || 'shop' } }" 
                  class="flex items-center gap-3 p-3 rounded-xl hover:bg-gradient-to-r hover:from-orange-50 hover:to-orange-100 text-gray-700 hover:text-orange-600 transition-all group"
                  @click="isMegaMenuOpen = false"
                >
                  <span class="w-2 h-2 rounded-full bg-gray-200 group-hover:bg-orange-500 group-hover:animate-pulse"></span>
                  <span class="font-black text-[11px] uppercase italic tracking-tight">{{ cat.name }}</span>
                </router-link>
              </div>
            </div>
          </transition>
        </div>

        <!-- Navigation Links -->
        <div class="flex items-center ml-4">
          <router-link v-for="cat in categories.slice(0, 6)" :key="cat.id" 
            :to="{ name: 'category', params: { id: cat.id, slug: cat.slug || 'shop' } }" 
            class="px-5 py-4 text-[11px] font-black text-gray-500 hover:text-orange-500 uppercase tracking-tighter transition group flex items-center gap-1 relative"
            active-class="text-orange-500"
          >
            {{ cat.name }}
            <span class="w-1 h-1 bg-gray-200 rounded-full group-hover:bg-orange-400"></span>
            <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-orange-500 group-hover:w-8 transition-all duration-300"></span>
          </router-link>
        </div>

        <!-- Promotions Link -->
        <router-link to="/promotions" class="ml-auto flex items-center gap-2 text-[10px] font-black bg-gradient-to-r from-red-50 to-red-100 text-red-600 hover:text-white hover:from-red-500 hover:to-red-600 px-4 py-2 rounded-xl transition-all uppercase tracking-widest shadow-sm hover:shadow-md">
          <Zap class="w-3.5 h-3.5" /> Ventes Flash
        </router-link>
      </div>
    </nav>
  </header>
  <div class="h-[140px] lg:h-[130px]"></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import {
  Phone, Truck, User, Heart, ShoppingCart,
  Menu, Search, Grid, Settings, LogOut,
  Shield, LayoutDashboard, Package, Zap, Clock
} from "lucide-vue-next";
import { useCartStore } from "@/stores/cartStore";
import { useCategoryStore } from "@/stores/categoryStore";
import { useWishlistStore } from "@/stores/wishlistStore";
import { useSearchStore } from "@/stores/searchStore";
import SearchAutocomplete from "@/Components/catalog/SearchAutocomplete.vue";
import axios from "axios";
import { useRouter } from "vue-router";

defineEmits(["open-menu"]);

const router = useRouter();
const categoryStore = useCategoryStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const searchStore = useSearchStore();

const isMegaMenuOpen = ref(false);
const currentUser = ref(null);

const updateUserInfo = () => {
  // Vérifier multiple sources
  currentUser.value = window.user || window.Laravel?.user || null;
};

const handleSearch = (searchTerm) => {
  searchStore.setSearchTerm(searchTerm);
  if (router.currentRoute.value.name !== 'home') {
    router.push({ name: 'home' });
  }
};

const handleClearSearch = () => {
  searchStore.clearSearch();
};

const handleLogout = async () => {
  if (!confirm("Voulez-vous vraiment vous déconnecter ?")) return;

  try {
    await axios.post("/api/logout");
  } catch (e) {
    console.error("Erreur déconnexion serveur", e);
  } finally {
    window.user = null;
    currentUser.value = null;
    window.dispatchEvent(new Event("user-updated"));
    router.push("/");
  }
};

onMounted(() => {
  updateUserInfo();
  window.addEventListener("user-updated", updateUserInfo);
  onUnmounted(() => {
    window.removeEventListener("user-updated", updateUserInfo);
  });
});

const categories = computed(() => categoryStore.categories);
const cartCount = computed(() => cartStore.totalItems);
const wishlistCount = computed(() => wishlistStore.items.length);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(15px);
}
</style>