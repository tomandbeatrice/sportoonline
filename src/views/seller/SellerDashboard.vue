<template>
  <div class="seller-dashboard min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50 transform transition-transform duration-300"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
      <div class="flex items-center justify-between p-6 border-b">
        <h1 class="text-xl font-bold text-gray-900">Satıcı Paneli</h1>
        <button @click="sidebarOpen = false" class="md:hidden text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav class="p-4 space-y-2">
              <button
                v-for="item in menuItems"
                :key="item.name"
                @click="currentView = item.component"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
                :class="currentView === item.component 
            ? 'bg-blue-500 text-white' 
            : 'text-gray-700 hover:bg-gray-100'"
        >
          <component :is="IconMap[item.icon]" class="w-6 h-6" aria-hidden="true" />
          <span class="font-medium">{{ item.name }}</span>
        </button>
      </nav>

      <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-gray-50">
        <div class="text-sm text-gray-600 mb-2">{{ user?.name }}</div>
        <button
          @click="logout"
          class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium"
        >
          Çıkış Yap
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="md:ml-64">
      <!-- Top Bar -->
      <div class="bg-white shadow-sm sticky top-0 z-40">
        <div class="flex items-center justify-between px-6 py-4">
          <button
            @click="sidebarOpen = !sidebarOpen"
            class="md:hidden text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <h2 class="text-xl font-semibold text-gray-900">{{ currentPageTitle }}</h2>
          <div class="flex items-center gap-4">
            <div class="text-sm text-gray-600">
              {{ new Date().toLocaleDateString('tr-TR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="p-0">
        <!-- Dashboard Overview -->
        <div v-if="currentView === 'overview'" class="p-6">
          <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard Özet</h1>

          <!-- Stats Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="text-sm text-gray-600 mb-1">Toplam Ürün</div>
                      <div class="text-3xl font-bold text-blue-600">{{ stats.total_products || 0 }}</div>
                    </div>
                    <IconCart cls="w-10 h-10 text-slate-400" />
                  </div>
                </div>

            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-sm text-gray-600 mb-1">Bekleyen Sipariş</div>
                  <div class="text-3xl font-bold text-yellow-600">{{ stats.pending_orders || 0 }}</div>
                </div>
                <IconClock cls="w-10 h-10 text-slate-400" />
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-sm text-gray-600 mb-1">Toplam Gelir</div>
                  <div class="text-3xl font-bold text-green-600">₺{{ formatMoney(stats.total_revenue || 0) }}</div>
                </div>
                <IconMoney cls="w-10 h-10 text-slate-400" />
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-sm text-gray-600 mb-1">Ortalama Puan</div>
                  <div class="text-3xl font-bold text-purple-600">{{ stats.avg_rating?.toFixed(1) || 'N/A' }}</div>
                </div>
                <IconStar cls="w-10 h-10 text-slate-400" />
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Hızlı İşlemler</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <button
                @click="currentView = 'products'"
                class="p-4 border-2 border-blue-200 rounded-lg hover:bg-blue-50 transition-colors"
              >
                <div class="text-3xl mb-2">➕</div>
                <div class="font-semibold text-gray-900">Yeni Ürün</div>
              </button>
              <button
                @click="currentView = 'orders'"
                class="p-4 border-2 border-green-200 rounded-lg hover:bg-green-50 transition-colors"
              >
                <div class="text-3xl mb-2">📋</div>
                <div class="font-semibold text-gray-900">Siparişler</div>
              </button>
              <button
                @click="currentView = 'stock'"
                class="p-4 border-2 border-orange-200 rounded-lg hover:bg-orange-50 transition-colors flex flex-col items-center"
              >
                <BadgeIcon name="chart-bar" cls="w-8 h-8 text-orange-600 mb-2" />
                <div class="font-semibold text-gray-900">Stok Takip</div>
              </button>
              <button
                @click="currentView = 'campaigns'"
                class="p-4 border-2 border-purple-200 rounded-lg hover:bg-purple-50 transition-colors flex flex-col items-center"
              >
                <BadgeIcon name="target" cls="w-8 h-8 text-purple-600 mb-2" />
                <div class="font-semibold text-gray-900">Kampanya</div>
              </button>
            </div>
          </div>

          <!-- Recent Orders -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-bold text-gray-900">Son Siparişler</h2>
              <button
                @click="currentView = 'orders'"
                class="text-blue-600 hover:text-blue-800 font-medium"
              >
                Tümünü Gör →
              </button>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="order in recentOrders.slice(0, 5)" :key="order.id" class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-semibold">#{{ order.id }}</td>
                    <td class="px-4 py-3 text-sm">{{ order.name || order.user?.name }}</td>
                    <td class="px-4 py-3 text-sm font-semibold">₺{{ formatMoney(order.total) }}</td>
                    <td class="px-4 py-3">
                      <span
                        class="px-2 py-1 text-xs font-semibold rounded-full"
                        :class="getStatusClass(order.status)"
                      >
                        {{ getStatusLabel(order.status) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="recentOrders.length === 0" class="text-center py-8 text-gray-500">
              Henüz sipariş yok
            </div>
          </div>

          <!-- Low Stock Alerts -->
          <div class="bg-white rounded-lg shadow-md p-6" v-if="lowStockProducts.length > 0">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <BadgeIcon name="alert-triangle" cls="w-6 h-6 text-orange-500" /> Düşük Stok Uyarısı
              </h2>
              <button
                @click="currentView = 'stock'"
                class="text-orange-600 hover:text-orange-800 font-medium"
              >
                Stok Yönetimi →
              </button>
            </div>
            <div class="space-y-3">
              <div
                v-for="product in lowStockProducts.slice(0, 5)"
                :key="product.id"
                class="flex items-center justify-between p-3 bg-orange-50 border border-orange-200 rounded-lg"
              >
                <div class="flex items-center gap-3">
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="w-12 h-12 rounded object-cover"
                  />
                  <div>
                    <div class="font-semibold text-gray-900">{{ product.name }}</div>
                    <div class="text-sm text-gray-600">Stok: {{ product.stock }}</div>
                  </div>
                </div>
                <button
                  @click="currentView = 'stock'"
                  class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-semibold"
                >
                  Güncelle
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Component Views -->
        <ProductManagement v-if="currentView === 'products'" />
        <StockManagement v-if="currentView === 'stock'" />
        <OrderManagement v-if="currentView === 'orders'" />
        <CampaignManagement v-if="currentView === 'campaigns'" />
        <SellerSettings v-if="currentView === 'settings'" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import ProductManagement from '@/components/seller/ProductManagement.vue'
import StockManagement from '@/components/seller/StockManagement.vue'
import OrderManagement from '@/components/seller/OrderManagement.vue'
import CampaignManagement from '@/components/seller/CampaignManagement.vue'
import SellerSettings from '@/components/seller/SellerSettings.vue'
import IconFlag from '@/components/icons/IconFlag.vue'
import IconCart from '@/components/icons/IconCart.vue'
import IconTarget from '@/components/icons/IconTarget.vue'
import IconClipboard from '@/components/icons/IconClipboard.vue'
import IconRocket from '@/components/icons/IconRocket.vue'
import IconClock from '@/components/icons/IconClock.vue'
import IconMoney from '@/components/icons/IconMoney.vue'
import IconStar from '@/components/icons/IconStar.vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const router = useRouter()
const sidebarOpen = ref(false)
const currentView = ref('overview')
const user = ref(null)

const stats = ref({
  total_products: 0,
  pending_orders: 0,
  total_revenue: 0,
  avg_rating: 0
})

const recentOrders = ref([])
const lowStockProducts = ref([])

const menuItems = [
  { name: 'Dashboard', icon: 'home', component: 'overview' },
  { name: 'Ürünler', icon: 'products', component: 'products' },
  { name: 'Stok Yönetimi', icon: 'stock', component: 'stock' },
  { name: 'Siparişler', icon: 'orders', component: 'orders' },
  { name: 'Kampanyalar', icon: 'campaigns', component: 'campaigns' },
  { name: 'Ayarlar', icon: 'settings', component: 'settings' },
]

const IconMap: Record<string, any> = {
  home: IconFlag,
  products: IconCart,
  stock: IconTarget,
  orders: IconClipboard,
  campaigns: IconTarget,
  settings: IconRocket,
}

const currentPageTitle = computed(() => {
  const item = menuItems.find(m => m.component === currentView.value)
  return item ? item.name : 'Dashboard'
})

const loadDashboardData = async () => {
  try {
    // Load stats
    const { data: statsData } = await axios.get('/api/seller/stats')
    stats.value = {
      total_products: statsData.total_products || 0,
      pending_orders: 0,
      total_revenue: statsData.total_revenue || 0,
      avg_rating: statsData.avg_rating || 0
    }

    // Load recent orders
    const { data: ordersData } = await axios.get('/api/seller/orders')
    recentOrders.value = ordersData
    stats.value.pending_orders = ordersData.filter(o => o.status === 'pending').length

    // Load products for low stock check
    const { data: productsData } = await axios.get('/api/seller/products')
    lowStockProducts.value = productsData.filter(p => {
      const threshold = p.low_stock_threshold || 10
      return p.stock > 0 && p.stock <= threshold
    })
  } catch (error) {
    console.error('Failed to load dashboard data:', error)
  }
}

const loadUser = async () => {
  try {
    const { data } = await axios.get('/api/user')
    user.value = data
  } catch (error) {
    console.error('Failed to load user:', error)
  }
}

const logout = async () => {
  try {
    await axios.post('/api/logout')
    localStorage.removeItem('token')
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Bekliyor',
    processing: 'Hazırlanıyor',
    shipped: 'Kargoda',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal'
  }
  return labels[status] || status
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount || 0)
}

onMounted(() => {
  loadUser()
  loadDashboardData()
})
</script>

<style scoped>
/* Responsive sidebar overlay for mobile */
@media (max-width: 768px) {
  .sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 40;
  }
}
</style>
