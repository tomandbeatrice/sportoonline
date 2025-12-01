import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

// Layouts
const AdminPanelLayout = () => import('@/layouts/AdminPanelLayout.vue')
const SellerPanelLayout = () => import('@/layouts/SellerPanelLayout.vue')
const UserPanelLayout = () => import('@/layouts/UserPanelLayout.vue')

// Admin Pages
const AdminDashboard = () => import('@/pages/admin/Dashboard.vue')
const AdminOrders = () => import('@/pages/admin/Orders.vue')
const AdminSellers = () => import('@/pages/admin/Sellers.vue')
const AdminCustomers = () => import('@/pages/admin/Customers.vue')
const AdminProducts = () => import('@/pages/admin/Products.vue')
const AdminCategories = () => import('@/pages/admin/Categories.vue')
const AdminSettings = () => import('@/pages/admin/Settings.vue')

// Buyer Pages
const MarketplaceHome = () => import('@/components/marketplace/MarketplaceHome.vue')
const BuyerProductDetail = () => import('@/pages/buyer/ProductDetail.vue')
const BuyerSearchResults = () => import('@/pages/buyer/SearchResults.vue')
const BuyerCart = () => import('@/pages/buyer/Cart.vue')

// Marketplace Service Pages
const FoodPage = () => import('@/views/marketplace/FoodPage.vue')
const HotelPage = () => import('@/views/marketplace/HotelPage.vue')
const HotelDetail = () => import('@/views/marketplace/HotelDetail.vue')
const RideSharePage = () => import('@/views/marketplace/RideSharePage.vue')
const ServicesPage = () => import('@/views/marketplace/ServicesPage.vue')
const CareersPage = () => import('@/views/marketplace/CareersPage.vue')

// Auth
const Login = () => import('@/views/auth/Login.vue')
const Register = () => import('@/views/auth/Register.vue')

// Fallback
const NotFound = () => import('@/views/NotFound.vue')

const routes: RouteRecordRaw[] = [
  // ========== BUYER ==========
  { path: '/', name: 'Home', component: MarketplaceHome },
  { path: '/products/:id', name: 'ProductDetail', component: BuyerProductDetail },
  { path: '/search', name: 'SearchResults', component: BuyerSearchResults },
  { path: '/cart', name: 'Cart', component: BuyerCart, meta: { requiresAuth: true } },

  // ========== MARKETPLACE SERVICES ==========
  { path: '/food', name: 'Food', component: FoodPage },
  { path: '/hotels', name: 'Hotels', component: HotelPage },
  { path: '/hotels/:id', name: 'HotelDetail', component: HotelDetail },
  { path: '/rides', name: 'Rides', component: RideSharePage },
  { path: '/services', name: 'Services', component: ServicesPage },
  { path: '/career', name: 'Career', component: CareersPage },

  // ========== USER (Kullanıcı Paneli) ==========
  {
    path: '/user',
    component: UserPanelLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'UserDashboard', component: () => import('@/pages/user/Dashboard.vue') },
      { path: 'dashboard', redirect: '/user' },
      { path: 'orders', name: 'UserOrders', component: () => import('@/pages/user/Orders.vue') },
      { path: 'favorites', name: 'UserFavorites', component: () => import('@/pages/user/Favorites.vue') },
      { path: 'profile', name: 'UserProfile', component: () => import('@/pages/user/Profile.vue') },
      { path: 'addresses', name: 'UserAddresses', component: () => import('@/pages/user/Addresses.vue') },
      { path: 'reviews', name: 'UserReviews', component: () => import('@/pages/user/Reviews.vue') },
      { path: 'coupons', name: 'UserCoupons', component: () => import('@/pages/user/Coupons.vue') },
      { path: 'settings', name: 'UserSettings', component: () => import('@/pages/user/Settings.vue') },
      { path: 'notifications', name: 'UserNotifications', component: () => import('@/pages/user/Notifications.vue') },
      { path: 'support', name: 'UserSupport', component: () => import('@/pages/user/Support.vue') },
    ]
  },

  // ========== SELLER ==========
  {
    path: '/seller',
    component: SellerPanelLayout,
    // meta: { requiresAuth: true, roles: ['seller'] }, // DEV: Auth geçici kapalı
    children: [
      { path: '', name: 'SellerDashboard', component: () => import('@/pages/seller/Dashboard.vue') },
      { path: 'dashboard', redirect: '/seller' },
      { path: 'products', name: 'SellerProducts', component: () => import('@/pages/seller/ProductManagement.vue') },
      { path: 'orders', name: 'SellerOrders', component: () => import('@/pages/seller/Orders.vue') },
      { path: 'stock', name: 'SellerStock', component: () => import('@/pages/seller/StockManagement.vue') },
      { path: 'finance', name: 'SellerFinance', component: () => import('@/pages/seller/Finance.vue') },
      { path: 'campaigns', name: 'SellerCampaigns', component: () => import('@/pages/seller/Campaigns.vue') },
      { path: 'reports', name: 'SellerReports', component: () => import('@/pages/seller/Reports.vue') },
      { path: 'analytics', name: 'SellerAnalytics', component: () => import('@/pages/seller/Analytics.vue') },
      { path: 'promotions', name: 'SellerPromotions', component: () => import('@/pages/seller/Promotions.vue') },
      { path: 'messages', name: 'SellerMessages', component: () => import('@/pages/seller/CustomerMessages.vue') },
      { path: 'reviews', name: 'SellerReviews', component: () => import('@/pages/seller/Reviews.vue') },
      { path: 'support', name: 'SellerSupport', component: () => import('@/pages/seller/SupportCenter.vue') },
      { path: 'settings', name: 'SellerSettings', component: () => import('@/pages/seller/Settings.vue') },
      { path: 'questions', name: 'SellerQuestions', component: () => import('@/pages/seller/ProductQuestions.vue') },
      { path: 'store-settings', name: 'SellerStoreSettings', component: () => import('@/pages/seller/StoreSettings.vue') },
      { path: 'shipping', name: 'SellerShipping', component: () => import('@/pages/seller/ShippingSettings.vue') },
    ]
  },

  // ========== ADMIN ==========
  {
    path: '/admin',
    component: AdminPanelLayout,
    meta: { requiresAuth: true, roles: ['admin'] },
    children: [
      { path: '', name: 'AdminDashboard', component: AdminDashboard },
      { path: 'dashboard', redirect: '/admin' },
      { path: 'orders', name: 'AdminOrders', component: AdminOrders },
      { path: 'sellers', name: 'AdminSellers', component: AdminSellers },
      { path: 'seller-applications', name: 'AdminSellerApplications', component: () => import('@/pages/admin/SellerApplications.vue') },
      { path: 'customers', name: 'AdminCustomers', component: AdminCustomers },
      { path: 'products', name: 'AdminProducts', component: AdminProducts },
      { path: 'variants', name: 'AdminVariants', component: () => import('@/pages/admin/VariantManager.vue') },
      { path: 'categories', name: 'AdminCategories', component: AdminCategories },
      { path: 'campaigns', name: 'AdminCampaigns', component: () => import('@/pages/admin/Campaigns.vue') },
      { path: 'banners', name: 'AdminBanners', component: () => import('@/pages/admin/Banners.vue') },
      { path: 'pages', name: 'AdminPages', component: () => import('@/pages/admin/Pages.vue') },
      { path: 'reports', name: 'AdminReports', component: () => import('@/pages/admin/Reports.vue') },
      { path: 'shipping', name: 'AdminShipping', component: () => import('@/pages/admin/ShippingSettings.vue') },
      { path: 'payment', name: 'AdminPayment', component: () => import('@/pages/admin/PaymentSettings.vue') },
      { path: 'stock', name: 'AdminStock', component: () => import('@/pages/admin/StockManagement.vue') },
      { path: 'notifications', name: 'AdminNotifications', component: () => import('@/pages/admin/NotificationCenter.vue') },
      { path: 'support', name: 'AdminSupport', component: () => import('@/pages/admin/SupportCenter.vue') },
      { path: 'coupons', name: 'AdminCoupons', component: () => import('@/pages/admin/CouponManagement.vue') },
      { path: 'commissions', name: 'AdminCommissions', component: () => import('@/pages/admin/CommissionSettings.vue') },
      { path: 'subscription-plans', name: 'AdminSubscriptionPlans', component: () => import('@/pages/admin/SubscriptionPlans.vue') },
      { path: 'reviews', name: 'AdminReviews', component: () => import('@/pages/admin/ReviewManagement.vue') },
      { path: 'email-templates', name: 'AdminEmailTemplates', component: () => import('@/pages/admin/EmailTemplates.vue') },
      { path: 'roles', name: 'AdminRoles', component: () => import('@/pages/admin/RoleManagement.vue') },
      { path: 'activity-logs', name: 'AdminActivityLogs', component: () => import('@/pages/admin/ActivityLogs.vue') },
      { path: 'labels', name: 'AdminLabels', component: () => import('@/pages/admin/LabelSystem.vue') },
      { path: 'finance', name: 'AdminFinance', component: () => import('@/pages/admin/FinancialManagement.vue') },
      { path: 'advanced-reports', name: 'AdminAdvancedReports', component: () => import('@/pages/admin/AdvancedReports.vue') },
      { path: 'settings', name: 'AdminSettings', component: AdminSettings },
    ]
  },

  // ========== AUTH ==========
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },

  // ========== FALLBACK ==========
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Auth Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const userRole = localStorage.getItem('role')

  if (to.meta.requiresAuth && !token) {
    return next({ name: 'Login', query: { redirect: to.fullPath } })
  }

  if (to.meta.roles && Array.isArray(to.meta.roles)) {
    if (!userRole || !to.meta.roles.includes(userRole)) {
      if (userRole === 'admin') return next('/admin')
      if (userRole === 'seller') return next('/seller/dashboard')
      return next('/')
    }
  }

  next()
})

export default router
