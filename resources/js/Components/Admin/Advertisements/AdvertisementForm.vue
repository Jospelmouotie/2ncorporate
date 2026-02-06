<template>
  <form @submit.prevent="submitForm" class="bg-white p-6 rounded-[2rem] shadow-xl border border-purple-100 space-y-4">
    <h2 class="text-xl font-black text-purple-600 mb-4">Créer une Campagne</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <input v-model="form.title" type="text" placeholder="Titre de la pub" required
        class="w-full px-5 py-3 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-purple-500"/>

      <select v-model="form.type" required
        class="w-full px-5 py-3 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-purple-500">
        <option value="">Choisir le Type</option>
        <option value="image">Image Standard</option>
        <option value="video">Vidéo MP4</option>
        <option value="interstitial">Interstitial (Plein écran)</option>
      </select>
    </div>

    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:border-purple-400 transition">
      <input type="file" @change="handleFile" id="media-upload" class="hidden" accept="image/*,video/mp4"/>
      <label for="media-upload" class="cursor-pointer block">
        <span v-if="!form.media" class="text-slate-400">Cliquez pour ajouter le média (JPG, PNG, MP4)</span>
        <span v-else class="text-purple-600 font-bold text-sm italic">{{ form.media.name }}</span>
      </label>
    </div>

    <div class="flex gap-4">
      <div class="w-1/2">
        <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Début</label>
        <input type="date" v-model="form.start_at" required class="w-full px-4 py-2 bg-slate-50 rounded-xl mt-1"/>
      </div>
      <div class="w-1/2">
        <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Fin (Optionnel)</label>
        <input type="date" v-model="form.end_at" class="w-full px-4 py-2 bg-slate-50 rounded-xl mt-1"/>
      </div>
    </div>

    <button type="submit" :disabled="loading"
      class="w-full bg-purple-600 text-white font-black py-4 rounded-xl hover:bg-purple-700 shadow-lg disabled:opacity-50">
      {{ loading ? 'Téléchargement...' : 'Lancer la Publicité' }}
    </button>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { useAdStore } from '@/stores/advertisementStore'

const adStore = useAdStore()
const loading = ref(false)

const form = ref({
  title: '',
  type: '',
  media: null,
  start_at: new Date().toISOString().substr(0, 10),
  end_at: ''
})

const handleFile = (e) => {
  form.value.media = e.target.files[0]
}

const submitForm = async () => {
  try {
    loading.value = true
    await adStore.createAd(form.value)
    form.value = { title: '', type: '', media: null, start_at: new Date().toISOString().substr(0, 10), end_at: '' }
    alert("Publicité enregistrée !")
  } catch (err) {
    alert("Erreur lors de l'upload")
  } finally {
    loading.value = false
  }
}
</script>
