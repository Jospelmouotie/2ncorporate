<script setup>
import { ref, watch, onMounted } from "vue";
import api from "@/services/api";
import axios from "axios"; // Assure-toi qu'axios est bien importé pour le submit
import { useToastStore } from "@/stores/useToastStore";
import { toRaw } from "vue"; // Ajoute cet import en haut

/* ======================
  PROPS & EMITS
====================== */
const props = defineProps({
  modelValue: Boolean,
  product: { type: Object, default: null },
});

const emit = defineEmits(["update:modelValue", "saved"]);
const toast = useToastStore();

/* ======================
  STATE
====================== */
const loading = ref(false);
const errors = ref({});
const categories = ref([]);

const images = ref([]); // Nouveaux fichiers File
const previews = ref([]); // URLs blob pour previews nouveaux
const existingImages = ref([]); // Images déjà en base
const deletedImageIds = ref([]); // IDs des images à supprimer sur le serveur

/* ======================
  FORM
====================== */
const form = ref({
  name: "",
  description: "",
  price: "",
  category_id: "",
});

/* ======================
  FETCH CATEGORIES
====================== */
const fetchCategories = async () => {
  try {
    const res = await api.get("/admin/categories");
    categories.value = res.data.data ?? res.data;
  } catch (e) {
    console.error("Erreur catégories", e);
  }
};

onMounted(fetchCategories);

/* ======================
  WATCH MODAL OPEN
====================== */
watch(
  () => props.modelValue,
  (open) => {
    if (!open) return;

    errors.value = {};
    images.value = [];
    previews.value = [];
    existingImages.value = [];
    deletedImageIds.value = [];

    if (props.product) {
      form.value = {
        name: props.product.name ?? "",
        description: props.product.description ?? "",
        price: props.product.price ?? "",
        category_id: props.product.category_id ?? "",
      };
      existingImages.value = [...(props.product.images ?? [])];
    } else {
      form.value = { name: "", description: "", price: "", category_id: "" };
    }
  }
);

/* ======================
  HANDLE IMAGES (FILES)
====================== */
const handleImages = (e) => {
  const files = Array.from(e.target.files);
  files.forEach((file) => {
    if (!file.type.startsWith("image/")) return;
    images.value.push(file);
    previews.value.push(URL.createObjectURL(file));
  });
  e.target.value = null;
};

const removeNewImage = (index) => {
  images.value.splice(index, 1);
  previews.value.splice(index, 1);
};

const removeExistingImage = (id) => {
  deletedImageIds.value.push(id);
  existingImages.value = existingImages.value.filter((img) => img.id !== id);
};

/* ======================
  SUBMIT
====================== */

/* ======================
  SUBMIT AVEC TON TOAST STORE
====================== */
const submit = async () => {
  loading.value = true;
  errors.value = {};

  const fd = new FormData();

  // 1. Champs de base
  fd.append('name', form.value.name);
  fd.append('price', form.value.price);
  fd.append('category_id', form.value.category_id);
  fd.append('description', form.value.description || '');

  // 2. Gestion des images
  if (images.value.length > 0) {
    images.value.forEach((file) => {
      const rawFile = toRaw(file);
      if (rawFile instanceof File) {
        fd.append('images[]', rawFile);
      }
    });
  }

  try {
    const config = {
      headers: { 'Content-Type': 'multipart/form-data' }
    };

    if (props.product) {
      // MODE ÉDITION
      fd.append('_method', 'PUT');
      deletedImageIds.value.forEach(id => fd.append('deleted_images[]', id));

      await api.post(`/admin/products/${props.product.id}`, fd, config);

      // Utilisation de ton action : add(message, type)
      toast.add("Produit mis à jour avec succès !", "success");
    } else {
      // MODE CRÉATION
      if (images.value.length === 0) {
        toast.add("Veuillez ajouter au moins une image.", "error");
        loading.value = false;
        return;
      }

      await api.post('/admin/products', fd, config);
      toast.add("Nouveau produit créé avec succès !", "success");
    }

    emit('saved');
    close();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
      toast.add("Erreur de validation. Vérifiez les champs.", "error");
    } else {
      console.error(error);
      toast.add("Une erreur serveur est survenue.", "error");
    }
  } finally {
    loading.value = false;
  }
};
const close = () => {
  emit("update:modelValue", false);
};
</script>

<template>
  <transition name="fade-scale">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
    >
      <div
        class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden max-h-[95vh] flex flex-col border-t-4 border-orange-500"
      >
        <div
          class="px-6 py-4 bg-gradient-to-r from-orange-600 to-orange-400 text-white flex justify-between items-center"
        >
          <h2 class="text-xl font-bold uppercase tracking-wider">
            {{ product ? "Modifier le produit" : "Nouveau produit" }}
          </h2>
          <button
            @click="close"
            class="text-3xl leading-none hover:rotate-90 transition-transform"
          >
            &times;
          </button>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-6 overflow-y-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="label">Nom du produit</label>
              <input
                v-model="form.name"
                class="input focus:border-orange-500 outline-none"
                placeholder="Ex: iPhone 15"
                required
              />
              <p v-if="errors.name" class="error">{{ errors.name[0] }}</p>
            </div>

            <div>
              <label class="label">Prix (FCFA)</label>
              <input
                type="number"
                v-model="form.price"
                class="input focus:border-orange-500 outline-none"
                placeholder="0"
                required
              />
              <p v-if="errors.price" class="error">{{ errors.price[0] }}</p>
            </div>
          </div>

          <div>
            <label class="label">Catégorie</label>
            <select
              v-model="form.category_id"
              class="input focus:border-orange-500 outline-none"
              required
            >
              <option value="">Sélectionner...</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">
                {{ c.name }}
              </option>
            </select>
            <p v-if="errors.category_id" class="error">{{ errors.category_id[0] }}</p>
          </div>

          <div>
            <label class="label">Description</label>
            <textarea
              v-model="form.description"
              class="input h-28 resize-none focus:border-orange-500 outline-none"
              placeholder="Détails du produit..."
            ></textarea>
          </div>

          <div v-if="existingImages.length">
            <label class="label text-orange-600"
              >Images en ligne (Cliquez sur × pour supprimer)</label
            >
            <div class="grid grid-cols-3 sm:grid-cols-5 gap-4">
              <div
                v-for="img in existingImages"
                :key="img.id"
                class="relative group aspect-square rounded-xl overflow-hidden border-2 border-orange-100"
              >
                <img :src="img.url" class="w-full h-full object-cover" />
                <button
                  type="button"
                  @click="removeExistingImage(img.id)"
                  class="absolute top-1 right-1 bg-red-600 text-white w-6 h-6 rounded-full shadow-lg flex items-center justify-center text-xs"
                >
                  ×
                </button>
              </div>
            </div>
          </div>

          <div>
            <label class="label">Ajouter des photos</label>
            <div
              @click="$refs.file.click()"
              class="border-2 border-dashed border-orange-200 rounded-xl p-8 text-center cursor-pointer hover:border-orange-500 hover:bg-orange-50 transition-all group"
            >
              <span
                class="text-3xl group-hover:scale-125 transition-transform inline-block"
                >📸</span
              >
              <p class="mt-2 font-medium text-gray-600">
                Cliquez pour parcourir vos fichiers
              </p>
            </div>
            <input
              ref="file"
              type="file"
              multiple
              accept="image/*"
              class="hidden"
              @change="handleImages"
            />
          </div>

          <div v-if="previews.length" class="grid grid-cols-3 sm:grid-cols-5 gap-4">
            <div
              v-for="(img, i) in previews"
              :key="i"
              class="relative group aspect-square rounded-xl overflow-hidden border-2 border-green-200"
            >
              <img :src="img" class="w-full h-full object-cover" />
              <div class="absolute inset-0 bg-green-500/10 pointer-events-none"></div>
              <button
                type="button"
                @click="removeNewImage(i)"
                class="absolute top-1 right-1 bg-gray-800 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs"
              >
                ×
              </button>
              <span
                class="absolute bottom-0 left-0 right-0 bg-green-500 text-white text-[9px] text-center py-0.5 font-bold uppercase"
                >Nouveau</span
              >
            </div>
          </div>

          <div class="flex justify-end gap-4 pt-6 border-t">
            <button type="button" @click="close" class="btn-secondary">Annuler</button>
            <button type="submit" class="btn-primary" :disabled="loading">
              {{ loading ? "Enregistrement..." : "Enregistrer le produit" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.25s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.label {
  display: block;
  font-size: 0.85rem;
  font-weight: 700;
  margin-bottom: 6px;
  color: #4b5563;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}
.input {
  width: 100%;
  padding: 12px 14px;
  border-radius: 12px;
  border: 2px solid #f3f4f6;
  transition: border-color 0.2s;
  background: #f9fafb;
}
.error {
  color: #ef4444;
  font-size: 11px;
  font-weight: 600;
  margin-top: 4px;
}
.btn-primary {
  background: #f97316;
  color: white;
  padding: 12px 28px;
  border-radius: 12px;
  font-weight: 800;
  box-shadow: 0 4px 14px 0 rgba(249, 115, 22, 0.39);
  transition: all 0.2s;
}
.btn-primary:hover:not(:disabled) {
  background: #ea580c;
  transform: translateY(-1px);
}
.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.btn-secondary {
  background: #f3f4f6;
  color: #4b5563;
  padding: 12px 28px;
  border-radius: 12px;
  font-weight: 700;
  transition: background 0.2s;
}
.btn-secondary:hover {
  background: #e5e7eb;
}
</style>
