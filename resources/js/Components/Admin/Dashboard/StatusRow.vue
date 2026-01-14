<template>
  <div class="space-y-2">
    <div class="flex justify-between items-end">
      <div class="flex flex-col">
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">
          Statut
        </span>
        <span class="text-sm font-bold text-slate-700 uppercase italic">
          {{ label }}
        </span>
      </div>
      <div class="text-right">
        <span class="text-lg font-black text-slate-900 leading-none">
          {{ count }}
        </span>
        <span class="text-[10px] font-bold text-slate-400 ml-1 uppercase">
          Commandes
        </span>
      </div>
    </div>

    <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
      <div
        class="h-full transition-all duration-1000 ease-out rounded-full"
        :class="color"
        :style="{ width: `${percentage}%` }"
      ></div>
    </div>

    <div class="flex justify-end">
      <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">
        {{ percentage }}% du volume total
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  count: {
    type: Number,
    default: 0
  },
  total: {
    type: Number,
    default: 0
  },
  color: {
    type: String,
    default: 'bg-orange-500'
  }
})

const percentage = computed(() => {
  if (props.total <= 0) return 0
  return Math.round((props.count / props.total) * 100)
})
</script>

<style scoped>
@keyframes slideIn {
  from { transform: translateX(-100%); }
  to { transform: translateX(0); }
}

.h-full {
  animation: slideIn 1.5s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
