<template>
  <transition name="slide-fade">
    <aside v-if="visible" class="fixed top-0 right-0 w-80 md:w-96 h-full bg-white shadow-2xl z-[100] flex flex-col">
      <CartHeader @close="closeSidebar" :count="cart.items.length" />

      <div class="flex-1 overflow-y-auto p-4 space-y-4">
        <CartEmpty v-if="cart.items.length === 0" />
        <CartItem
          v-for="item in cart.items"
          :key="item.id"
          :product="item"
          @remove="removeItem(item.id)"
        />
      </div>

      <CartSummary v-if="cart.items.length > 0" />
    </aside>
  </transition>

  <div v-if="visible" @click="closeSidebar" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[90]"></div>
</template>

<script setup>
import { ref } from 'vue'
import { useCartStore } from '@/stores/cartStore'
import { CartHeader, CartItem, CartSummary, CartEmpty } from './index.js'

const cart = useCartStore()
const visible = ref(false)

// Fonctions exposées pour le parent
const openSidebar = () => { visible.value = true }
const closeSidebar = () => { visible.value = false }

// On expose les fonctions pour pouvoir les appeler via une ref
defineExpose({ openSidebar, closeSidebar })

const removeItem = (id) => {
  cart.items = cart.items.filter(item => item.id !== id)
}
</script>

<style scoped>
.slide-fade-enter-active, .slide-fade-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-fade-enter-from, .slide-fade-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>
