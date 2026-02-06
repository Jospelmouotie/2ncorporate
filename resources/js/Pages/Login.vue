<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const form = reactive({
  email: '',
  password: '',
  remember: false
})
const loading = ref(false)
const errors = reactive({
  email: [],
  password: [],
  general: []
})

const submit = async () => {
  loading.value = true

  try {
    const response = await axios.post('/api/login', {
      email: form.email,
      password: form.password,
      remember: form.remember
    })

    // Force la mise à jour de window.user
    window.user = response.data.user

    // IMPORTANT: Force le rechargement de la page pour mettre à jour la sidebar
    window.location.href = window.user.is_admin ? '/admin' : '/'

  } catch (err) {
    console.error('Login error:', err)
    if (err.response?.data?.errors) {
      errors.email = err.response.data.errors.email || []
      errors.password = err.response.data.errors.password || []
      errors.general = err.response.data.errors.general || []
    }
  } finally {
    loading.value = false
  }
}
</script>
<template>
  <div class="flex items-center justify-center min-h-[80vh] px-4">
    <div class="auth-form w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-gray-100">
      <h2 class="text-3xl font-extrabold text-center text-slate-800 mb-2 text-shadow-sm">Bon retour !</h2>
      <p class="text-center text-slate-500 mb-8 text-sm italic">Business differently with 2NCorporate</p>

      <!-- Message d'erreur général -->
      <Transition name="fade">
        <div v-if="error" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-lg">
          {{ error }}
        </div>
      </Transition>

      <!-- Erreurs générales du serveur -->
      <Transition name="fade">
        <div v-if="errors.general.length" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-lg">
          {{ errors.general[0] }}
        </div>
      </Transition>

      <div class="space-y-6">
        <!-- Champ email -->
        <div>
          <label class="block text-sm font-bold text-slate-700 mb-2">Adresse Email</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="votre@email.com"
            class="input-field"
            :class="{ 'border-red-400 ring-2 ring-red-50': errors.email.length }"
            @keyup="handleKeyPress"
          />
          <p v-if="errors.email.length" class="error-text">{{ errors.email[0] }}</p>
        </div>

        <!-- Champ mot de passe -->
        <div>
          <label class="block text-sm font-bold text-slate-700 mb-2">Mot de passe</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="••••••••"
            class="input-field"
            :class="{ 'border-red-400 ring-2 ring-red-50': errors.password.length }"
            @keyup="handleKeyPress"
          />
          <p v-if="errors.password.length" class="error-text">{{ errors.password[0] }}</p>
        </div>

        <!-- Options -->
        <div class="flex items-center justify-between">
          <label class="flex items-center text-sm text-slate-600 cursor-pointer group">
            <input
              v-model="form.remember"
              type="checkbox"
              class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500 mr-2 transition"
            >
            <span class="group-hover:text-orange-600">Se souvenir de moi</span>
          </label>
          <router-link
            to="/forgot-password"
            class="text-sm text-orange-600 font-bold hover:underline"
          >
            Mot de passe oublié ?
          </router-link>
        </div>

        <!-- Bouton de soumission -->
        <button
          @click="submit"
          :disabled="loading"
          class="btn-primary"
        >
          <span v-if="loading" class="loader"></span>
          {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
        </button>
      </div>

      <!-- Lien d'inscription -->
      <p class="text-center mt-8 text-sm text-slate-600">
        Nouveau client ?
        <router-link
          to="/register"
          class="text-orange-600 font-black hover:underline underline-offset-4 ml-1"
        >
          Créer un compte
        </router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.input-field {
  width: 100%;
  padding: 14px 16px;
  background-color: #f8fafc;
  border: 2px solid #f1f5f9;
  border-radius: 14px;
  transition: all 0.3s ease;
  outline: none;
  font-size: 16px; /* Évite le zoom sur mobile */
}

.input-field:focus {
  background-color: #fff;
  border-color: #f97316;
  box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
}

.input-field:focus-visible {
  outline: 2px solid #f97316;
  outline-offset: 2px;
}

.error-text {
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.5rem;
  font-weight: 600;
}

.btn-primary {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 16px;
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  color: white;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-radius: 14px;
  border: none;
  cursor: pointer;
  box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.3);
  transition: all 0.3s ease;
  font-size: 0.95rem;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 20px 25px -5px rgba(249, 115, 22, 0.4);
}

.btn-primary:active:not(:disabled) {
  transform: translateY(0);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.loader {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Amélioration de l'accessibilité */
.input-field:focus,
.btn-primary:focus {
  outline: 2px solid #f97316;
  outline-offset: 2px;
}

/* Support des navigateurs plus anciens */
@supports not (selector(:focus-visible)) {
  .input-field:focus,
  .btn-primary:focus {
    outline: 2px solid #f97316;
    outline-offset: 2px;
  }
}
</style>
