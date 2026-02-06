<template>
  <div class="fixed right-0 top-0 h-full w-80 bg-white shadow-2xl z-50 p-6 overflow-y-auto">
    <h2 class="text-2xl font-bold mb-6">Mon Panier</h2>
    <div v-for="item in cartItems" :key="item.id" class="flex gap-4 mb-4 border-b pb-4">
      <img :src="item.product.image_url" class="w-16 h-16 object-cover rounded">
      <div class="flex-1">
        <h4 class="font-medium text-sm">{{ item.product.name }}</h4>
        <p class="text-gray-500 text-xs">Qté: {{ item.quantity }}</p>
        <p class="font-bold text-blue-600">{{ item.product.price }}€</p>
      </div>
      <button @click="removeItem(item.id)" class="text-red-500">×</button>
    </div>
    <div class="mt-6 border-t pt-4">
      <div class="flex justify-between text-xl font-bold">
        <span>Total:</span>
        <span>{{ cartTotal }}€</span>
      </div>
      <button class="w-full bg-black text-white py-3 mt-4 rounded-lg hover:bg-gray-800">Commander</button>
    </div>
  </div>
</template>
