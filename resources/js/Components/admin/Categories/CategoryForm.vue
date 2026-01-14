<template>
  <div class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">

      <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
        <h3 class="text-xl font-black text-gray-800">
          {{ category ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
        </h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
      </div>

      <form @submit.prevent="handleSubmit" class="p-8 space-y-5">
        <div class="flex flex-col items-center gap-4 mb-6">
          <div class="relative group">
            <div class="w-24 h-24 rounded-3xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
              <img v-if="previewUrl" :src="previewUrl" class="w-full h-full object-cover" />
              <span v-else class="text-3xl">📸</span>
            </div>
            <input
              type="file"
              @change="handleFileChange"
              accept="image/*"
              class="absolute inset-0 opacity-0 cursor-pointer"
            />
          </div>
          <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest text-center">
            Cliquez pour {{ previewUrl ? 'changer' : 'ajouter' }} une image
          </p>
        </div>

        <div>
          <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Nom du rayon</label>
          <input
            v-model="form.name"
            type="text"
            required
            placeholder="Ex: Électronique"
            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-500 transition-all outline-none text-sm"
          />
        </div>

        <div>
          <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Slug (URL)</label>
          <input
            v-model="form.slug"
            type="text"
            placeholder="electronique-pro"
            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-500 transition-all outline-none text-sm"
          />
        </div>

        <div class="flex gap-3 pt-4">
          <button
            type="button"
            @click="$emit('close')"
            class="flex-1 py-4 font-bold text-gray-500 hover:bg-gray-100 rounded-2xl transition-colors"
          >
            Annuler
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="flex-1 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-orange-600 disabled:opacity-50 transition-all shadow-xl"
          >
            {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCategoryStore } from '@/stores/categoryStore'
import { useToastStore } from '@/stores/useToastStore'

const props = defineProps({
  category: Object
})

const emit = defineEmits(['close'])
const categoryStore = useCategoryStore()
const toast = useToastStore()

const loading = ref(false)
const previewUrl = ref(props.category?.image_url || null)

const form = ref({
  name: props.category?.name || '',
  slug: props.category?.slug || '',
  image: null
})

const handleFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.image = file
    previewUrl.value = URL.createObjectURL(file) // Aperçu temporaire
  }
}
const handleSubmit = async () => {
  loading.value = true
  const fd = new FormData()
  fd.append('name', form.value.name)
  fd.append('slug', form.value.slug)

  if (form.value.image instanceof File) {
    fd.append('image', form.value.image)
  }

  try {
    if (props.category) {
      await categoryStore.updateCategory(props.category.id, fd)
      toast.add('Mis à jour !', 'success')
    } else {
      await categoryStore.createCategory(fd)
      toast.add('Créé !', 'success')
    }
    emit('close')
  } catch (err) {
    if (err.response?.status === 422) {
      const errors = err.response.data.errors
      // Ceci va t'afficher exactement quel champ bloque dans la console
      console.table(errors)

      // Optionnel : afficher le premier message d'erreur
      const firstError = Object.values(errors)[0][0]
      toast.add(firstError, 'error')
    } else {
      toast.add('Erreur serveur', 'error')
    }
  } finally {
    loading.value = false
  }
}
</script>
