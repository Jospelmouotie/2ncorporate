<script setup>
import { ref, reactive } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)
const errors = ref({})

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

/**
 * VALIDATION CÔTÉ CLIENT
 */
const validateForm = () => {
  const localErrors = {}
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

  // Nom
  if (!form.name) localErrors.name = ["Le nom est requis."]

  // Email
  if (!form.email) {
    localErrors.email = ["L'email est requis."]
  } else if (!emailRegex.test(form.email)) {
    localErrors.email = ["Format d'email invalide."]
  }

  // Mot de passe (Min 4 caractères)
  if (!form.password) {
    localErrors.password = ["Le mot de passe est requis."]
  } else if (form.password.length < 4) {
    localErrors.password = ["Le mot de passe doit faire au moins 4 caractères."]
  }

  // Confirmation
  if (form.password !== form.password_confirmation) {
    localErrors.password_confirmation = ["Les mots de passe ne correspondent pas."]
  }

  errors.value = localErrors
  return Object.keys(localErrors).length === 0
}

/**
 * SOUMISSION
 */
const submit = async () => {
  errors.value = {}
  if (!validateForm()) return

  loading.value = true

  try {
    // Initialisation CSRF
    await axios.get('/sanctum/csrf-cookie')

    // Appel à ton AuthController (API)
    const response = await axios.post('/api/register', form)

    // On stocke l'utilisateur et on redirige
    window.user = response.data.user
    router.push({ name: 'home' })

  } catch (e) {
    if (e.response && e.response.status === 422) {
      errors.value = e.response.data.errors
    } else {
      errors.value = { general: ["Erreur lors de l'inscription. Réessayez."] }
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="flex items-center justify-center min-h-[80vh] px-4 py-10">
    <div class="auth-form w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-gray-100">

      <h2 class="text-3xl font-extrabold text-center text-slate-800 mb-2">Créer un compte</h2>
      <p class="text-center text-slate-500 mb-8 text-sm">Rejoignez-nous en quelques secondes.</p>

      <div v-if="errors.general" class="mb-6 p-4 bg-red-50 text-red-700 text-sm rounded-lg">
        {{ errors.general[0] }}
      </div>

      <div class="space-y-5">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Nom complet</label>
          <input v-model="form.name" type="text" placeholder="John Doe" class="input-field" :class="{ 'border-red-400': errors.name }" />
          <p v-if="errors.name" class="error-text">{{ errors.name[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
          <input v-model="form.email" type="email" placeholder="john@example.com" class="input-field" :class="{ 'border-red-400': errors.email }" />
          <p v-if="errors.email" class="error-text">{{ errors.email[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Mot de passe (Min. 4)</label>
          <input v-model="form.password" type="password" placeholder="••••••••" class="input-field" :class="{ 'border-red-400': errors.password }" />
          <p v-if="errors.password" class="error-text">{{ errors.password[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Confirmer le mot de passe</label>
          <input v-model="form.password_confirmation" type="password" placeholder="••••••••" class="input-field" :class="{ 'border-red-400': errors.password_confirmation }" />
          <p v-if="errors.password_confirmation" class="error-text">{{ errors.password_confirmation[0] }}</p>
        </div>

        <button @click="submit" class="btn-primary" :disabled="loading">
          <span v-if="loading" class="loader"></span>
          {{ loading ? 'Création du compte...' : 'S’inscrire' }}
        </button>
      </div>

      <p class="text-center mt-8 text-sm text-slate-600">
        Déjà inscrit ?
        <router-link to="/login" class="text-orange-600 font-bold hover:underline">Connectez-vous</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.input-field {
  width: 100%;
  padding: 12px 14px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  outline: none;
  transition: all 0.2s;
}
.input-field:focus {
  border-color: #f97316;
  background-color: white;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}
.error-text { color: #ef4444; font-size: 0.75rem; margin-top: 4px; font-weight: 500; }
.btn-primary {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 14px;
  background: linear-gradient(to right, #f97316, #ea580c);
  color: white;
  font-weight: bold;
  border-radius: 10px;
  transition: transform 0.2s;
}
.btn-primary:hover:not(:disabled) { transform: translateY(-1px); }
.btn-primary:disabled { opacity: 0.7; }
.loader {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
