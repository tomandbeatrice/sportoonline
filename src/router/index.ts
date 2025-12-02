import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

// Marketplace
const MarketplaceHome = () => import('@/components/marketplace/MarketplaceHome.vue')

const routes: RouteRecordRaw[] = [
  // Marketplace Home
  { path: '/', name: 'Home', component: MarketplaceHome },
  
  // Fallback
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: () => import('@/pages/AccessDenied.vue') }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
