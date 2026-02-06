<script setup>
defineProps({
  isOpen: Boolean,
  categories: Array
})
defineEmits(['close'])
</script>

<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-4"
  >
    <div
      v-if="isOpen"
      class="absolute top-full left-0 w-full bg-white shadow-2xl border-t border-orange-100 z-50 py-8"
      @mouseleave="$emit('close')"
    >
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
          <div v-for="cat in categories" :key="cat.id" class="group">
            <router-link
              :to="`/shop?category=${cat.id}`"
              @click="$emit('close')"
              class="flex flex-col items-center p-4 rounded-2xl hover:bg-orange-50 transition-all"
            >
              <div class="w-20 h-20 mb-4 rounded-full overflow-hidden bg-gray-100 border-2 border-transparent group-hover:border-orange-500 transition-all">
                <img :src="cat.image" :alt="cat.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
              </div>
              <h3 class="font-bold text-gray-800 group-hover:text-orange-600 text-sm uppercase text-center">
                {{ cat.name }}
              </h3>
              <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest">
                {{ cat.products_count || 0 }} Produits
              </p>
            </router-link>
          </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
          <p class="text-sm text-gray-500 italic">Besoin d'aide pour choisir ? Contactez notre support.</p>
          <router-link to="/shop" class="text-orange-600 font-bold text-sm hover:underline">
            Voir tout le catalogue →
          </router-link>
        </div>
      </div>
    </div>
  </Transition>
</template>
