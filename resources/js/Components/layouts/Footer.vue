<template>
  <footer class="bg-[rgba(55,65,81,1)] text-white mt-10">
    <div class="container mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Logo & Description -->
      <div>
        <h2 class="text-2xl font-bold text-[rgba(251,146,60,1)] mb-2">ADHIMI</h2>
        <p class="text-gray-300 text-sm">
          Boutique en ligne spécialisée dans les produits de qualité. Livraison rapide et service client dédié.
        </p>
      </div>

      <!-- Liens -->
      <div>
        <h3 class="font-semibold mb-4 text-[rgba(251,146,60,1)]">Liens utiles</h3>
        <ul class="space-y-2">
          <li><router-link to="/shop" class="hover:text-[rgba(139,92,246,1)] transition-colors">Boutique</router-link></li>
          <li><router-link to="/promotions" class="hover:text-[rgba(251,146,60,1)] transition-colors">Promotions</router-link></li>
          <li><router-link to="/new" class="hover:text-[rgba(139,92,246,1)] transition-colors">Nouveautés</router-link></li>
          <li><router-link to="/brands" class="hover:text-[rgba(139,92,246,1)] transition-colors">Marques</router-link></li>
          <li><router-link to="/contact" class="hover:text-[rgba(251,146,60,1)] transition-colors">Contact</router-link></li>
        </ul>
      </div>

      <!-- Contact -->
      <div>
        <h3 class="font-semibold mb-4 text-[rgba(251,146,60,1)]">Contact</h3>
        <ul class="space-y-2 text-gray-300 text-sm">
          <li>📞 +237 XXX XXX XXX</li>
          <li>✉ contact@adhimi.com</li>
          <li>🏢 Yaoundé, Cameroun</li>
        </ul>
      </div>

      <!-- Réseaux sociaux -->
      <div>
        <h3 class="font-semibold mb-4 text-[rgba(251,146,60,1)]">Suivez-nous</h3>
        <div class="flex items-center gap-4">
          <a href="#" class="hover:text-[rgba(139,92,246,1)]">Facebook</a>
          <a href="#" class="hover:text-[rgba(251,146,60,1)]">Instagram</a>
          <a href="#" class="hover:text-[rgba(139,92,246,1)]">Twitter</a>
        </div>
      </div>
    </div>

    <div class="border-t border-gray-700 mt-6 py-4 text-center text-gray-400 text-sm">
      © 2026 ADHIMI. Tous droits réservés.
    </div>
  </footer>
</template>

<script setup>
// Aucun JS nécessaire pour l'instant
</script>
