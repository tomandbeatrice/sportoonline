import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Landing',
    component: () => import('@/views/Landing.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/Register.vue')
  },
  {
    path: '/demo',
    name: 'Demo',
    component: () => import('@/views/Demo.vue'),
    meta: {
      public: true,
      layout: 'demo',
      title: 'Demo Ortamı – Satıcı & Alıcı Testi'
    }
  },
  {
    path: '/early-access',
    name: 'EarlyAccess',
    component: () => import('@/views/EarlyAccess.vue'),
    meta: {
      public: true,
      layout: 'landing',
      title: 'İlk 100 Satıcı – Sportoonline'
    }
  },
  {
    path: '/seller/dashboard',
    name: 'SellerDashboard',
    component: () => import('@/views/seller/Dashboard.vue'),
    meta: { requiresAuth: true, role: 'seller' }
  },
  {
    path: '/buyer/home',
    name: 'BuyerHome',
    component: () => import('@/views/buyer/Home.vue'),
    meta: { requiresAuth: true, role: 'buyer' }
  },
  {
    path: '/admin/panel',
    name: 'AdminPanel',
    component: () => import('@/views/admin/Panel.vue'),
    meta: { requiresAuth: true, role: 'admin' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 })
})

export default router