<script setup>
import { ref } from 'vue'
import LoginForm from './LoginForm.vue' // Ton code de login
import RegisterForm from './RegisterForm.vue' // Ton code de register

const props = defineProps({
  isOpen: Boolean
})

const emit = defineEmits(['close'])
const currentView = ref('login') // 'login' ou 'register'

const toggleView = (view) => {
  currentView.value = view
}
</script>

<template>
  <Transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @click.self="emit('close')">

      <Transition name="slide-up" mode="out-in">
        <div :key="currentView" class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 relative overflow-hidden">

          <button @click="emit('close')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-2xl">&times;</button>

          <LoginForm v-if="currentView === 'login'" @switch="toggleView('register')" />
          <RegisterForm v-else @switch="toggleView('login')" />

        </div>
      </Transition>

    </div>
  </Transition>
</template>

<style scoped>
/* Animation de l'overlay (fond sombre) */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Animation du formulaire (superposition/glissement) */
.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-up-enter-from { transform: translateY(20px); opacity: 0; }
.slide-up-leave-to { transform: translateY(-20px); opacity: 0; }
</style>
