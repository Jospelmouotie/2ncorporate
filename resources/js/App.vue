<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Header from '@/Components/layouts/Header.vue'
import Footer from '@/Components/layouts/Footer.vue'
import Sidebar from '@/Components/layouts/Sidebar.vue'
import MobileMenu from '@/Components/layouts/MobileMenu.vue'
import ActionModal from '@/Components/ui/ActionModal.vue'
import Toast from '@/Components/ui/Toast.vue'

import { useCartStore } from '@/stores/cartStore'
import { useWishlistStore } from '@/stores/wishlistStore'
import { useCategoryStore } from '@/stores/categoryStore'
import { useUserStore } from '@/stores/userStore' // Importez le store

const router = useRouter()
const isMenuOpen = ref(false)

// Stores
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const categoryStore = useCategoryStore()
const userStore = useUserStore() // Initialisez le store

onMounted(async () => {
  console.log('App mounted, initial user:', window.user)

  // 1. Charger les catégories
  await categoryStore.fetchCategories()

  // 2. Rafraîchir l'utilisateur (important pour les sessions existantes)
  await userStore.fetchUser()

  console.log('After fetch, user:', userStore.user)

  // 3. Si connecté, charger les données privées
  if (userStore.user) {
    try {
      await Promise.all([
        cartStore.fetchCart(),
        wishlistStore.fetchWishlist()
      ])
    } catch (e) {
      console.warn("Erreur lors du chargement des données privées:", e)
    }
  }

  // 4. Rafraîchir l'utilisateur à chaque changement de route
  router.afterEach(async () => {
    await userStore.fetchUser()
  })
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50 text-gray-800 font-sans">
    <Header @open-menu="isMenuOpen = true" />

    <div class="flex flex-1 pt-[140px] lg:pt-[130px]">
      <Sidebar class="hidden lg:block w-72 shrink-0 border-r border-gray-200 bg-white" />

      <main class="flex-1 w-full min-w-0 overflow-x-hidden">
        <div class="container mx-auto p-4 md:p-8">
          <router-view v-slot="{ Component }">
            <transition name="page" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </div>
      </main>
    </div>

    <ActionModal />
    <Toast />

    <MobileMenu
      :open="isMenuOpen"
      :categories="categoryStore.categories"
      @close="isMenuOpen = false"
    />

    <Footer v-if="$route.meta.showFooter !== false" />
  </div>
</template>
