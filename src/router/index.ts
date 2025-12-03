import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

// Marketplace
const MarketplaceHome = () => import('@/components/marketplace/MarketplaceHome.vue')
const CareerOpportunities = () => import('@/components/marketplace/CareerOpportunities.vue')
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
const SellerDashboard = () => import('@/views/seller/SellerOnboarding.vue') // Using onboarding as dashboard
const SellerLogin = () => import('@/views/seller/SellerLogin.vue')
const SellerRegistration = () => import('@/views/seller/SellerRegistration.vue')
const SellerOnboarding = () => import('@/views/seller/SellerOnboarding.vue')
const SellerCampaign = () => import('@/views/seller/SellerCampaign.vue')
const SellerFinancialReport = () => import('@/views/seller/FinancialReport.vue')

// Marketplace & Storefront
const C2CMarketplace = () => import('@/views/marketplace/C2CMarketplaceDashboard.vue')
const BuyerDashboard = () => import('@/views/buyer/BuyerDashboard.vue')
const UnifiedDashboard = () => import('@/views/unified/UnifiedDashboard.vue')
const Search = () => import('@/views/storefront/Search.vue')
const Category = () => import('@/views/storefront/Category.vue')
const ProductPublic = () => import('@/views/storefront/ProductPublic.vue')

// User
const UserProfile = () => import('@/views/user/UserProfile.vue')
const UserEdit = () => import('@/views/user/UserEdit.vue')

// Hotel
const HotelBookingConfirmation = () => import('@/views/hotel/HotelBookingConfirmation.vue')

// ServiceHub Pages
const FoodHome = () => import('@/views/services/FoodHome.vue')
const HotelHome = () => import('@/views/services/HotelHome.vue')
const RidesHome = () => import('@/views/services/RidesHome.vue')
const ServicesHome = () => import('@/views/services/ServicesHome.vue')

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
  { path: '/seller/login', name: 'SellerLogin', component: SellerLogin },
  { path: '/seller/register', name: 'SellerRegister', component: SellerRegistration },
  { 
    path: '/seller', 
    children: [
      { path: '', redirect: '/seller/dashboard' },
      { path: 'dashboard', name: 'SellerDashboard', component: SellerDashboard },
      { path: 'products', name: 'SellerProducts', component: SellerProducts },
      { path: 'onboarding', name: 'SellerOnboarding', component: SellerOnboarding },
      { path: 'campaigns', name: 'SellerCampaigns', component: SellerCampaign },
      { path: 'financial-report', name: 'SellerFinancialReport', component: SellerFinancialReport },
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
  { path: '/buyer/dashboard', name: 'BuyerDashboard', component: BuyerDashboard },
  { path: '/unified/dashboard', name: 'UnifiedDashboard', component: UnifiedDashboard },
  { path: '/search', name: 'Search', component: Search },
  { path: '/category/:id', name: 'Category', component: Category },
  { path: '/storefront/product/:id', name: 'ProductPublic', component: ProductPublic },

  // User Profile
  { path: '/user/profile', name: 'UserProfile', component: UserProfile },
  { path: '/user/edit', name: 'UserEdit', component: UserEdit },
  
  // Marketplace Services
  { path: '/food', name: 'Food', component: FoodHome },
  { path: '/food/restaurants', name: 'FoodRestaurants', component: ComingSoon },
  { path: '/food/category/:id', name: 'FoodCategory', component: ComingSoon },
  { path: '/food/restaurant/:id', name: 'FoodRestaurant', component: ComingSoon },
  { path: '/food/group-order', name: 'FoodGroupOrder', component: ComingSoon },
  { path: '/hotels', name: 'Hotels', component: HotelHome },
  { path: '/hotels/all', name: 'HotelsAll', component: ComingSoon },
  { path: '/hotels/search', name: 'HotelsSearch', component: ComingSoon },
  { path: '/hotels/:id', name: 'HotelDetail', component: ComingSoon },
  { path: '/hotels/booking-confirmation', name: 'HotelBookingConfirmation', component: HotelBookingConfirmation },
  { path: '/rides', name: 'Rides', component: RidesHome },
  { path: '/rides/search', name: 'RidesSearch', component: ComingSoon },
  { path: '/services', name: 'Services', component: ServicesHome },
  { path: '/services/all', name: 'ServicesAll', component: ComingSoon },
  { path: '/career', name: 'Career', component: CareerOpportunities },
  
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
      { path: 'seller-applications', name: 'AdminSellerApplications', component: AdminSellerApplications },
      { path: 'customers', name: 'AdminCustomers', component: AdminCustomers },
      { path: 'categories', name: 'AdminCategories', component: AdminCategories },
      { path: 'banners', name: 'AdminBanners', component: AdminBanners },
      { path: 'reports', name: 'AdminReports', component: AdminReports },
      { path: 'settings', name: 'AdminSettings', component: AdminSettings },
      { path: 'pages', name: 'AdminPages', component: AdminPages },
      { path: 'themes', name: 'AdminThemes', component: AdminThemes },
      { path: 'notifications', name: 'AdminNotifications', component: AdminNotifications },
      { path: 'segment-export', name: 'AdminSegmentExport', component: AdminSegmentExport },
      { path: 'export-files', name: 'AdminExportFiles', component: AdminExportFiles },
      { path: 'improved', name: 'AdminImproved', component: AdminImproved },
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

export default router
