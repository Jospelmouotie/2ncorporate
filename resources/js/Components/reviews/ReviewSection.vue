<template>
  <div class="mt-20 border-t border-gray-100 pt-16">
    <div class="flex flex-col lg:flex-row justify-between items-start gap-12">
      <div class="w-full lg:w-1/2 space-y-8">
        <h2 class="text-4xl font-black uppercase italic tracking-tighter">Avis Clients ({{ reviews.length }})</h2>
        <div v-if="reviews.length === 0" class="p-8 bg-gray-50 rounded-[2rem] text-center border-2 border-dashed border-gray-200 text-gray-400 font-bold">
          Aucun avis pour le moment.
        </div>
        <div v-else class="space-y-6">
          <div v-for="review in reviews" :key="review.id" class="p-6 bg-white rounded-[2rem] shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-black text-sm">
                  {{ review.user?.name?.charAt(0).toUpperCase() }}
                </div>
                <span class="font-bold text-gray-900">{{ review.user?.name }}</span>
              </div>
              <div class="flex gap-0.5">
                <Star v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= review.rating ? 'fill-orange-500 text-orange-500' : 'text-gray-200'" />
              </div>
            </div>
            <p class="text-gray-600 italic">"{{ review.comment }}"</p>
          </div>
        </div>
      </div>

      <div class="w-full lg:w-1/2 bg-orange-600 p-8 md:p-10 rounded-[3rem] shadow-xl text-white">
        <h3 class="text-2xl font-black uppercase italic mb-6">Noter ce produit</h3>
        <form @submit.prevent="submitForm" class="space-y-4">
          <div class="flex gap-2 mb-4">
            <button v-for="i in 5" :key="i" type="button" @click="form.rating = i">
              <Star class="w-8 h-8" :class="form.rating >= i ? 'fill-white text-white' : 'text-orange-400'" />
            </button>
          </div>
          <textarea v-model="form.comment" rows="3" class="w-full bg-orange-500 border-2 border-orange-400 rounded-2xl p-4 text-white placeholder:text-orange-200 focus:outline-none" placeholder="Votre avis..."></textarea>
          <button type="submit" class="w-full bg-white text-orange-600 py-4 rounded-2xl font-black uppercase hover:bg-black hover:text-white transition-all">
            Publier
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Star } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps(['productId', 'reviews']);
const emit = defineEmits(['review-added']);
const form = ref({ rating: 5, comment: '' });

const submitForm = async () => {
  try {
    const res = await axios.post(`/api/products/${props.productId}/reviews`, form.value);
    emit('review-added', res.data.data);
    form.value = { rating: 5, comment: '' };
  } catch (e) { alert("Erreur lors de l'envoi"); }
};
</script>
