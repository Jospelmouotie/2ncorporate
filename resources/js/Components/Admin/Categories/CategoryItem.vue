<template>
  <tr class="border-b border-gray-50 hover:bg-gray-50/80 transition">
    <td class="px-6 py-4 font-mono text-xs text-gray-400">#{{ category.id }}</td>
    <td class="px-6 py-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden border">
          <img v-if="category.image_url" :src="category.image_url" class="w-full h-full object-cover">
          <div v-else class="w-full h-full flex items-center justify-center text-lg">📁</div>
        </div>
        <span class="font-bold text-gray-800">{{ category.name }}</span>
      </div>
    </td>
    <td class="px-6 py-4">
      <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] font-black uppercase tracking-tighter">
        {{ category.slug }}
      </span>
    </td>
    <td class="px-6 py-4 text-right">
      <div class="flex gap-2 justify-end">
        <button @click="$emit('edit', category)" class="p-2 bg-blue-50 text-blue-600 rounded-lg">✏️</button>
        <button @click="$emit('delete', category.id)" class="p-2 bg-red-50 text-red-500 rounded-lg">🗑️</button>
      </div>
    </td>
  </tr>
</template>

<script setup>
defineProps({ category: Object })
defineEmits(['edit', 'delete'])
</script>
