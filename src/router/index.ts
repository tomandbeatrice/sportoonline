import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

// Marketplace
const MarketplaceHome = () => import('@/components/marketplace/MarketplaceHome.vue')
const AccessDenied = () => import('@/pages/AccessDenied.vue')
const ComingSoon = () => import('@/pages/ComingSoon.vue')

const routes: RouteRecordRaw[] = [
  // Marketplace Home
  { path: '/', name: 'Home', component: MarketplaceHome },
  
  // Navbar Links
  { path: '/products', name: 'Products', component: ComingSoon },
  { path: '/apply-seller', name: 'ApplySeller', component: ComingSoon },
  { path: '/campaigns', name: 'Campaigns', component: ComingSoon },
  { path: '/orders', name: 'Orders', component: ComingSoon },
  { path: '/cart', name: 'Cart', component: ComingSoon },
  
  // Marketplace Services (Coming Soon)
  { path: '/food', name: 'Food', component: ComingSoon },
  { path: '/hotels', name: 'Hotels', component: ComingSoon },
  { path: '/rides', name: 'Rides', component: ComingSoon },
  { path: '/services', name: 'Services', component: ComingSoon },
  { path: '/career', name: 'Career', component: ComingSoon },
  
  // Auth (Coming Soon)
  { path: '/login', name: 'Login', component: ComingSoon },
  { path: '/register', name: 'Register', component: ComingSoon },
  
  // Fallback
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: AccessDenied }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
