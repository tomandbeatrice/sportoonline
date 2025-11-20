import { createRouter, createWebHistory } from 'vue-router'

import ProductList from '@/views/products/ProductList.vue'
import Cart from '@/views/products/Cart.vue'
import Checkout from '@/views/products/Checkout.vue'
import OrderStatus from '@/views/products/OrderStatus.vue'

// Auth routes
const Login = () => import('@/components/user/Login.vue')
const Register = () => import('@/components/user/Register.vue')

// Seller routes
const SellerDashboard = () => import('@/components/seller/SellerDashboard.vue')

// Admin routes
const AdminDashboard = () => import('@/components/admin/AdminDashboard.vue')

const routes = [
  { path: '/', name: 'ProductList', component: ProductList },
  { path: '/cart', name: 'Cart', component: Cart },
  { path: '/checkout', name: 'Checkout', component: Checkout, meta: { requiresAuth: true } },
  { path: '/orders', name: 'OrderStatus', component: OrderStatus, meta: { requiresAuth: true } },
  
  // Auth
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  
  // Seller
  { path: '/seller', name: 'SellerDashboard', component: SellerDashboard, meta: { requiresAuth: true, role: 'seller' } },
  
  // Admin
  { path: '/admin', name: 'AdminDashboard', component: AdminDashboard, meta: { requiresAuth: true, role: 'admin' } }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Route guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  
  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else if (to.meta.role) {
    // Rol kontrolü için user bilgisi gerekli
    // TODO: User bilgisini store'dan veya API'den çek
    next();
  } else {
    next();
  }
});

export default router