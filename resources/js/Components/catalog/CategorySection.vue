<template>
  <section class="w-full mt-10 md:mt-20 overflow-hidden">
    <div class="md:hidden px-4">
      <button
        @click="isMobileMenuOpen = !isMobileMenuOpen"
        class="w-full flex items-center justify-between p-5 bg-white rounded-2xl shadow-sm border border-gray-100"
      >
        <div class="flex items-center gap-3">
          <span class="text-xl">📁</span>
          <span class="font-black text-gray-800 uppercase tracking-widest text-sm">Voir les catégories</span>
        </div>
        <span
          class="transition-transform duration-300 text-orange-500 text-xl"
          :class="{ 'rotate-180': isMobileMenuOpen }"
        >
          ▼
        </span>
      </button>

      <transition name="slide-down">
        <div v-if="isMobileMenuOpen" class="grid grid-cols-2 gap-3 mt-4">
          <router-link
            v-for="cat in categories"
            :key="cat.id"
            :to="{ name: 'category', params: { id: cat.id, slug: cat.slug } }"
            class="flex flex-col items-center p-4 bg-white rounded-2xl border border-gray-50 shadow-sm active:scale-95 transition-transform"
          >
            <img :src="cat.image_url || '/storage/placeholder.png'" class="w-12 h-12 object-contain mb-2" />
            <span class="text-[10px] font-bold text-gray-700 text-center uppercase">{{ cat.name }}</span>
          </router-link>
        </div>
      </transition>
    </div>

    <div class="hidden md:block relative">
      <div class="flex items-center gap-4 mb-8 px-10">
        <div class="h-8 w-2 bg-orange-500 rounded-full"></div>
        <h2 class="text-2xl font-black text-gray-800 uppercase italic">Nos Univers</h2>
      </div>

      <div class="marquee-container group bg-white py-10 border-y border-gray-50">
        <div class="marquee-content flex gap-10">
          <router-link
            v-for="cat in [...categories, ...categories]"
            :key="cat.id + Math.random()"
            :to="{ name: 'category', params: { id: cat.id, slug: cat.slug } }"
            class="flex-shrink-0 flex items-center gap-4 px-8 py-4 bg-gray-50 rounded-full hover:bg-orange-600 hover:text-white transition-all duration-300 group/item hover:-translate-y-1 hover:shadow-lg"
          >
            <div class="w-12 h-12 rounded-full overflow-hidden bg-white border border-gray-100 shadow-sm">
              <img :src="cat.image_url || '/storage/placeholder.png'" class="w-full h-full object-cover" />
            </div>
            <span class="font-black uppercase italic tracking-tighter text-lg whitespace-nowrap">
              {{ cat.name }}
            </span>
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  categories: {
    type: Array,
    required: true
  }
})

const isMobileMenuOpen = ref(false)
</script>

<style scoped>
/* --- ANIMATION DÉFILEMENT (DESKTOP) --- */
.marquee-container {
  display: flex;
  overflow: hidden;
  user-select: none;
}

.marquee-content {
  display: flex;
  animation: scroll 35s linear infinite;
  min-width: 100%;
}

/* Pause au survol pour faciliter le clic */
.marquee-container:hover .marquee-content {
  animation-play-state: paused;
}

@keyframes scroll {
  from { transform: translateX(0); }
  to { transform: translateX(-50%); }
}

/* --- ANIMATION ACCORDÉON (MOBILE) --- */
.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  max-height: 1000px;
}
.slide-down-enter-from, .slide-down-leave-to {
  opacity: 0;
  max-height: 0;
  transform: translateY(-10px);
}

/* Suppression de la barre de défilement si nécessaire */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
</style>
