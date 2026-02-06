<template>
  <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow relative">
    <img :src="promotion.image_url || 'https://via.placeholder.com/300x150'" class="w-full h-32 object-cover rounded-md mb-2" />
    <h3 class="font-semibold text-lg">{{ promotion.title }}</h3>
    <p class="text-sm text-gray-500">
      Du {{ new Date(promotion.start_at).toLocaleDateString() }} au {{ new Date(promotion.end_at).toLocaleDateString() }}
    </p>
    <div class="flex gap-2 mt-2">
      <button @click="$emit('edit', promotion)" class="px-3 py-1 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">Éditer</button>
      <button @click="$emit('delete', promotion.id)" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">Supprimer</button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  promotion: Object
})
</script>
