<template>
  <form @submit.prevent="submit">
    <div class="mb-4">
      <label class="block text-gray-700 font-medium mb-1">Titre</label>
      <input v-model="form.title" type="text" class="w-full border rounded px-3 py-2" />
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 font-medium mb-1">Image</label>
      <input @change="handleFile" type="file" class="w-full" />
    </div>

    <div class="mb-4 grid grid-cols-2 gap-4">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Début</label>
        <input v-model="form.start_at" type="date" class="w-full border rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Fin</label>
        <input v-model="form.end_at" type="date" class="w-full border rounded px-3 py-2" />
      </div>
    </div>

    <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">Enregistrer</button>
  </form>
</template>

<script setup>
import { reactive } from 'vue'
import { usePromotionStore } from '@/stores/promotionStore'

const store = usePromotionStore()

const form = reactive({
  title: store.promotion?.title || '',
  start_at: store.promotion?.start_at?.slice(0,10) || '',
  end_at: store.promotion?.end_at?.slice(0,10) || '',
  image: null
})

const handleFile = (e) => {
  form.image = e.target.files[0]
}

const submit = async () => {
  const data = new FormData()
  data.append('title', form.title)
  data.append('start_at', form.start_at)
  data.append('end_at', form.end_at)
  if (form.image) data.append('media', form.image)

  try {
    if (store.promotion?.id) {
      await store.updatePromotion(store.promotion.id, data)
    } else {
      await store.createPromotion(data)
    }
    alert('Promotion enregistrée')
  } catch (err) {
    alert('Erreur: ' + err.message)
  }
}
</script>
