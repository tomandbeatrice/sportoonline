import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

// Marketplace pages
const Home = () => import('@/views/marketplace/Home.vue')
const ProductDetail = () => import('@/views/marketplace/ProductDetail.vue')
const Login = () => import('@/views/auth/Login.vue')
const Register = () => import('@/views/auth/Register.vue')
const ApplyAsSeller = () => import('@/views/seller/ApplyAsSeller.vue')
const Cart = () => import('@/views/cart/Cart.vue')
const Checkout = () => import('@/views/cart/Checkout.vue')

// Buyer pages
const BuyerDashboard = () => import('@/views/buyer/BuyerDashboard.vue')
const OrderTracking = () => import('@/views/orders/OrderTracking.vue')

// Admin pages
const AdminDashboard = () => import('@/views/admin/AdminDashboard.vue')
const ImprovedDashboard = () => import('@/views/admin/ImprovedDashboard.vue')
const SellerManagement = () => import('@/views/admin/SellerManagement.vue')
const OrderManagement = () => import('@/views/admin/OrderManagement.vue')
const CustomerManagement = () => import('@/views/admin/CustomerManagement.vue')
const CategoryManagement = () => import('@/views/admin/CategoryManagement.vue')
const BannerManagement = () => import('@/views/admin/BannerManagement.vue')
const PageManagement = () => import('@/views/admin/PageManagement.vue')
const ReportsAnalytics = () => import('@/views/admin/ReportsAnalytics.vue')
const SystemSettings = () => import('@/views/admin/SystemSettings.vue')
const NotificationCenter = () => import('@/views/admin/NotificationCenter.vue')
const ThemeManagement = () => import('@/views/admin/ThemeManagement.vue')
const SellerApplications = () => import('@/views/admin/SellerApplications.vue')
const TurboWinners = () => import('@/components/admin/TurboWinners.vue')

// Seller pages
const SellerDashboard = () => import('@/views/seller/SellerDashboard.vue')

// Test
const Test = () => import('@/views/Test.vue')
const TestingDashboard = () => import('@/views/TestingDashboard.vue')

// C2C Marketplace
const C2CMarketplaceDashboard = () => import('@/views/marketplace/C2CMarketplaceDashboard.vue')

// Fallback
const NotFound = () => import('@/views/NotFound.vue')

const routes: RouteRecordRaw[] = [
  // Test
  { path: '/test', name: 'Test', component: Test },
  { path: '/testing', name: 'TestingDashboard', component: TestingDashboard },
  
  // C2C Marketplace Dashboard (Universal - adapts to user role)
  { path: '/dashboard', name: 'C2CMarketplaceDashboard', component: C2CMarketplaceDashboard, meta: { requiresAuth: true } },
  
  // Marketplace routes
  { path: '/', name: 'Home', component: Home },
  { path: '/products/:id', name: 'ProductDetail', component: ProductDetail },
  { path: '/cart', name: 'Cart', component: Cart, meta: { requiresAuth: true } },
  { path: '/checkout', name: 'Checkout', component: Checkout, meta: { requiresAuth: true } },
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/apply-seller', name: 'ApplyAsSeller', component: ApplyAsSeller, meta: { requiresAuth: true } },
  
  // Buyer routes
  { path: '/buyer/dashboard', name: 'BuyerDashboard', component: BuyerDashboard, meta: { requiresAuth: true, roles: ['buyer'] } },
  { path: '/orders/:id', name: 'OrderTracking', component: OrderTracking, meta: { requiresAuth: true } },
  { path: '/payment/success', name: 'PaymentSuccess', component: () => import('@/views/PaymentSuccess.vue') },
  { path: '/payment/failed', name: 'PaymentFailed', component: () => import('@/views/PaymentFailed.vue') },
  
  // Admin routes
  { path: '/admin/dashboard', name: 'AdminDashboard', component: AdminDashboard, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/dashboard-new', name: 'ImprovedDashboard', component: ImprovedDashboard, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/sellers', name: 'SellerManagement', component: SellerManagement, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/orders', name: 'OrderManagement', component: OrderManagement, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/customers', name: 'CustomerManagement', component: CustomerManagement, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/categories', name: 'CategoryManagement', component: CategoryManagement, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/banners', name: 'BannerManagement', component: BannerManagement, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/pages', name: 'PageManagement', component: PageManagement, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/reports', name: 'ReportsAnalytics', component: ReportsAnalytics, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/settings', name: 'SystemSettings', component: SystemSettings, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/notifications', name: 'NotificationCenter', component: NotificationCenter, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/theme', name: 'ThemeManagement', component: ThemeManagement, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/seller-applications', name: 'SellerApplications', component: SellerApplications, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/turbo-winners', name: 'TurboWinners', component: TurboWinners, meta: { requiresAuth: true, roles: ['admin'] } },
  
  // Seller routes
  { path: '/seller/dashboard', name: 'SellerDashboard', component: SellerDashboard, meta: { requiresAuth: true, roles: ['seller'] } },

  // 404
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Role guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const userRole = localStorage.getItem('role')
  
  console.log('🔒 Route Guard:', { 
    to: to.path, 
    from: from.path,
    requiresAuth: to.meta.requiresAuth, 
    roles: to.meta.roles,
    hasToken: !!token, 
    userRole 
  })
  
  // If route requires auth and no token, redirect to login
  if (to.meta.requiresAuth && !token) {
    console.log('❌ No token, redirecting to login')
    return next({ name: 'Login', query: { redirect: to.fullPath } })
  }
  
  // If route has role requirements, check user role
  if (to.meta.roles && Array.isArray(to.meta.roles)) {
    if (!userRole || !to.meta.roles.includes(userRole)) {
      console.log('❌ Role mismatch:', userRole, 'not in', to.meta.roles)
      alert('Bu sayfaya erişim yetkiniz yok!')
      
      // Redirect based on user role
      if (userRole === 'admin') {
        return next({ name: 'AdminDashboard' })
      } else if (userRole === 'seller') {
        return next({ name: 'SellerDashboard' })
      } else if (userRole === 'buyer') {
        return next({ name: 'BuyerDashboard' })
      } else {
        return next({ name: 'Home' })
      }
    }
  }

  console.log('✅ Access granted to', to.path)
  next()
})

export default router