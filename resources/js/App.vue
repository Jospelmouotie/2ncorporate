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

<script setup>
import { ref, onMounted } from 'vue'
import Header from '@/Components/layouts/Header.vue'
import Footer from '@/Components/layouts/Footer.vue'
import Sidebar from '@/Components/layouts/Sidebar.vue'
import MobileMenu from '@/Components/layouts/MobileMenu.vue'
import ActionModal from '@/Components/ui/ActionModal.vue'
import Toast from '@/Components/ui/Toast.vue'

import { useCartStore } from '@/stores/cartStore'
import { useWishlistStore } from '@/stores/wishlistStore'
import { useCategoryStore } from '@/stores/categoryStore'

const isMenuOpen = ref(false)
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const categoryStore = useCategoryStore()

onMounted(async () => {
  await Promise.all([
    cartStore.fetchCart(),
    wishlistStore.fetchWishlist(),
    categoryStore.fetchCategories()
  ])
})
</script>

<style>
.page-enter-active, .page-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.page-enter-from { opacity: 0; transform: translateY(10px); }
.page-leave-to { opacity: 0; transform: translateY(-10px); }
</style>
