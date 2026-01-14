<template>
  <router-link
    :to="to"
    class="flex items-center justify-between px-4 py-3 rounded-2xl font-bold transition-all group border border-transparent"
    :class="[
      isActive
        ? 'bg-orange-50 text-orange-600 shadow-sm border-orange-100'
        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 hover:border-slate-100'
    ]"
  >
    <div class="flex items-center gap-4">
      <component
        :is="icon"
        class="w-5 h-5 transition-colors"
        :class="isActive ? 'text-orange-500' : 'text-slate-400 group-hover:text-orange-500'"
      />
      <span class="tracking-tight text-sm uppercase italic"><slot /></span>
    </div>

    <div
      v-if="isActive"
      class="w-1.5 h-1.5 bg-orange-500 rounded-full shadow-[0_0_8px_rgba(249,115,22,0.6)]"
    ></div>
  </router-link>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  to: { type: String, required: true },
  icon: { type: [Object, Function], required: true }
})

const route = useRoute()
const isActive = computed(() => route.path === props.to)
</script>
