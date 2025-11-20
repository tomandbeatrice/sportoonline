<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Top Header -->
    <header class="bg-white shadow-sm border-b sticky top-0 z-50">
      <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">SportoOnline Merkezi Kontrol Paneli</h1>
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
              {{ userRole }}
            </span>
          </div>
          <div class="flex items-center gap-4">
            <button @click="showNotifications = !showNotifications" class="relative p-2 hover:bg-gray-100 rounded-lg">
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span v-if="unreadNotifications > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                {{ unreadNotifications }}
              </span>
            </button>
            <div class="flex items-center gap-2">
              <img :src="userAvatar" class="w-10 h-10 rounded-full" />
              <div>
                <p class="text-sm font-semibold">{{ userName }}</p>
                <p class="text-xs text-gray-500">{{ userEmail }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="container mx-auto px-6 py-8">
      <!-- Quick Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatCard
          v-for="stat in quickStats"
          :key="stat.id"
          :title="stat.title"
          :value="stat.value"
          :change="stat.change"
          :trend="stat.trend"
          :icon="stat.icon"
          :color="stat.color"
          @click="handleStatClick(stat.id)"
        />
      </div>

      <!-- Main Control Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Module Hub -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Modül Merkezi</h2>
            <select v-model="selectedCategory" class="px-4 py-2 border rounded-lg text-sm">
              <option value="all">Tüm Modüller</option>
              <option value="sales">Satış & Sipariş</option>
              <option value="inventory">Ürün & Stok</option>
              <option value="marketing">Pazarlama</option>
              <option value="analytics">Analitik</option>
              <option value="admin">Yönetim</option>
            </select>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <ModuleCard
              v-for="module in filteredModules"
              :key="module.id"
              :module="module"
              @click="openModule(module)"
            />
          </div>
        </div>

        <!-- Activity Feed -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Son Aktiviteler</h2>
          <div class="space-y-3 max-h-[600px] overflow-y-auto">
            <ActivityItem
              v-for="activity in recentActivities"
              :key="activity.id"
              :activity="activity"
            />
          </div>
        </div>
      </div>

      <!-- Integrated Workflows -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Hızlı İş Akışları</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <WorkflowCard
            v-for="workflow in workflows"
            :key="workflow.id"
            :workflow="workflow"
            @execute="executeWorkflow(workflow)"
          />
        </div>
      </div>

      <!-- Campaign Performance Dashboard -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Aktif Kampanyalar</h2>
          <CampaignList :campaigns="activeCampaigns" @manage="manageCampaign" />
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Performans Özeti</h2>
          <PerformanceChart :data="performanceData" />
        </div>
      </div>

      <!-- Seller & Order Management Integration -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Satıcı Yönetimi</h2>
          <SellerOverview :sellers="topSellers" @viewAll="openModule({ id: 'sellers' })" />
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Bekleyen Siparişler</h2>
          <OrderQueue :orders="pendingOrders" @process="processOrder" />
        </div>
      </div>
    </div>

    <!-- Floating Action Button -->
    <button 
      @click="showQuickActions = !showQuickActions"
      class="fixed bottom-8 right-8 w-16 h-16 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-2xl flex items-center justify-center transition-transform hover:scale-110"
    >
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
    </button>

    <!-- Quick Actions Menu -->
    <transition name="slide-up">
      <div v-if="showQuickActions" class="fixed bottom-28 right-8 bg-white rounded-xl shadow-2xl p-4 w-64">
        <h3 class="font-bold text-gray-900 mb-3">Hızlı İşlemler</h3>
        <div class="space-y-2">
          <button
            v-for="action in quickActions"
            :key="action.id"
            @click="executeQuickAction(action)"
            class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-100 flex items-center gap-3"
          >
            <span>{{ action.icon }}</span>
            <span class="font-medium">{{ action.label }}</span>
          </button>
        </div>
      </div>
    </transition>

    <!-- Modals -->
    <ModuleModal
      v-if="activeModule"
      :module="activeModule"
      @close="activeModule = null"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import StatCard from '@/components/unified/StatCard.vue'
import ModuleCard from '@/components/unified/ModuleCard.vue'
import ActivityItem from '@/components/unified/ActivityItem.vue'
import WorkflowCard from '@/components/unified/WorkflowCard.vue'
import CampaignList from '@/components/unified/CampaignList.vue'
import PerformanceChart from '@/components/unified/PerformanceChart.vue'
import SellerOverview from '@/components/unified/SellerOverview.vue'
import OrderQueue from '@/components/unified/OrderQueue.vue'
import ModuleModal from '@/components/unified/ModuleModal.vue'

const router = useRouter()

// User Info
const user = JSON.parse(localStorage.getItem('user') || '{}')
const userName = ref(user.name || 'Admin User')
const userEmail = ref(user.email || 'admin@sportoonline.com')
const userRole = ref(user.role || 'admin')
const userAvatar = ref(user.avatar || 'https://ui-avatars.com/api/?name=' + userName.value)

// State
const showNotifications = ref(false)
const showQuickActions = ref(false)
const selectedCategory = ref('all')
const activeModule = ref(null)
const unreadNotifications = ref(5)

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
  
  // Admin
  { id: 'sellers', name: 'Satıcı Yönetimi', icon: 'store', category: 'admin', route: '/admin/seller-applications', status: 'active' },
  { id: 'users', name: 'Kullanıcı Yönetimi', icon: 'users', category: 'admin', route: '/admin/users', status: 'active' },
  { id: 'settings', name: 'Sistem Ayarları', icon: 'settings', category: 'admin', route: '/admin/settings', status: 'active' }
])

const filteredModules = computed(() => {
  if (selectedCategory.value === 'all') return modules.value
  return modules.value.filter(m => m.category === selectedCategory.value)
})

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

// Activities
const recentActivities = ref([])
const activeCampaigns = ref([])
const performanceData = ref({})
const topSellers = ref([])
const pendingOrders = ref([])

// Quick Actions
const quickActions = ref([
  { id: 'new-product', label: 'Yeni Ürün Ekle', icon: 'plus' },
  { id: 'new-campaign', label: 'Kampanya Oluştur', icon: 'target' },
  { id: 'approve-seller', label: 'Satıcı Onayla', icon: 'check' },
  { id: 'process-order', label: 'Sipariş İşle', icon: 'box' },
  { id: 'view-analytics', label: 'Analitikleri Gör', icon: 'chart' },
  { id: 'export-data', label: 'Veri Export', icon: 'download' }
])

onMounted(() => {
  loadDashboardData()
})

async function loadDashboardData() {
  try {
    // Load all dashboard data in parallel
    const [activities, campaigns, performance, sellers, orders] = await Promise.all([
      axios.get('/api/admin/recent-activities'),
      axios.get('/api/admin/active-campaigns'),
      axios.get('/api/admin/performance'),
      axios.get('/api/admin/top-sellers'),
      axios.get('/api/admin/pending-orders')
    ])

    recentActivities.value = activities.data
    activeCampaigns.value = campaigns.data
    performanceData.value = performance.data
    topSellers.value = sellers.data
    pendingOrders.value = orders.data
  } catch (error) {
    console.error('Dashboard data loading failed:', error)
  }
}

function handleStatClick(statId: string) {
  const routes: Record<string, string> = {
    revenue: '/admin/analytics/revenue',
    orders: '/admin/orders',
    campaigns: '/admin/campaigns',
    sellers: '/admin/seller-applications'
  }
  
  if (routes[statId]) {
    router.push(routes[statId])
  }
}

function openModule(module: any) {
  if (module.route) {
    router.push(module.route)
  } else {
    activeModule.value = module
  }
}

function executeWorkflow(workflow: any) {
  router.push({
    name: 'WorkflowExecutor',
    params: { workflowId: workflow.id }
  })
}

function executeQuickAction(action: any) {
  showQuickActions.value = false
  
  const actionRoutes: Record<string, string> = {
    'new-product': '/admin/products/create',
    'new-campaign': '/admin/campaigns/create',
    'approve-seller': '/admin/seller-applications?filter=pending',
    'process-order': '/admin/orders?status=pending',
    'view-analytics': '/admin/analytics',
    'export-data': '/admin/exports'
  }

  if (actionRoutes[action.id]) {
    router.push(actionRoutes[action.id])
  }
}

function manageCampaign(campaign: any) {
  router.push(`/admin/campaigns/${campaign.id}`)
}

function processOrder(order: any) {
  router.push(`/admin/orders/${order.id}`)
}
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
