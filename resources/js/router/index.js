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

// Pages admin
// Remplace tes imports Admin (lignes 16 à 20) par ceux-ci :
import Dashboard from '@/Components/Admin/Dashboard.vue'
import ProductList from '@/Components/Admin/Products/ProductList.vue'
import CategoryList from '@/Components/Admin/Categories/CategoryList.vue'
import PromotionDashboard from '@/Components/Admin/Promotions/PromotionDashboard.vue'
import AdvertisementDashboard from '@/Components/Admin/Advertisements/AdvertisementDashboard.vue'
import ProductsByCategory from '../Pages/ProductsByCategory.vue'
import ProductDetail from '../Components/product/ProductDetail.vue'
import Shop from '../Pages/Shop.vue'

const routes = [
  // Client
  { path: '/', name: 'home', component: Home },
  { path: '/shop', name: 'shop', component: Shop },
  { path: '/product/:id', name: 'product', component: ProductPage },
    { path: '/product/:id', name: 'product.detail', component: ProductDetail },
  { path: '/promotions', name: 'promotions', component: Promotions },
  { path: '/cart', name: 'cart', component: Cart },
  { path: '/wishlist', name: 'wishlist', component: Wishlist },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
{ path: '/category/:id/:slug', name: 'category', component: ProductsByCategory },

  // Admin
  { path: '/admin', name: 'admin.dashboard', component: Dashboard },
  { path: '/admin/products', name: 'admin.products', component: ProductList },
  { path: '/admin/categories', name: 'admin.categories', component: CategoryList },
  { path: '/admin/promotions', name: 'admin.promotions', component: PromotionDashboard },
  { path: '/admin/advertisements', name: 'admin.ads', component: AdvertisementDashboard },

  // Not found
  { path: '/:pathMatch(.*)*', name: 'notfound', component: NotFound },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
