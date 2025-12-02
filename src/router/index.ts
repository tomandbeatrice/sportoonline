import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

// Marketplace
const MarketplaceHome = () => import('@/components/marketplace/MarketplaceHome.vue')
const AccessDenied = () => import('@/pages/AccessDenied.vue')

// Simple placeholder component for under construction pages
const PlaceholderPage = { 
  template: `
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
      <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
        <div class="text-6xl mb-4">🚧</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Yakında!</h1>
        <p class="text-gray-600 mb-6">Bu sayfa şu anda geliştirme aşamasında.</p>
        <router-link to="/" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
          Ana Sayfaya Dön
        </router-link>
      </div>
    </div>
  `
}

const routes: RouteRecordRaw[] = [
  // Marketplace Home
  { path: '/', name: 'Home', component: MarketplaceHome },
  
  // Marketplace Services (Placeholder)
  { path: '/food', name: 'Food', component: PlaceholderPage },
  { path: '/hotels', name: 'Hotels', component: PlaceholderPage },
  { path: '/rides', name: 'Rides', component: PlaceholderPage },
  { path: '/services', name: 'Services', component: PlaceholderPage },
  { path: '/career', name: 'Career', component: PlaceholderPage },
  
  // Auth (Placeholder)
  { path: '/login', name: 'Login', component: PlaceholderPage },
  { path: '/register', name: 'Register', component: PlaceholderPage },
  
  // Fallback
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: AccessDenied }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
