<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Promotions</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <PromotionItem
        v-for="promo in promotions"
        :key="promo.id"
        :promotion="promo"
        @edit="selectPromotion"
        @delete="deletePromotion"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { usePromotionStore } from '@/stores/promotionStore'
import PromotionItem from './PromotionItem.vue'

const store = usePromotionStore()
const { promotions, fetchPromotions, deletePromotion } = store

onMounted(() => {
  fetchPromotions()
})

const selectPromotion = (promo) => {
  store.promotion = promo
}
</script>
