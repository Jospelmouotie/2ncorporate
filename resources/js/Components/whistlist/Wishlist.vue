<template>
  <div class="min-h-screen bg-slate-50">
    <div class="container mx-auto px-4 py-10">
      <div class="mb-8 flex justify-between items-end">
        <div>
          <h1 class="text-3xl font-black text-slate-900 uppercase">Mes Favoris</h1>
          <p class="text-slate-500">Articles sauvegardés dans votre session ou compte.</p>
        </div>
        <div class="text-orange-600 font-bold">{{ wishlistStore.count }} Articles</div>
      </div>

      <div class="mb-8 max-w-md">
        <SearchBar v-model="localSearch" />
      </div>

      <div v-if="wishlistStore.count > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
        <ProductCard
          v-for="product in filteredWishlist"
          :key="product.id"
          :product="product"
        />
      </div>

      <CartEmpty v-else />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useWishlistStore } from '@/stores/wishlistStore'
import { SearchBar } from '@/Components/ui/index.js'
import ProductCard from '@/Components/ProductCard.vue'
import CartEmpty from '@/Components/cart/CartEmpty.vue'

const wishlistStore = useWishlistStore()
const localSearch = ref('')

const filteredWishlist = computed(() => {
  return wishlistStore.items.filter(item =>
    item.name.toLowerCase().includes(localSearch.value.toLowerCase())
  )
})

onMounted(() => {
  wishlistStore.fetchWishlist()
})
</script>
