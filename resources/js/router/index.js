import { createRouter, createWebHistory } from 'vue-router'

// Sayfa bileşenleri
import Cockpit from '../views/Cockpit.vue'
import ProductList from '../views/products/ProductList.vue'
import ProductDetail from '../views/products/ProductDetail.vue'
import Cart from '../views/Cart.vue'
import Checkout from '../views/Checkout.vue'
import OrderStatus from '../views/OrderStatus.vue'
import PaymentResult from '../views/PaymentResult.vue'
import Orders from '../views/Orders.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'

// Admin Components
import AdminLayout from '../views/admin/AdminLayout.vue'
import AdminDashboard from '../views/admin/AdminDashboard.vue' // Assuming this is the correct path
import SellerApplications from '../views/admin/SellerApplications.vue'
import AdminIndex from '../views/admin/AdminIndex.vue'
import AdminFinancialReport from '../views/admin/AdminFinancialReport.vue'
import CampaignAI from '../views/admin/CampaignAI.vue'

// Seller Components
import SellerDashboard from '../views/seller/SellerDashboard.vue'

const routes = [
  { path: '/', component: Cockpit },
  { path: '/products', component: ProductList },
  { path: '/products/:id', component: ProductDetail },
  { path: '/cart', component: Cart },
  { path: '/checkout', component: Checkout },
  { path: '/order-status', component: OrderStatus },
  { path: '/orders', component: Orders, meta: { requiresAuth: true } },
  { path: '/payment/success', component: PaymentResult },
  { path: '/payment/failure', component: PaymentResult },
  { path: '/login', component: Login },
  { path: '/register', component: Register },

  // Admin Routes
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'admin' }, // Protect all admin routes
    children: [
      {
        path: '',
        name: 'AdminIndex',
        component: AdminIndex,
      },
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: AdminDashboard,
      },
      {
        path: 'seller-applications',
        name: 'AdminSellerApplications',
        component: SellerApplications,
      },
      {
        path: 'financial-report',
        name: 'AdminFinancialReport',
        component: AdminFinancialReport,
      },
      {
        path: 'campaign-ai',
        name: 'CampaignAI',
        component: CampaignAI,
      },
      // Add more admin routes here
    ],
  },

  // Seller Routes
  {
    path: '/seller',
    children: [
      {
        path: 'dashboard',
        name: 'SellerDashboard',
        component: SellerDashboard,
        meta: { requiresAuth: true, role: 'seller' },
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router