<template>
  <Transition name="fade">
    <div v-if="modal.isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="modal.close()"></div>

      <div class="relative bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden animate-pop">
        <div class="p-8">
          <div class="flex justify-center mb-6">
            <div class="bg-green-100 p-4 rounded-full">
              <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
          </div>

          <h3 class="text-2xl font-black text-center text-gray-900 uppercase italic mb-2">Félicitations !</h3>
          <p class="text-center text-gray-500 font-medium mb-8">{{ modal.message }}</p>

          <div v-if="modal.product" class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl mb-8 border border-gray-100">
            <img :src="modal.product.image_url || (modal.product.images?.[0]?.url)" class="w-20 h-20 object-contain bg-white rounded-xl" />
            <div>
              <p class="font-bold text-gray-800 text-sm uppercase">{{ modal.product.name }}</p>
              <p class="text-orange-600 font-black">{{ modal.product.final_price || modal.product.price }} FCFA</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <button @click="modal.close()" class="py-4 rounded-2xl font-bold text-xs uppercase tracking-widest text-gray-500 hover:bg-gray-100 transition">
              Continuer
            </button>
            <router-link
              :to="modal.type === 'cart' ? '/cart' : '/wishlist'"
              @click="modal.close()"
              class="py-4 bg-orange-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest text-center hover:bg-orange-700 shadow-lg shadow-orange-200"
            >
              {{ modal.type === 'cart' ? 'Voir Panier' : 'Mes Favoris' }}
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { useModalStore } from '@/stores/modalStore';
const modal = useModalStore();
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.animate-pop { animation: pop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes pop {
  0% { transform: scale(0.8); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
</style>
