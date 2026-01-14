<script setup>
import { ref } from 'vue'
const props = defineProps({ images: Array })
const active = ref(props.images?.[0]?.url)
</script>

<template>
  <div>
    <img :src="active" class="w-full h-96 object-cover rounded-2xl" />

    <div class="flex gap-3 mt-4">
      <img
        v-for="img in images"
        :key="img.id"
        :src="img.url"
        @click="active = img.url"
        class="w-20 h-20 object-cover rounded-xl cursor-pointer border"
      />
    </div>
  </div>
</template>
