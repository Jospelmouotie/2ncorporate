// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// Pages client
import Home from '@/Pages/Home.vue'
import ProductPage from '@/Pages/ProductPage.vue'
import Promotions from '@/Pages/Promotions.vue'
import Cart from '@/Pages/Cart.vue'
import Wishlist from '@/Pages/Wishlist.vue'
import Login from '@/Pages/Login.vue'
import Register from '@/Pages/Register.vue'
import NotFound from '@/Pages/NotFound.vue'
import ProductsByCategory from '../Pages/ProductsByCategory.vue'
import ProductDetail from '../Components/product/ProductDetail.vue'
import Shop from '../Pages/Shop.vue'

// Pages admin
import Dashboard from '@/Components/Admin/Dashboard.vue'
import ProductList from '@/Components/Admin/Products/ProductList.vue'
import CategoryList from '@/Components/Admin/Categories/CategoryList.vue'
import PromotionDashboard from '@/Components/Admin/Promotions/PromotionDashboard.vue'
import AdvertisementDashboard from '@/Components/Admin/Advertisements/AdvertisementDashboard.vue'

const routes = [
  // --- ROUTES CLIENT ---
  { path: '/', name: 'home', component: Home },
  { path: '/shop', name: 'shop', component: Shop },

  // Doublons conservés comme demandé
  { path: '/product/:id', name: 'product', component: ProductPage },
  { path: '/product/:id', name: 'product.detail', component: ProductDetail },

  { path: '/promotions', name: 'promotions', component: Promotions },
  { path: '/cart', name: 'cart', component: Cart },
  { path: '/wishlist', name: 'wishlist', component: Wishlist },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/category/:id/:slug', name: 'category', component: ProductsByCategory },

  // --- ROUTES ADMIN (Protégées) ---
  {
    path: '/admin',
    name: 'admin.dashboard',
    component: Dashboard,
    meta: { requiresAdmin: true }
  },
  {
    path: '/admin/products',
    name: 'admin.products',
    component: ProductList,
    meta: { requiresAdmin: true }
  },
  {
    path: '/admin/categories',
    name: 'admin.categories',
    component: CategoryList,
    meta: { requiresAdmin: true }
  },
  {
    path: '/admin/promotions',
    name: 'admin.promotions',
    component: PromotionDashboard,
    meta: { requiresAdmin: true }
  },
  {
    path: '/admin/advertisements',
    name: 'admin.ads',
    component: AdvertisementDashboard,
    meta: { requiresAdmin: true }
  },

  // Not found
  { path: '/:pathMatch(.*)*', name: 'notfound', component: NotFound },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

/**
 * PROTECTION DES ROUTES
 * Vérifie si l'utilisateur est admin avant d'accéder aux pages /admin
 */
// resources/js/router/index.js

router.beforeEach((to, from, next) => {
  const user = window.user // Récupère l'utilisateur injecté par Blade

  if (to.matched.some(record => record.meta.requiresAdmin)) {
    if (!user) {
      // Pas connecté du tout
      next({ name: 'login' })
    } else if (user.is_admin !== true) {
      // Connecté mais pas admin
      alert('Accès réservé aux administrateurs')
      next({ name: 'home' })
    } else {
      // Admin, on autorise
      next()
    }
  } else {
    next()
  }
})
export default router
