<script setup>
import ProductGrid from './ProductGrid.vue'
defineProps({ products: Array })
</script>

<template>
  <section>
    <h3 class="text-xl font-black mb-6">Produits similaires</h3>
    <ProductGrid :products="products" />
  </section>
</template>
