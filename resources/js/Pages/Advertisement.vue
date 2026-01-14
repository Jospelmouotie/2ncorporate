<template>
  <div
    class="relative w-full h-full overflow-hidden bg-slate-900 rounded-[2.5rem] md:rounded-[3.5rem] shadow-2xl group border border-slate-800"
  >
    <TransitionGroup name="fade">
      <div
        v-for="(ad, i) in ads"
        :key="ad.id"
        v-show="i === currentIndex"
        class="absolute inset-0"
      >
        <template v-if="isVideo(ad.media_url)">
          <video
            :src="ad.media_url"
            autoplay
            loop
            muted
            playsinline
            class="w-full h-full object-cover opacity-50 transition-transform duration-[12s] group-hover:scale-105"
          ></video>
        </template>
        <template v-else>
          <img
            :src="ad.media_url"
            class="w-full h-full object-cover opacity-60 transition-transform duration-[12s] group-hover:scale-105"
          />
        </template>

        <div
          class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/40 to-transparent"
        ></div>

        <div
          class="absolute inset-0 flex flex-col justify-center p-8 md:p-16 space-y-6 z-10 max-w-[85%]"
        >
          <div class="flex items-center gap-3">
             <span class="w-8 h-[2px] bg-orange-500"></span>
             <span class="text-orange-500 text-[10px] md:text-xs font-black uppercase tracking-[0.3em]">
               2Ncorporate Exclusive
             </span>
          </div>

          <h1
            class="text-4xl md:text-7xl font-black text-white leading-[0.9] italic uppercase tracking-tighter"
          >
            {{ ad.title }}
          </h1>

          <p class="text-sm md:text-xl text-slate-300 font-medium max-w-lg line-clamp-2 italic">
            {{ ad.description }}
          </p>

          <div class="pt-6">
            <button
              class="group/btn relative bg-orange-500 text-slate-900 font-black py-4 px-10 rounded-2xl hover:bg-white transition-all shadow-xl shadow-orange-500/20 uppercase text-xs tracking-widest active:scale-95 flex items-center gap-3"
            >
              Voir l'offre
              <ArrowRight class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
            </button>
          </div>
        </div>
      </div>
    </TransitionGroup>

    <div class="absolute bottom-10 left-10 flex items-center gap-3 z-20">
      <div
        v-for="(_, i) in ads"
        :key="i"
        @click="currentIndex = i"
        class="h-1.5 rounded-full cursor-pointer transition-all duration-500"
        :class="
          i === currentIndex
            ? 'bg-orange-500 w-12'
            : 'bg-white/20 w-3 hover:bg-white/50'
        "
      ></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { ArrowRight } from "lucide-vue-next";

const props = defineProps({ ads: Array });
const currentIndex = ref(0);
let timer = null;

// Fonction pour détecter si le média est une vidéo
const isVideo = (url) => {
  if (!url) return false;
  const videoExtensions = ['.mp4', '.webm', '.ogg', '.mov'];
  return videoExtensions.some(ext => url.toLowerCase().endsWith(ext));
};

const startTimer = () => {
  if (props.ads.length > 1) {
    timer = setInterval(() => {
      currentIndex.value = (currentIndex.value + 1) % props.ads.length;
    }, 8000);
  }
};

onMounted(() => {
  startTimer();
});

onUnmounted(() => clearInterval(timer));
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 1.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.fade-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}

/* Scrollbar hide pour mobile au cas où */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
