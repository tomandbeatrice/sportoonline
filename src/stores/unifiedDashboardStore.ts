import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useUnifiedDashboardStore = defineStore('unifiedDashboard', () => {
  // State
  const recentActivities = ref([])
  const activeCampaigns = ref([])
  const performanceData = ref({})
  const topSellers = ref([])
  const pendingOrders = ref([])
  const loading = ref(false)

  // Quick Stats
  const quickStats = ref([
    {
      id: 'revenue',
      title: 'Günlük Gelir',
      value: '₺45,280',
      change: '+12.5%',
      trend: 'up',
      icon: 'money',
      color: 'green'
    },
    {
      id: 'orders',
      title: 'Aktif Siparişler',
      value: '128',
      change: '+8',
      trend: 'up',
      icon: 'box',
      color: 'blue'
    },
    {
      id: 'campaigns',
      title: 'Kampanyalar',
      value: '12',
      change: '3 aktif',
      trend: 'neutral',
      icon: 'target',
      color: 'purple'
    },
    {
      id: 'sellers',
      title: 'Yeni Satıcılar',
      value: '7',
      change: '+2',
      trend: 'up',
      icon: 'store',
      color: 'orange'
    }
  ])

  // Modules
  const modules = ref([
    // Sales & Orders
    { id: 'orders', name: 'Sipariş Yönetimi', icon: 'box', category: 'sales', route: '/admin/orders', status: 'active' },
    { id: 'order-tracking', name: 'Sipariş Takip', icon: 'truck', category: 'sales', route: '/orders/:id', status: 'active' },
    { id: 'cart', name: 'Sepet Analizi', icon: 'cart', category: 'sales', route: '/admin/carts', status: 'active' },
    
    // Inventory
    { id: 'products', name: 'Ürün Yönetimi', icon: 'box', category: 'inventory', route: '/admin/products', status: 'active' },
    { id: 'categories', name: 'Kategori Yönetimi', icon: 'folder', category: 'inventory', route: '/admin/categories', status: 'active' },
    { id: 'inventory', name: 'Stok Takibi', icon: 'chart', category: 'inventory', route: '/admin/inventory', status: 'active' },
    
    // Marketing
    { id: 'campaigns', name: 'Kampanya Yönetimi', icon: 'target', category: 'marketing', route: '/admin/campaigns', status: 'active' },
    { id: 'banners', name: 'Banner Yönetimi', icon: 'image', category: 'marketing', route: '/admin/banners', status: 'active' },
    { id: 'promotions', name: 'Promosyonlar', icon: 'gift', category: 'marketing', route: '/admin/promotions', status: 'active' },
    
    // Analytics
    { id: 'analytics', name: 'Analitik Dashboard', icon: 'chart', category: 'analytics', route: '/admin/analytics', status: 'active' },
    { id: 'reports', name: 'Raporlar', icon: 'chart-up', category: 'analytics', route: '/admin/reports', status: 'active' },
    { id: 'exports', name: 'Veri Export', icon: 'download', category: 'analytics', route: '/admin/exports', status: 'active' },
    
    // Local & Discovery
    { id: 'nearby', name: 'Yakınımdaki İşletmeler', icon: 'map-pin', category: 'local', route: '/nearby', status: 'active' },

    // Admin
    { id: 'sellers', name: 'Satıcı Yönetimi', icon: 'store', category: 'admin', route: '/admin/seller-applications', status: 'active' },
    { id: 'users', name: 'Kullanıcı Yönetimi', icon: 'users', category: 'admin', route: '/admin/users', status: 'active' },
    { id: 'settings', name: 'Sistem Ayarları', icon: 'settings', category: 'admin', route: '/admin/settings', status: 'active' }
  ])

  // Workflows
  const workflows = ref([
    {
      id: 'new-product-campaign',
      name: 'Yeni Ürün + Kampanya',
      description: 'Ürün ekle, kampanya oluştur, satıcıya bildir',
      steps: ['Ürün Ekle', 'Kampanya Oluştur', 'Satıcıya Bildir'],
      icon: 'rocket',
      color: 'blue'
    },
    {
      id: 'seller-approval',
      name: 'Satıcı Onay Süreci',
      description: 'Başvuru değerlendir, onay ver, bildir',
      steps: ['Başvuru İncele', 'Onay Ver', 'Email Gönder'],
      icon: 'check',
      color: 'green'
    },
    {
      id: 'order-fulfillment',
      name: 'Sipariş İşleme',
      description: 'Sipariş onayla, kargo hazırla, müşteriyi bildir',
      steps: ['Sipariş Onayla', 'Kargo Hazırla', 'Müşteri Bildirimi'],
      icon: 'box',
      color: 'purple'
    },
    {
      id: 'flash-sale',
      name: 'Flaş Kampanya',
      description: 'Kampanya oluştur, ürünleri ekle, yayınla',
      steps: ['Kampanya Oluştur', 'Ürün Seç', 'Yayınla'],
      icon: 'bolt',
      color: 'orange'
    },
    {
      id: 'inventory-alert',
      name: 'Stok Uyarısı',
      description: 'Düşük stok tespiti, satıcıya bildir, sipariş öner',
      steps: ['Stok Kontrol', 'Satıcıya Bildir', 'Otomatik Sipariş'],
      icon: 'bell',
      color: 'red'
    },
    {
      id: 'customer-segment',
      name: 'Müşteri Segmentasyonu',
      description: 'Segment oluştur, kampanya ata, email gönder',
      steps: ['Segment Oluştur', 'Kampanya Ata', 'Email Kampanyası'],
      icon: '🎯',
      color: 'indigo'
    }
  ])

  // Actions
  async function loadDashboardData() {
    loading.value = true
    try {
      // Load all dashboard data in parallel
      const [activitiesRes, campaignsRes, performanceRes, sellersRes, ordersRes] = await Promise.all([
        axios.get('/api/admin/recent-activities'),
        axios.get('/api/admin/active-campaigns'),
        axios.get('/api/admin/performance'),
        axios.get('/api/admin/top-sellers'),
        axios.get('/api/admin/pending-orders')
      ])

      recentActivities.value = activitiesRes.data
      activeCampaigns.value = campaignsRes.data
      performanceData.value = performanceRes.data
      topSellers.value = sellersRes.data
      pendingOrders.value = ordersRes.data
    } catch (error) {
      console.error('Dashboard data loading failed:', error)
    } finally {
      loading.value = false
    }
  }

  return {
    recentActivities,
    activeCampaigns,
    performanceData,
    topSellers,
    pendingOrders,
    loading,
    quickStats,
    modules,
    workflows,
    loadDashboardData
  }
})
