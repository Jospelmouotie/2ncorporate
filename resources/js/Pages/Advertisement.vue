<template>
  <div
    class="relative w-full h-full overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-[2.5rem] md:rounded-[4rem] shadow-2xl group border-4 border-white/10"
    @mouseenter="pauseTimer"
    @mouseleave="resumeTimer"
  >
    <!-- Glowing effect -->
    <div class="absolute -top-24 -right-24 w-64 h-64 bg-gradient-to-r from-orange-500/20 to-red-500/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-full blur-3xl"></div>

    <!-- Main content with transitions -->
    <TransitionGroup name="ad-swap" tag="div" class="relative w-full h-full">
      <div
        v-for="(ad, i) in ads"
        :key="ad.id"
        v-show="i === currentIndex"
        class="absolute inset-0 overflow-hidden"
      >
        <!-- Media container with parallax effect -->
        <div class="absolute inset-0 transform-gpu" :style="{ transform: `scale(${1 + parallaxScale})` }">
          <template v-if="isVideo(ad.media_url)">
            <video
              :src="ad.media_url"
              autoplay
              loop
              muted
              playsinline
              class="w-full h-full object-cover transition-transform duration-10000 ease-out"
              :class="{ 'scale-110': parallaxScale > 0 }"
            ></video>
          </template>
          <template v-else>
            <img
              :src="ad.media_url || '/images/placeholder.jpg'"
              :alt="ad.title"
              class="w-full h-full object-cover transition-transform duration-10000 ease-out"
              :class="{ 'scale-110': parallaxScale > 0 }"
              @load="handleImageLoad"
            />
          </template>
        </div>

        <!-- Gradient overlays -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/70 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/50 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/30 via-transparent to-slate-900/50"></div>

        <!-- Floating particles effect -->
        <div class="absolute inset-0 overflow-hidden">
          <div
            v-for="n in 20"
            :key="n"
            class="absolute w-1 h-1 bg-white/10 rounded-full animate-float"
            :style="{
              left: `${Math.random() * 100}%`,
              top: `${Math.random() * 100}%`,
              animationDelay: `${Math.random() * 5}s`,
              animationDuration: `${10 + Math.random() * 20}s`
            }"
          ></div>
        </div>

        <!-- Content container -->
        <div
          class="absolute inset-0 flex flex-col justify-center p-8 md:p-16 space-y-6 z-10 max-w-[85%] lg:max-w-[75%]"
        >
          <!-- Premium badge -->
          <div class="flex items-center gap-3">
            <div class="relative">
              <div class="w-8 h-[2px] bg-gradient-to-r from-orange-500 to-amber-500 rounded-full"></div>
              <div class="absolute inset-0 w-8 h-[2px] bg-gradient-to-r from-orange-500 to-amber-500 rounded-full animate-ping opacity-60"></div>
            </div>
            <span
              class="text-gradient bg-gradient-to-r from-orange-400 to-amber-400 bg-clip-text text-transparent text-[10px] md:text-xs font-black uppercase tracking-[0.3em]"
            >
              PREMIUM COLLECTION
            </span>
          </div>

          <!-- Main title with gradient -->
          <h1
            class="text-4xl md:text-6xl lg:text-7xl font-black text-white leading-[0.9] italic uppercase tracking-tighter"
          >
            <span class="bg-gradient-to-r from-white via-white to-orange-200 bg-clip-text text-transparent">
              {{ ad.title }}
            </span>
          </h1>

          <!-- Description with subtle animation -->
          <div class="overflow-hidden">
            <p
              class="text-sm md:text-lg lg:text-xl text-slate-300 font-medium max-w-xl leading-relaxed italic transform transition-all duration-1000 delay-300"
              :class="{ 'translate-y-0 opacity-100': isContentLoaded }"
            >
              {{ ad.description || "Découvrez l'excellence 2NCorporate" }}
            </p>
          </div>

          <!-- CTA Button with hover effects -->
          <div class="pt-6 transform transition-all duration-1000 delay-500"
               :class="{ 'translate-y-0 opacity-100': isContentLoaded }">
            <button
              @click="handleAdClick(ad)"
              class="group/btn relative overflow-hidden bg-gradient-to-r from-orange-500 via-orange-600 to-amber-500 text-white font-black py-4 px-10 rounded-2xl hover:shadow-2xl hover:shadow-orange-500/30 transition-all duration-300 uppercase text-xs tracking-widest active:scale-95 flex items-center gap-3"
            >
              <!-- Shine effect -->
              <div class="absolute inset-0 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

              <!-- Button content -->
              <span class="relative z-10">Explorer maintenant</span>
              <ArrowRight class="w-4 h-4 relative z-10 transition-transform duration-300 group-hover/btn:translate-x-1" />

              {/* <!-- Animated border --> */}
              <div class="absolute -inset-[1px] bg-gradient-to-r from-orange-500 via-amber-500 to-orange-500 rounded-2xl opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300 -z-10"></div>
            </button>
          </div>
        </div>

        <!-- Index indicator -->
        <div class="absolute bottom-6 right-6 z-20">
          <div class="flex items-center gap-2 text-white/60 text-xs font-bold">
            <span class="text-white text-lg font-black">{{ currentIndex + 1 }}</span>
            <span class="text-white/40">/</span>
            <span>{{ ads.length }}</span>
          </div>
        </div>
      </div>
    </TransitionGroup>

    <!-- Navigation dots with enhanced design -->
    <div class="absolute bottom-8 left-8 flex items-center gap-3 z-30">
      <div
        v-for="(_, i) in ads"
        :key="i"
        @click="goToAd(i)"
        class="relative cursor-pointer transition-all duration-300 group/dot"
      >
        <div
          class="h-1.5 rounded-full transition-all duration-500 relative overflow-hidden"
          :class="[
            i === currentIndex
              ? 'bg-gradient-to-r from-orange-500 to-amber-500 w-12 shadow-lg shadow-orange-500/30'
              : 'bg-white/20 w-3 hover:bg-white/40'
          ]"
        >
          <!-- Progress bar for active dot -->
          <div
            v-if="i === currentIndex"
            class="absolute top-0 left-0 h-full bg-gradient-to-r from-white/40 to-transparent animate-progress"
          ></div>
        </div>

        {/* <!-- Tooltip on hover --> */}
        <div
          v-if="i !== currentIndex"
          class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1.5 bg-slate-900/90 backdrop-blur-sm rounded-lg text-xs text-white font-medium whitespace-nowrap opacity-0 invisible group-hover/dot:opacity-100 group-hover/dot:visible transition-all duration-200 pointer-events-none"
        >
          {{ ads[i]?.title.substring(0, 20) }}...
        </div>
      </div>
    </div>

    <!-- Navigation arrows (only show if multiple ads) -->
    <div v-if="ads.length > 1" class="absolute right-8 top-1/2 transform -translate-y-1/2 flex flex-col gap-4 z-30">
      <button
        @click="prevAd"
        class="w-12 h-12 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white hover:bg-white/20 hover:scale-110 transition-all duration-300 group/arrow"
        aria-label="Previous ad"
      >
        <ChevronUp class="w-5 h-5 transition-transform group-hover/arrow:-translate-y-0.5" />
      </button>
      <button
        @click="nextAd"
        class="w-12 h-12 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white hover:bg-white/20 hover:scale-110 transition-all duration-300 group/arrow"
        aria-label="Next ad"
      >
        <ChevronDown class="w-5 h-5 transition-transform group-hover/arrow:translate-y-0.5" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { ArrowRight, ChevronUp, ChevronDown } from "lucide-vue-next";

const props = defineProps({
  ads: {
    type: Array,
    default: () => []
  }
});

const currentIndex = ref(0);
const isTimerPaused = ref(false);
const parallaxScale = ref(0);
const isContentLoaded = ref(false);
let timer = null;
let parallaxTimer = null;
let progressTimer = null;

// Computed for safety
const safeAds = computed(() => {
  if (!props.ads || !Array.isArray(props.ads)) return [];
  return props.ads.filter(ad => ad && typeof ad === 'object');
});

// Video detection
const isVideo = (url) => {
  if (!url || typeof url !== 'string') return false;
  const videoExtensions = ['.mp4', '.webm', '.ogg', '.mov', '.avi', '.m4v'];
  return videoExtensions.some(ext => url.toLowerCase().endsWith(ext));
};

// Handle image load
const handleImageLoad = () => {
  isContentLoaded.value = true;
};

// Handle ad click
const handleAdClick = (ad) => {
  if (ad.url) {
    window.open(ad.url, ad.target_blank ? '_blank' : '_self');
  } else if (ad.product_id) {
    // Navigate to product page
    console.log('Navigate to product:', ad.product_id);
  }
};

// Navigation functions
const nextAd = () => {
  if (safeAds.value.length === 0) return;
  currentIndex.value = (currentIndex.value + 1) % safeAds.value.length;
  resetContentAnimation();
};

const prevAd = () => {
  if (safeAds.value.length === 0) return;
  currentIndex.value = (currentIndex.value - 1 + safeAds.value.length) % safeAds.value.length;
  resetContentAnimation();
};

const goToAd = (index) => {
  if (index >= 0 && index < safeAds.value.length) {
    currentIndex.value = index;
    resetContentAnimation();
  }
};

// Timer controls
const startTimer = () => {
  if (safeAds.value.length > 1 && !isTimerPaused.value) {
    clearInterval(timer);
    timer = setInterval(() => {
      nextAd();
    }, 3000); // Change every 8 seconds
  }
};

const pauseTimer = () => {
  isTimerPaused.value = true;
  clearInterval(timer);
};

const resumeTimer = () => {
  isTimerPaused.value = false;
  startTimer();
};

// Parallax effect
const startParallaxEffect = () => {
  parallaxTimer = setInterval(() => {
    parallaxScale.value = Math.sin(Date.now() / 10000) * 0.1;
  }, 20);
};

// Reset animation states
const resetContentAnimation = () => {
  isContentLoaded.value = false;
  setTimeout(() => {
    isContentLoaded.value = true;
  }, 100);
};

// Initialize
onMounted(() => {
  if (safeAds.value.length > 0) {
    startTimer();
    startParallaxEffect();
    // Trigger initial content animation
    setTimeout(() => {
      isContentLoaded.value = true;
    }, 300);
  }
});

// Cleanup
onUnmounted(() => {
  clearInterval(timer);
  clearInterval(parallaxTimer);
  clearInterval(progressTimer);
});
</script>

<style scoped>
/* Custom animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0) translateX(0);
    opacity: 0;
  }
  10% {
    opacity: 0.3;
  }
  90% {
    opacity: 0.3;
  }
  50% {
    transform: translateY(-20px) translateX(10px);
  }
}

@keyframes progress {
  0% {
    width: 0%;
  }
  100% {
    width: 100%;
  }
}

@keyframes glow {
  0%, 100% {
    box-shadow: 0 0 20px rgba(249, 115, 22, 0.3);
  }
  50% {
    box-shadow: 0 0 40px rgba(249, 115, 22, 0.6);
  }
}

.animate-float {
  animation: float linear infinite;
}

.animate-progress {
  animation: progress 8s linear;
  animation-play-state: running;
}

.group:hover .animate-progress {
  animation-play-state: paused;
}

/* Transition effects */
.ad-swap-enter-active,
.ad-swap-leave-active {
  transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
  position: absolute;
  width: 100%;
  height: 100%;
}

.ad-swap-enter-from {
  opacity: 0;
  transform: translateX(50px) scale(0.95);
  filter: blur(10px);
}

.ad-swap-leave-to {
  opacity: 0;
  transform: translateX(-50px) scale(0.95);
  filter: blur(10px);
}

.ad-swap-enter-to,
.ad-swap-leave-from {
  opacity: 1;
  transform: translateX(0) scale(1);
  filter: blur(0);
}

/* Smooth image transitions */
img, video {
  will-change: transform;
  backface-visibility: hidden;
}

/* Gradient text utility */
.text-gradient {
  background-size: 200% 200%;
  animation: gradient-shift 3s ease infinite;
}

@keyframes gradient-shift {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

/* Custom scrollbar for the dots container (if needed) */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .max-w-\[85\%\] {
    max-width: 95% !important;
  }

  .text-4xl {
    font-size: 2rem;
  }

  .text-sm {
    font-size: 0.875rem;
  }

  .p-8 {
    padding: 1.5rem;
  }
}

/* Accessibility improvements */
button:focus-visible {
  outline: 2px solid rgba(249, 115, 22, 0.8);
  outline-offset: 2px;
}

/* Loading state */
.skeleton {
  background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
</style>
