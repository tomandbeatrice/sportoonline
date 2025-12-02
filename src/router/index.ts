import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

// Marketplace
const MarketplaceHome = () => import('@/components/marketplace/MarketplaceHome.vue')
const AccessDenied = () => import('@/pages/AccessDenied.vue')
const ComingSoon = () => import('@/pages/ComingSoon.vue')

// Product & Shopping
const ProductList = () => import('@/views/product/ProductList.vue')
const ProductDetail = () => import('@/views/product/ProductDetail.vue')
const Cart = () => import('@/views/cart/Cart.vue')
const Checkout = () => import('@/views/cart/Checkout.vue')

// Orders
const OrderList = () => import('@/views/order/OrderList.vue')
const OrderDetail = () => import('@/views/order/OrderDetail.vue')
const OrderTrack = () => import('@/views/order/OrderTrack.vue')

// Auth
const Login = () => import('@/views/auth/Login.vue')
const Register = () => import('@/views/auth/Register.vue')

// Seller
const ApplyAsSeller = () => import('@/views/seller/ApplyAsSeller.vue')
const SellerProducts = () => import('@/views/seller/SellerProducts.vue')

// Admin
const AdminDashboard = () => import('@/views/admin/AdminDashboard.vue')
const AdminOrders = () => import('@/views/admin/OrderManagement.vue')
const AdminSellers = () => import('@/views/admin/SellerManagement.vue')
const AdminCustomers = () => import('@/views/admin/CustomerManagement.vue')
const AdminCategories = () => import('@/views/admin/CategoryManagement.vue')
const AdminBanners = () => import('@/views/admin/BannerManagement.vue')
const AdminReports = () => import('@/views/admin/ReportsAnalytics.vue')
const AdminSettings = () => import('@/views/admin/SystemSettings.vue')

const routes: RouteRecordRaw[] = [
  // Marketplace Home
  { path: '/', name: 'Home', component: MarketplaceHome },
  
  // Products & Shopping
  { path: '/products', name: 'Products', component: ProductList },
  { path: '/products/:id', name: 'ProductDetail', component: ProductDetail },
  { path: '/cart', name: 'Cart', component: Cart },
  { path: '/checkout', name: 'Checkout', component: Checkout },
  
  // Orders
  { path: '/orders', name: 'Orders', component: OrderList },
  { path: '/orders/:id', name: 'OrderDetail', component: OrderDetail },
  { path: '/orders/:id/track', name: 'OrderTrack', component: OrderTrack },
  
  // Seller
  { path: '/apply-seller', name: 'ApplySeller', component: ApplyAsSeller },
  { path: '/seller/products', name: 'SellerProducts', component: SellerProducts },
  
  // Campaigns
  { path: '/campaigns', name: 'Campaigns', component: ComingSoon },
  
  // Marketplace Services
  { path: '/food', name: 'Food', component: ComingSoon },
  { path: '/hotels', name: 'Hotels', component: ComingSoon },
  { path: '/rides', name: 'Rides', component: ComingSoon },
  { path: '/services', name: 'Services', component: ComingSoon },
  { path: '/career', name: 'Career', component: ComingSoon },
  
  // Auth
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  
  // Admin
  { 
    path: '/admin', 
    children: [
      { path: '', redirect: '/admin/dashboard' },
      { path: 'dashboard', name: 'AdminDashboard', component: AdminDashboard },
      { path: 'orders', name: 'AdminOrders', component: AdminOrders },
      { path: 'sellers', name: 'AdminSellers', component: AdminSellers },
      { path: 'customers', name: 'AdminCustomers', component: AdminCustomers },
      { path: 'categories', name: 'AdminCategories', component: AdminCategories },
      { path: 'banners', name: 'AdminBanners', component: AdminBanners },
      { path: 'reports', name: 'AdminReports', component: AdminReports },
      { path: 'settings', name: 'AdminSettings', component: AdminSettings },
    ]
  },
  
  // Fallback
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: AccessDenied }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
