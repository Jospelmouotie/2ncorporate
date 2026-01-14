<template>
  <div class="min-h-screen bg-slate-50 py-24 px-4 md:px-10 mt-10">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center gap-4 mb-12">
        <div class="h-12 w-2 bg-orange-500 rounded-full"></div>
        <h1 class="text-5xl font-black uppercase italic tracking-tighter text-slate-900">Mon Panier</h1>
      </div>

      <div v-if="cartStore.items.length > 0" class="flex flex-col lg:flex-row gap-10">
        <div class="flex-grow space-y-4">
          <div v-for="item in cartStore.items" :key="item.id"
               class="bg-white p-6 rounded-[2.5rem] flex flex-wrap md:flex-nowrap items-center gap-6 shadow-sm border border-slate-100">
            <template v-if="item.product">
              <img :src="item.product.image_url" class="w-24 h-24 object-contain rounded-2xl bg-slate-50 p-2" />
              <div class="flex-grow">
                <h3 class="font-black uppercase text-slate-800">{{ item.product.name }}</h3>
                <p class="text-orange-600 font-black text-lg">{{ formatPrice(item.product.final_price) }} F</p>
              </div>

              <div class="flex items-center border-2 border-slate-100 rounded-2xl p-1 bg-slate-50">
                <button @click="cartStore.updateQuantity(item.product_id, item.quantity - 1)"
                        class="w-8 h-8 font-black hover:bg-white rounded-lg transition" :disabled="item.quantity <= 1">−</button>
                <span class="w-10 text-center font-black">{{ item.quantity }}</span>
                <button @click="cartStore.updateQuantity(item.product_id, item.quantity + 1)"
                        class="w-8 h-8 font-black hover:bg-white rounded-lg transition">+</button>
              </div>

              <div class="text-right min-w-[120px]">
                <p class="font-black text-xl text-slate-900">{{ formatPrice(item.product.final_price * item.quantity) }} F</p>
                <button @click="removeProduct(item.product_id)" class="text-red-500 flex items-center gap-1 ml-auto mt-2">
                  <Trash2 class="w-4 h-4" /> <span class="text-[10px] font-black uppercase">Retirer</span>
                </button>
              </div>
            </template>
          </div>
        </div>

        <div class="w-full lg:w-[400px]">
          <div class="bg-slate-900 text-white p-8 rounded-[3rem] sticky top-32 shadow-2xl">
            <h2 class="text-2xl font-black italic mb-6">RÉSUMÉ</h2>
            <div class="space-y-2 mb-8 border-b border-slate-800 pb-6">
              <div class="flex justify-between text-slate-400 font-bold">
                <span>Sous-total</span>
                <span>{{ formatPrice(cartStore.totalPrice) }} F</span>
              </div>
              <div class="flex justify-between text-slate-400 font-bold">
                <span>Livraison</span>
                <span class="text-green-400 uppercase text-xs">Gratuite</span>
              </div>
            </div>

            <p class="text-5xl font-black text-orange-500 mb-8 tracking-tighter">{{ formatPrice(cartStore.totalPrice) }} F</p>

            <div class="space-y-4">
              <button
                @click="handleCheckout"
                :disabled="isProcessing"
                class="w-full bg-orange-600 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-700 transition active:scale-95 disabled:opacity-50 flex items-center justify-center gap-3"
              >
                <span v-if="isProcessing" class="animate-spin border-2 border-white border-t-transparent rounded-full h-5 w-5"></span>
                {{ isProcessing ? 'Traitement...' : 'Passer la commande' }}
              </button>
              <router-link to="/shop" class="block text-center text-slate-400 font-bold text-xs uppercase tracking-widest hover:text-white transition">
                Continuer mes achats
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-20 bg-white rounded-[4rem] border-4 border-dashed border-slate-100">
        <div class="text-8xl mb-6">🛒</div>
        <h2 class="text-3xl font-black uppercase italic text-slate-300">Votre panier est vide</h2>
        <router-link to="/shop" class="mt-8 inline-block bg-slate-900 text-white px-12 py-5 rounded-[2rem] font-black uppercase hover:bg-orange-500 transition-all">
          Découvrir nos produits
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cartStore'
import { Trash2 } from 'lucide-vue-next'
import axios from 'axios'

const cartStore = useCartStore()
const router = useRouter()
const isProcessing = ref(false)

const formatPrice = (p) => new Intl.NumberFormat('fr-FR').format(p)

const removeProduct = (id) => {
  if (confirm("Retirer cet article ?")) cartStore.removeItem(id)
}

const handleCheckout = async () => {
  if (!cartStore.cartId || isProcessing.value) return

  isProcessing.value = true
  try {
    const res = await axios.post('/api/orders', {
      cart_id: cartStore.cartId,
      shipping_address: "Douala, Cameroun (Adresse test)", // À remplacer par un vrai formulaire
      payment_method: "cash"
    })

    if (res.data.success) {
      cartStore.clearCart()
      alert("Félicitations ! Votre commande a été enregistrée.")
      router.push('/shop') // Ou vers une page "Mes Commandes"
    }
  } catch (error) {
    alert(error.response?.data?.message || "Erreur lors de la commande")
  } finally {
    isProcessing.value = false
  }
}
</script>
