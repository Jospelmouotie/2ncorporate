<template>
  <div :class="[color]" class="p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-5 transition-transform hover:scale-[1.02]">
    <div :class="[iconColor, color === 'bg-slate-900' ? 'bg-slate-800' : 'bg-slate-50']" class="p-4 rounded-2xl">
      <component :is="LucideIcons[icon]" class="w-6 h-6" />
    </div>
    <div>
      <p :class="[color === 'bg-slate-900' ? 'text-slate-400' : 'text-slate-500']" class="text-[10px] font-black uppercase tracking-widest">{{ title }}</p>
      <p :class="[color === 'bg-slate-900' ? 'text-white' : 'text-slate-900']" class="text-xl font-black tracking-tighter">{{ value }}</p>
    </div>
  </div>
</template>

<script setup>
import * as LucideIcons from 'lucide-vue-next'
defineProps(['title', 'value', 'icon', 'color', 'iconColor'])
</script>
