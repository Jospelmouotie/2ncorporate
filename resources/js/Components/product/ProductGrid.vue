<template>
  <div class="container mx-auto px-4 py-8">
    <div v-if="products.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
        @click="$router.push(`/product/${product.id}`)"
        @add-to-cart="handleAddToCart"
      />
    </div>
    <div v-else class="text-center py-20 text-gray-500">Aucun produit trouvé.</div>
  </div>
</template>
