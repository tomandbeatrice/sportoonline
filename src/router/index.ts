import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

// Marketplace
const SuperHome = () => import('@/views/home/SuperHome.vue')
const MapDiscovery = () => import('@/views/discovery/MapDiscovery.vue')
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
const AdminDashboard = () => import('@/views/admin/AdminDashboardNew.vue')
const AdminOrders = () => import('@/views/admin/OrderManagement.vue')
const AdminSellers = () => import('@/views/admin/SellerManagement.vue')
const AdminCustomers = () => import('@/views/admin/CustomerManagement.vue')
const AdminCategories = () => import('@/views/admin/CategoryManagement.vue')
const AdminBanners = () => import('@/views/admin/BannerManagement.vue')
const AdminReports = () => import('@/views/admin/ReportsAnalytics.vue')
const AdminSettings = () => import('@/views/admin/SystemSettings.vue')
const AdminPages = () => import('@/views/admin/PageManagement.vue')
const AdminThemes = () => import('@/views/admin/ThemeManagement.vue')
const AdminNotifications = () => import('@/views/admin/NotificationCenter.vue')
const AdminSegmentExport = () => import('@/views/admin/SegmentExport.vue')
const AdminExportFiles = () => import('@/views/admin/ExportFileList.vue')
const AdminImproved = () => import('@/views/admin/ImprovedDashboard.vue')
const AdminSellerApplications = () => import('@/views/admin/SellerApplications.vue')

// Campaigns
const CampaignList = () => import('@/views/CampaignList.vue')
const CampaignCreate = () => import('@/views/CampaignCreate.vue')
const CampaignDetail = () => import('@/views/CampaignDetail.vue')
const CampaignEdit = () => import('@/views/CampaignEdit.vue')
const CampaignDelete = () => import('@/views/CampaignDelete.vue')
const CampaignFeedback = () => import('@/views/CampaignFeedback.vue')
const CampaignStats = () => import('@/views/CampaignStats.vue')
const CampaignScore = () => import('@/views/CampaignScore.vue')
const CampaignTabs = () => import('@/views/CampaignTabs.vue')

// Segments
const SegmentManager = () => import('@/views/segment/SegmentManager.vue')
const SegmentAnalytics = () => import('@/views/segment/SegmentAnalytics.vue')
const SegmentHeatmap = () => import('@/views/segment/SegmentHeatmap.vue')
const SegmentStrategy = () => import('@/views/segment/SegmentStrategyPanel.vue')
const SegmentExportScheduler = () => import('@/views/segment/SegmentExportScheduler.vue')
const SegmentExportHistory = () => import('@/views/segment/SegmentExportHistory.vue')
const SegmentAccuracy = () => import('@/views/segment/SegmentAccuracyChart.vue')
const SegmentAccuracyBreakdown = () => import('@/views/segment/SegmentAccuracyBreakdown.vue')
const SegmentSuccessPrediction = () => import('@/views/segment/SegmentSuccessPrediction.vue')
const SegmentSuccessRegression = () => import('@/views/segment/SegmentSuccessRegression.vue')

// Seller (extended)
const SellerLogin = () => import('@/views/seller/SellerLogin.vue')
const SellerRegistration = () => import('@/views/seller/SellerRegistration.vue')
const SellerOnboarding = () => import('@/views/seller/SellerOnboarding.vue')
const SellerCampaign = () => import('@/views/seller/SellerCampaign.vue')
const SellerFinancialReport = () => import('@/views/seller/SellerWallet.vue')

// Marketplace & Storefront
const C2CMarketplace = () => import('@/views/marketplace/C2CMarketplaceDashboard.vue')
const BuyerDashboard = () => import('@/views/buyer/BuyerDashboardNew.vue')
const UnifiedDashboard = () => import('@/views/unified/UnifiedDashboard.vue')
const Search = () => import('@/views/storefront/Search.vue')
const Category = () => import('@/views/storefront/Category.vue')
const ProductPublic = () => import('@/views/storefront/ProductPublic.vue')

// User
const UserProfile = () => import('@/views/user/UserProfile.vue')
const UserEdit = () => import('@/views/user/UserEdit.vue')

// Hotel
const HotelBookingConfirmation = () => import('@/views/hotel/HotelBookingConfirmation.vue')

// Location
const NearbyView = () => import('@/views/location/NearbyView.vue')

// ServiceHub Pages
const FoodHome = () => import('@/views/services/FoodHome.vue')
const HotelHome = () => import('@/views/services/HotelHome.vue')
const RidesHome = () => import('@/views/services/RidesHome.vue')
const ServicesHome = () => import('@/views/services/ServicesHome.vue')
const AIRecommendations = () => import('@/views/services/AIRecommendations.vue')

// ServiceHub Sub-Pages
const RestaurantList = () => import('@/views/services/food/RestaurantList.vue')
const RestaurantDetail = () => import('@/views/services/food/RestaurantDetail.vue')
const FoodCategory = () => import('@/views/services/food/FoodCategory.vue')
const GroupOrder = () => import('@/views/services/food/GroupOrder.vue')
const HotelList = () => import('@/views/services/hotels/HotelList.vue')
const HotelDetailPage = () => import('@/views/services/hotels/HotelDetail.vue')
const HotelSearch = () => import('@/views/services/hotels/HotelSearch.vue')
const RidesSearch = () => import('@/views/services/rides/RidesSearch.vue')
const ServiceList = () => import('@/views/services/service/ServiceList.vue')
const TourList = () => import('@/views/services/tours/TourList.vue')
const CarList = () => import('@/views/services/cars/CarList.vue')
const InsuranceList = () => import('@/views/services/insurance/InsuranceList.vue')
const ActivityList = () => import('@/views/services/activities/ActivityList.vue')
const TourDetail = () => import('@/views/services/tours/TourDetail.vue')
const CarDetail = () => import('@/views/services/cars/CarDetail.vue')
const InsuranceDetail = () => import('@/views/services/insurance/InsuranceDetail.vue')
const ActivityDetail = () => import('@/views/services/activities/ActivityDetail.vue')

// Static Pages
const AboutPage = () => import('@/views/pages/AboutPage.vue')
const ContactPage = () => import('@/views/pages/ContactPage.vue')
const FAQPage = () => import('@/views/pages/FAQPage.vue')
const SellerGuidePage = () => import('@/views/pages/SellerGuidePage.vue')

// Returns
const ReturnRequest = () => import('@/views/returns/ReturnRequest.vue')
const ReturnList = () => import('@/views/returns/ReturnList.vue')
const ReturnDetail = () => import('@/views/returns/ReturnDetail.vue')
const AdminReturnManagement = () => import('@/views/admin/ReturnManagement.vue')

// Other Pages
const PaymentSuccess = () => import('@/views/PaymentSuccess.vue')
const PaymentFailed = () => import('@/views/PaymentFailed.vue')
const SprintCockpit = () => import('@/views/SprintCockpit.vue')
const VisualExport = () => import('@/views/VisualExport.vue')
const Verify = () => import('@/views/Verify.vue')
const Unauthorized = () => import('@/views/Unauthorized.vue')
const Timeout = () => import('@/views/Timeout.vue')
const NotFoundPage = () => import('@/views/NotFound.vue')

const routes: RouteRecordRaw[] = [
  // Marketplace Home
    {
    path: '/',
    name: 'SuperHome',
    component: SuperHome,
    meta: { title: 'Ana Sayfa' }
  },
  {
    path: '/discovery',
    name: 'Discovery',
    component: MapDiscovery,
    meta: { title: 'Keşfet' }
  },
  {
    path: '/nearby',
    name: 'Nearby',
    component: NearbyView,
    meta: { title: 'Yakınımdaki İşletmeler' }
  },
  { path: '/market', name: 'Marketplace', component: MarketplaceHome },
  
  // Products & Shopping
  { path: '/products', name: 'Products', component: ProductList },
  { path: '/products/:id', name: 'ProductDetail', component: ProductDetail },
  { path: '/cart', name: 'Cart', component: Cart },
  { path: '/checkout', name: 'Checkout', component: Checkout },
  
  // Orders
  { path: '/orders', name: 'Orders', component: OrderList },
  { path: '/orders/:id', name: 'OrderDetail', component: OrderDetail },
  { path: '/orders/:id/track', name: 'OrderTrack', component: OrderTrack },
  
  // Returns (User)
  { path: '/returns', name: 'Returns', component: ReturnList },
  { path: '/returns/new', name: 'ReturnCreate', component: ReturnRequest },
  { path: '/returns/:id', name: 'ReturnDetail', component: ReturnDetail },
  
  // Seller
  { path: '/apply-seller', name: 'ApplySeller', component: ApplyAsSeller },
  { path: '/seller/login', name: 'SellerLogin', component: SellerLogin, meta: { guestOnly: true } },
  { path: '/seller/register', name: 'SellerRegister', component: SellerRegistration, meta: { guestOnly: true } },
  { 
    path: '/seller', 
    component: () => import('@/views/seller/SellerLayout.vue'),
    meta: { requiresAuth: true, requiresSeller: true },
    children: [
      { path: '', redirect: '/seller/dashboard' },
      { path: 'dashboard', name: 'SellerDashboard', component: () => import('@/views/seller/SellerDashboard.vue'), meta: { title: 'Satıcı Paneli' } },
      { path: 'products', name: 'SellerProducts', component: SellerProducts, meta: { title: 'Ürünlerim' } },
      { path: 'orders', name: 'SellerOrders', component: () => import('@/views/seller/SellerOrders.vue'), meta: { title: 'Siparişler' } },
      { path: 'returns', name: 'SellerReturns', component: () => import('@/views/seller/SellerReturns.vue'), meta: { title: 'İadeler' } },
      { path: 'onboarding', name: 'SellerOnboarding', component: SellerOnboarding, meta: { title: 'Mağaza Ayarları' } },
      { path: 'campaigns', name: 'SellerCampaigns', component: SellerCampaign, meta: { title: 'Kampanyalar' } },
      { path: 'financial-report', name: 'SellerFinancialReport', component: SellerFinancialReport, meta: { title: 'Finansal Rapor' } },
    ]
  },
  
  // Campaigns
  { path: '/campaigns', name: 'CampaignList', component: CampaignList },
  { path: '/campaigns/create', name: 'CampaignCreate', component: CampaignCreate },
  { path: '/campaigns/:id', name: 'CampaignDetail', component: CampaignDetail },
  { path: '/campaigns/:id/edit', name: 'CampaignEdit', component: CampaignEdit },
  { path: '/campaigns/:id/delete', name: 'CampaignDelete', component: CampaignDelete },
  { path: '/campaigns/:id/feedback', name: 'CampaignFeedback', component: CampaignFeedback },
  { path: '/campaigns/:id/stats', name: 'CampaignStats', component: CampaignStats },
  { path: '/campaigns/:id/score', name: 'CampaignScore', component: CampaignScore },
  { path: '/campaigns/tabs', name: 'CampaignTabs', component: CampaignTabs },

  // Segments
  { path: '/segments', name: 'SegmentManager', component: SegmentManager },
  { path: '/segments/analytics', name: 'SegmentAnalytics', component: SegmentAnalytics },
  { path: '/segments/heatmap', name: 'SegmentHeatmap', component: SegmentHeatmap },
  { path: '/segments/strategy', name: 'SegmentStrategy', component: SegmentStrategy },
  { path: '/segments/export-scheduler', name: 'SegmentExportScheduler', component: SegmentExportScheduler },
  { path: '/segments/export-history', name: 'SegmentExportHistory', component: SegmentExportHistory },
  { path: '/segments/accuracy', name: 'SegmentAccuracy', component: SegmentAccuracy },
  { path: '/segments/accuracy-breakdown', name: 'SegmentAccuracyBreakdown', component: SegmentAccuracyBreakdown },
  { path: '/segments/success-prediction', name: 'SegmentSuccessPrediction', component: SegmentSuccessPrediction },
  { path: '/segments/success-regression', name: 'SegmentSuccessRegression', component: SegmentSuccessRegression },

  // Marketplace & Storefront
  { path: '/marketplace/c2c', name: 'C2CMarketplace', component: C2CMarketplace },
  { path: '/buyer/dashboard', name: 'BuyerDashboard', component: () => import('@/views/buyer/BuyerDashboardNew.vue') },
  { path: '/unified/dashboard', name: 'UnifiedDashboard', component: UnifiedDashboard },
  { path: '/search', name: 'Search', component: Search },
  { path: '/category/:id', name: 'Category', component: Category },
  { path: '/storefront/product/:id', name: 'ProductPublic', component: ProductPublic },

  // User Profile
  { path: '/user/profile', name: 'UserProfile', component: UserProfile },
  { path: '/user/edit', name: 'UserEdit', component: UserEdit },
  
  // Notifications
  { path: '/notifications', name: 'Notifications', component: () => import('@/views/notifications/NotificationsPage.vue') },
  { path: '/notifications/settings', name: 'NotificationSettings', component: () => import('@/views/notifications/NotificationSettings.vue') },
  
  // Search
  { path: '/search/results', name: 'SearchResults', component: () => import('@/views/search/SearchResults.vue') },
  { path: '/search/advanced', name: 'AdvancedSearch', component: () => import('@/views/search/SearchResults.vue') },
  
  // Messages/Chat
  { path: '/messages', name: 'Messages', component: () => import('@/views/messages/MessagesPage.vue') },
  { path: '/messages/:id', name: 'MessageDetail', component: () => import('@/views/messages/MessagesPage.vue') },
  
  // Marketplace Services
  { path: '/food', name: 'Food', component: FoodHome },
  { path: '/food/restaurants', name: 'FoodRestaurants', component: RestaurantList },
  { path: '/food/category/:id', name: 'FoodCategory', component: FoodCategory },
  { path: '/food/restaurant/:id', name: 'FoodRestaurant', component: RestaurantDetail },
  { path: '/food/group-order', name: 'FoodGroupOrder', component: GroupOrder },
  { path: '/hotels', name: 'Hotels', component: HotelHome },
  { path: '/hotels/all', name: 'HotelsAll', component: HotelList },
  { path: '/hotels/search', name: 'HotelsSearch', component: HotelSearch },
  { path: '/hotels/:id', name: 'HotelDetail', component: HotelDetailPage },
  { path: '/hotels/booking-confirmation', name: 'HotelBookingConfirmation', component: HotelBookingConfirmation },
  { path: '/rides', name: 'Rides', component: RidesHome },
  { path: '/rides/search', name: 'RidesSearch', component: RidesSearch },
  { path: '/services', name: 'Services', component: ServicesHome },
  { path: '/services/all', name: 'ServicesAll', component: ServiceList },
  { path: '/tours', name: 'Tours', component: TourList },
  { path: '/cars', name: 'Cars', component: CarList },
  { path: '/insurance', name: 'Insurance', component: InsuranceList },
  { path: '/activities', name: 'Activities', component: ActivityList },
  { path: '/tours/:id', name: 'TourDetail', component: TourDetail },
  { path: '/cars/:id', name: 'CarDetail', component: CarDetail },
  { path: '/insurance/:id', name: 'InsuranceDetail', component: InsuranceDetail },
  { path: '/activities/:id', name: 'ActivityDetail', component: ActivityDetail },
  { path: '/ai-recommendations', name: 'AIRecommendations', component: AIRecommendations },
  { path: '/turbo', name: 'TurboMod', component: () => import('@/views/turbo/TurboMod.vue') },

  // Static Pages
  { path: '/about', name: 'About', component: AboutPage },
  { path: '/contact', name: 'Contact', component: ContactPage },
  { path: '/faq', name: 'FAQ', component: FAQPage },
  { path: '/seller/guide', name: 'SellerGuide', component: SellerGuidePage },
  
  // Auth
  { path: '/login', name: 'Login', component: Login, meta: { guestOnly: true, title: 'Giriş Yap' } },
  { path: '/register', name: 'Register', component: Register, meta: { guestOnly: true, title: 'Kayıt Ol' } },
  { path: '/forgot-password', name: 'ForgotPassword', component: () => import('@/views/auth/ForgotPassword.vue'), meta: { guestOnly: true, title: 'Şifremi Unuttum' } },
  { path: '/reset-password', name: 'ResetPassword', component: () => import('@/views/auth/ResetPassword.vue'), meta: { guestOnly: true, title: 'Şifre Sıfırla' } },
  
  // Admin
  { 
    path: '/admin', 
    component: () => import('@/views/admin/AdminLayout.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      { path: '', redirect: '/admin/dashboard' },
      { path: 'dashboard', name: 'AdminDashboard', component: AdminDashboard, meta: { title: 'Admin Paneli' } },
      { path: 'orders', name: 'AdminOrders', component: AdminOrders, meta: { title: 'Siparişler' } },
      { path: 'sellers', name: 'AdminSellers', component: AdminSellers, meta: { title: 'Satıcılar' } },
      { path: 'seller-applications', name: 'AdminSellerApplications', component: AdminSellerApplications, meta: { title: 'Satıcı Başvuruları' } },
      { path: 'customers', name: 'AdminCustomers', component: AdminCustomers, meta: { title: 'Müşteriler' } },
      { path: 'finance', name: 'AdminFinance', component: () => import('@/views/admin/FinanceManagement.vue'), meta: { title: 'Finans' } },
      { path: 'commissions', name: 'AdminCommissions', component: () => import('@/views/admin/CommissionManagement.vue'), meta: { title: 'Komisyonlar' } },
      { path: 'products', name: 'AdminProducts', component: () => import('@/views/admin/ProductManagement.vue'), meta: { title: 'Ürünler' } },
      { path: 'categories', name: 'AdminCategories', component: AdminCategories, meta: { title: 'Kategoriler' } },
      { path: 'campaigns', name: 'AdminCampaigns', component: () => import('@/views/admin/CampaignManagement.vue'), meta: { title: 'Kampanyalar' } },
      { path: 'banners', name: 'AdminBanners', component: AdminBanners, meta: { title: 'Bannerlar' } },
      { path: 'reports', name: 'AdminReports', component: AdminReports, meta: { title: 'Raporlar' } },
      { path: 'settings', name: 'AdminSettings', component: AdminSettings, meta: { title: 'Ayarlar' } },
      { path: 'pages', name: 'AdminPages', component: AdminPages, meta: { title: 'Sayfalar' } },
      { path: 'blog', name: 'AdminBlog', component: () => import('@/views/admin/BlogManagement.vue'), meta: { title: 'Blog' } },
      { path: 'themes', name: 'AdminThemes', component: AdminThemes, meta: { title: 'Temalar' } },
      { path: 'notifications', name: 'AdminNotifications', component: AdminNotifications, meta: { title: 'Bildirimler' } },
      { path: 'users', name: 'AdminUsers', component: () => import('@/views/admin/AdminUserManagement.vue'), meta: { title: 'Kullanıcılar' } },
      { path: 'segment-export', name: 'AdminSegmentExport', component: AdminSegmentExport, meta: { title: 'Segment Export' } },
      { path: 'export-files', name: 'AdminExportFiles', component: AdminExportFiles, meta: { title: 'Export Dosyaları' } },
      { path: 'improved', name: 'AdminImproved', component: AdminImproved, meta: { title: 'Gelişmiş Panel' } },
      { path: 'returns', name: 'AdminReturns', component: AdminReturnManagement, meta: { title: 'İadeler' } },
      { path: 'turbo', name: 'AdminTurbo', component: () => import('@/components/admin/TurboWinners.vue'), meta: { title: 'Turbo' } },
      { path: 'transport', name: 'AdminTransport', component: () => import('@/views/admin/TransportManagement.vue'), meta: { title: 'Ulaşım' } },
      { path: 'restaurants', name: 'AdminRestaurants', component: () => import('@/views/admin/RestaurantManagement.vue'), meta: { title: 'Restoranlar' } },
      { path: 'hotels', name: 'AdminHotels', component: () => import('@/views/admin/HotelManagement.vue'), meta: { title: 'Oteller' } },
      { path: 'car-rental', name: 'AdminCarRental', component: () => import('@/views/admin/CarRentalManagement.vue'), meta: { title: 'Araç Kiralama' } },
      { path: 'insurance', name: 'AdminInsurance', component: () => import('@/views/admin/InsuranceManagement.vue'), meta: { title: 'Sigorta' } },
      { path: 'tours', name: 'AdminTours', component: () => import('@/views/admin/TourManagement.vue'), meta: { title: 'Turlar' } },
      { path: 'activities', name: 'AdminActivities', component: () => import('@/views/admin/ActivityManagement.vue'), meta: { title: 'Aktiviteler' } },
      { path: 'marketplace', name: 'MarketplaceDashboard', component: () => import('@/views/admin/MarketplaceDashboard.vue'), meta: { title: 'Marketplace' } },
    ]
  },

  // Utility Pages
  { path: '/payment/success', name: 'PaymentSuccess', component: PaymentSuccess },
  { path: '/payment/failed', name: 'PaymentFailed', component: PaymentFailed },
  { path: '/sprint-cockpit', name: 'SprintCockpit', component: SprintCockpit },
  { path: '/visual-export', name: 'VisualExport', component: VisualExport },
  { path: '/verify', name: 'Verify', component: Verify },
  { path: '/unauthorized', name: 'Unauthorized', component: Unauthorized },
  { path: '/timeout', name: 'Timeout', component: Timeout },
  
  // Fallback
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFoundPage }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guards
router.beforeEach(async (to, from, next) => {
  // Update document title
  document.title = to.meta?.title ? `${to.meta.title} | SportOnline` : 'SportOnline'

  // Check if route requires authentication
  const requiresAuth = to.matched.some(record => record.meta?.requiresAuth)
  const requiresAdmin = to.matched.some(record => record.meta?.requiresAdmin)
  const requiresSeller = to.matched.some(record => record.meta?.requiresSeller)
  const guestOnly = to.matched.some(record => record.meta?.guestOnly)

  // Get auth state from localStorage (avoid circular import)
  const token = localStorage.getItem('token')
  const userStr = localStorage.getItem('user')
  const user = userStr ? JSON.parse(userStr) : null
  const isAuthenticated = !!token

  // Guest-only routes (login, register)
  if (guestOnly && isAuthenticated) {
    return next({ name: 'Home' })
  }

  // Protected routes
  if (requiresAuth && !isAuthenticated) {
    return next({ name: 'Login', query: { redirect: to.fullPath } })
  }

  // Admin-only routes
  if (requiresAdmin && user?.role !== 'admin') {
    return next({ name: 'Unauthorized' })
  }

  // Seller-only routes
  if (requiresSeller && !['seller', 'admin'].includes(user?.role)) {
    return next({ name: 'Unauthorized' })
  }

  next()
})

export default router
