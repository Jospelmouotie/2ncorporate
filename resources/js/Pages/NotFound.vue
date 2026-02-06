<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 font-sans">
    <div class="text-center space-y-6">
      <div class="text-8xl font-black text-orange-500">404</div>

      <h1 class="text-3xl font-black text-slate-800">
        Page introuvable
      </h1>

      <p class="text-slate-500 max-w-md mx-auto">
        La page que vous recherchez n’existe pas ou a été déplacée.
      </p>

      <div class="flex justify-center gap-4 pt-6">
        <button
          @click="router.back()"
          class="px-6 py-3 rounded-xl bg-slate-200 font-bold hover:bg-slate-300 transition"
        >
          ← Retour
        </button>

        <router-link
          to="/"
          class="px-6 py-3 rounded-xl bg-orange-500 text-white font-bold hover:bg-orange-600 transition shadow-lg"
        >
          Accueil
        </router-link>
      </div>
    </div>
  </div>
</template>
