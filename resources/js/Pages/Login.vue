<script setup>
import { ref } from 'vue'

const emit = defineEmits(['switch'])
const form = ref({ email: '', password: '' })
const loading = ref(false)
const errors = ref({})

const submit = async () => {
  loading.value = true
  errors.value = {}

  try {
    // TODO : Appel API login (axios / fetch)
    console.log('submit login', form.value)

  } catch (e) {
    errors.value = e.response?.data?.errors || {}
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-form">
    <h2 class="text-2xl font-bold text-center text-slate-800 mb-4">Se connecter</h2>

    <input
      v-model="form.email"
      type="email"
      placeholder="Email"
      class="input-field"
    />

    <input
      v-model="form.password"
      type="password"
      placeholder="Mot de passe"
      class="input-field"
    />

    <button @click="submit" class="btn-primary" :disabled="loading">
      {{ loading ? 'Connexion...' : 'Se connecter' }}
    </button>

    <p class="text-center mt-4 text-sm text-slate-600">
      Pas de compte ?
      <span @click="emit('switch', 'register')" class="text-orange-500 cursor-pointer font-bold">
        S’inscrire
      </span>
    </p>
  </div>
</template>

<style scoped>
.input-field {
  width: 100%;
  padding: 12px;
  margin-bottom: 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.btn-primary {
  width: 100%;
  padding: 12px;
  background: #f97316;
  color: white;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
</style>
