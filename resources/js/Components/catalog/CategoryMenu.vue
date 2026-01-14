<template>
  <nav class="bg-white shadow rounded-xl p-4">
    <ul class="flex flex-wrap gap-3">
      <li
        v-for="cat in categories"
        :key="cat.id"
      >
        <button
          @click="$emit('select', cat.id)"
          class="px-4 py-2 rounded-full font-medium transition
          hover:bg-orange-100"
          :class="{
            'bg-orange-500 text-white': cat.id === active
          }"
        >
          {{ cat.name }}
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup>
defineProps({
  categories: Array,
  active: Number
})
</script>
