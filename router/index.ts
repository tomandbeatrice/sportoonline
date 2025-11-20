import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

// Lazy loading ile sayfa bileşenleri
const ExportLogAnaliz = () => import('@/pages/analiz/ExportLogAnaliz.vue')
const AdminPanel = () => import('@/views/admin/AdminPanel.vue')
const Home = () => import('@/pages/Home.vue')
const StrategyCockpit = () => import('@/views/StrategyCockpitView.vue')
const SellerDashboard = () => import('@/views/seller/SellerDashboard.vue')
const AccessDenied = () => import('@/views/AccessDenied.vue')
const SegmentExport = () => import('@/views/admin/SegmentExport.vue')

const routes: RouteRecordRaw[] = [
  { path: '/', redirect: '/home' },
  {
    path: '/home',
    name: 'Home',
    component: Home
  },
  {
    path: '/analiz/export',
    name: 'ExportLogAnaliz',
    component: ExportLogAnaliz,
    meta: { requiresAuth: true, roles: ['admin'] }
  },
  {
    path: '/admin/panel',
    name: 'AdminPanel',
    component: AdminPanel,
    meta: { requiresAuth: true, roles: ['admin'] }
  },
  {
    path: '/admin/strategy-cockpit',
    name: 'StrategyCockpit',
    component: StrategyCockpit,
    meta: { requiresAuth: true, roles: ['admin'] }
  },
  {
    path: '/admin/segment-export',
    name: 'SegmentExport',
    component: SegmentExport,
    meta: { requiresAuth: true, roles: ['admin'] }
  },
  {
    path: '/seller/dashboard',
    name: 'SellerDashboard',
    component: SellerDashboard,
    meta: { requiresAuth: true, roles: ['seller'] }
  },
  {
    path: '/access-denied',
    name: 'AccessDenied',
    component: AccessDenied
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Role Guard: Yetkisiz erişimi engelle
router.beforeEach((to, from, next) => {
  const userRole = localStorage.getItem('role')
  const allowedRoles = Array.isArray(to.meta.roles)
    ? to.meta.roles
    : to.meta.role
    ? [to.meta.role]
    : []

  if (to.meta.requiresAuth && allowedRoles.length > 0) {
    if (!userRole || !allowedRoles.includes(userRole)) {
      return next({ name: 'AccessDenied' })
    }
  }

  next()
})

export default router