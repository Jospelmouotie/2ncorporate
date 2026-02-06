<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue' // AJOUTEZ onUnmounted ici
import { useRouter } from 'vue-router'
import { useCategoryStore } from '@/stores/categoryStore.js'
import {
  Package, Grid, Zap, Tv, ChevronRight,
  LayoutDashboard, Settings, LogIn, LogOut, User, Shield,
  Home, ShoppingBag, Heart, UserPlus
} from 'lucide-vue-next'
import SidebarLink from './SidebarLink.vue'
import axios from 'axios'

const router = useRouter()
const categoryStore = useCategoryStore()

// État réactif
const user = ref(window.user || null)
const isAdmin = computed(() => {
  return user.value?.is_admin === true || user.value?.is_admin === 1
})

// Fonction pour rafraîchir l'utilisateur
const refreshUser = async () => {
  try {
    const response = await axios.get('/api/user')
    user.value = response.data
    window.user = response.data
    console.log('User refreshed in sidebar:', user.value)
  } catch (error) {
    console.log('Not authenticated')
    user.value = null
    window.user = null
  }
}

const handleLogout = async () => {
  try {
    await axios.post('/api/logout')
    user.value = null
    window.user = null
    window.location.href = '/'
  } catch (error) {
    console.error('Logout error:', error)
    window.location.href = '/logout'
  }
}

// Surveille les changements de route pour rafraîchir l'utilisateur
router.afterEach(async () => {
  await refreshUser()
})

// Écouter les événements personnalisés
const handleUserUpdate = (event) => {
  user.value = event.detail.user
  console.log('User updated via event:', user.value)
}

onMounted(async () => {
  await categoryStore.fetchCategories()

  // Rafraîchir l'utilisateur au montage
  await refreshUser()

  // Écouter les événements de mise à jour
  window.addEventListener('user-updated', handleUserUpdate)

  // Écouter les messages depuis d'autres onglets (si besoin)
  window.addEventListener('storage', (event) => {
    if (event.key === 'user') {
      try {
        user.value = JSON.parse(event.newValue)
      } catch (e) {
        user.value = null
      }
    }
  })

  console.log('Sidebar mounted - User:', user.value)
  console.log('Sidebar mounted - isAdmin:', isAdmin.value)
})

// Nettoyage
onUnmounted(() => {
  window.removeEventListener('user-updated', handleUserUpdate)
})
</script>

<template>
  <aside class="bg-white w-72 min-h-[calc(100vh-130px)] border-r border-gray-200 hidden lg:flex flex-col sticky top-[130px]">
    <!-- DEBUG INFO (temporaire - à retirer en production) -->
    <div v-if="false" class="bg-yellow-50 p-3 border-b border-yellow-200">
      <div class="text-xs space-y-1">
        <div><strong>Debug Info:</strong></div>
        <div>User exists: <span :class="user ? 'text-green-600 font-bold' : 'text-red-600'">{{ !!user }}</span></div>
        <div>is_admin value: <span :class="user?.is_admin ? 'text-green-600 font-bold' : 'text-red-600'">{{ user?.is_admin }}</span></div>
        <div>isAdmin computed: <span :class="isAdmin ? 'text-green-600 font-bold' : 'text-red-600'">{{ isAdmin }}</span></div>
        <div>Window.user: <span :class="window.user ? 'text-green-600' : 'text-red-600'">{{ window.user ? 'Present' : 'Absent' }}</span></div>
      </div>
    </div>

    <!-- En-tête -->
    <div class="p-6 border-b border-gray-200">
      <div class="flex flex-col">
        <span class="font-black text-2xl tracking-tighter text-gray-900">
          2N<span class="text-orange-500">Corporate</span>
        </span>

        <!-- Badge admin -->
        <div v-if="isAdmin && user" class="flex items-center gap-2 mt-2">
          <Shield class="w-4 h-4 text-orange-500" />
          <span class="text-orange-600 text-xs font-bold uppercase tracking-widest">
            Administration
          </span>
        </div>

        <!-- Message si non connecté -->
        <div v-if="!user" class="mt-2">
          <span class="text-sm text-gray-500 italic">Connectez-vous pour accéder à votre espace</span>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
      <!-- SECTION ADMIN -->
      <div v-if="isAdmin && user" class="mb-8">
        <h4 class="px-4 text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">
          Administration
        </h4>
        <div class="space-y-1">
          <SidebarLink to="/admin" :icon="LayoutDashboard" :active="$route.path === '/admin'">
            Tableau de bord
          </SidebarLink>
          <SidebarLink to="/admin/products" :icon="Package" :active="$route.path.includes('/admin/products')">
            Produits
          </SidebarLink>
          <SidebarLink to="/admin/categories" :icon="Grid" :active="$route.path.includes('/admin/categories')">
            Catégories
          </SidebarLink>
          <SidebarLink to="/admin/promotions" :icon="Zap" :active="$route.path.includes('/admin/promotions')">
            Promotions
          </SidebarLink>
          <SidebarLink to="/admin/advertisements" :icon="Tv" :active="$route.path.includes('/admin/advertisements')">
            Publicités
          </SidebarLink>
          <SidebarLink to="/admin/settings" :icon="Settings">
            Paramètres
          </SidebarLink>
        </div>
      </div>

      <!-- SECTION BOUTIQUE -->
      <div class="mb-8">
        <h4 class="px-4 text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">
          Boutique
        </h4>
        <div class="space-y-1">
          <SidebarLink to="/" :icon="Home" :active="$route.path === '/'">
            Accueil
          </SidebarLink>
          <SidebarLink to="/shop" :icon="ShoppingBag" :active="$route.path.includes('/shop')">
            Tous les produits
          </SidebarLink>
          <SidebarLink to="/promotions" :icon="Zap" :active="$route.path.includes('/promotions')">
            Promotions
          </SidebarLink>
        </div>
      </div>

      <!-- SECTION CATÉGORIES -->
      <div class="mb-8">
        <h4 class="px-4 text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">
          Catégories
        </h4>
        <div class="max-h-[300px] overflow-y-auto space-y-1">
          <router-link
            v-for="cat in categoryStore.categories"
            :key="cat.id"
            :to="{ name: 'category', params: { id: cat.id, slug: cat.slug || 'shop' } }"
            class="flex items-center justify-between px-4 py-3 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-xl transition-all group"
            :class="{ 'text-orange-500 bg-orange-50': $route.params.id === cat.id.toString() }"
          >
            <span class="text-sm font-medium">{{ cat.name }}</span>
            <ChevronRight class="w-4 h-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
          </router-link>
        </div>
      </div>

      <!-- SECTION COMPTE -->
      <div>
        <h4 class="px-4 text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">
          Mon compte
        </h4>
        <div class="space-y-1">
          <SidebarLink v-if="user" to="/profile" :icon="User" :active="$route.path.includes('/profile')">
            Mon profil
          </SidebarLink>
          <SidebarLink v-if="user" to="/orders" :icon="Package" :active="$route.path.includes('/orders')">
            Mes commandes
          </SidebarLink>
          <SidebarLink v-if="user" to="/wishlist" :icon="Heart" :active="$route.path.includes('/wishlist')">
            Ma liste d'envies
          </SidebarLink>
          <SidebarLink v-if="!user" to="/login" :icon="LogIn" :active="$route.path.includes('/login')">
            Connexion
          </SidebarLink>
          <SidebarLink v-if="!user" to="/register" :icon="UserPlus" :active="$route.path.includes('/register')">
            Inscription
          </SidebarLink>
        </div>
      </div>
    </nav>

    <!-- FOOTER -->
    <div class="p-4 border-t border-gray-200 bg-gray-50 mt-auto">
      <div v-if="user" class="space-y-3">
        <!-- Info utilisateur -->
        <div class="flex items-center gap-3 px-3 py-2 bg-white rounded-lg shadow-sm">
          <div class="w-10 h-10 rounded-full bg-gradient-to-r from-orange-100 to-orange-50 border-2 border-orange-200 flex items-center justify-center">
            <User class="w-5 h-5 text-orange-600" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900 truncate">
              {{ user.name }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
              <span v-if="isAdmin" class="inline-flex items-center gap-1 text-orange-600 font-semibold bg-orange-50 px-2 py-0.5 rounded-full">
                <Shield class="w-3 h-3" />
                Administrateur
              </span>
              <span v-else class="text-gray-500">Client</span>
            </p>
          </div>
        </div>

        <!-- Bouton déconnexion -->
        <button
          @click="handleLogout"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:text-white hover:bg-red-500 rounded-lg transition-all font-semibold border border-red-200 hover:border-red-500"
        >
          <LogOut class="w-4 h-4" />
          Déconnexion
        </button>
      </div>

      <!-- Non connecté -->
      <div v-else class="space-y-2">
        <router-link
          to="/login"
          class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all font-bold text-sm shadow-md hover:shadow-lg"
        >
          <LogIn class="w-4 h-4" />
          Se connecter
        </router-link>

        <router-link
          to="/register"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all text-sm font-medium border border-gray-200 hover:border-orange-200"
        >
          Créer un compte
        </router-link>
      </div>
    </div>
  </aside>
</template>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Animation pour le badge admin */
@keyframes pulse-glow {
  0%, 100% {
    box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4);
  }
  50% {
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0);
  }
}

.admin-badge {
  animation: pulse-glow 2s infinite;
}
</style>
