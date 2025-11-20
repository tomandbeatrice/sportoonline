<template>
  <div class="c2c-marketplace-dashboard min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Top Header -->
    <header class="bg-white shadow-sm border-b sticky top-0 z-50">
      <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">
              {{ dashboardTitle }}
            </h1>
            <span :class="roleBadgeClass" class="px-3 py-1 rounded-full text-sm font-semibold">
              {{ roleLabel }}
            </span>
          </div>
          
          <!-- Role Switcher (For demo/admin) -->
          <div v-if="canSwitchRoles" class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Rol:</label>
            <select v-model="currentRole" class="px-3 py-1 border rounded-lg text-sm">
              <option value="seller">Satıcı</option>
              <option value="buyer">Alıcı</option>
              <option value="admin">Platform Admin</option>
            </select>
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
            
            <!-- User Menu -->
            <div class="flex items-center gap-3">
              <img :src="userAvatar" class="w-10 h-10 rounded-full" alt="User Avatar" />
              <div>
                <p class="text-sm font-semibold">{{ userName }}</p>
                <p class="text-xs text-gray-500">{{ userEmail }}</p>
              </div>
              
              <!-- Logout Button -->
              <button 
                @click="handleLogout"
                class="ml-2 rounded-lg bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100 transition-colors"
                title="Çıkış Yap"
              >
                Çıkış
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="container mx-auto px-6 py-8">
      <!-- Quick Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div
          v-for="stat in stats"
          :key="stat.label"
          class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all cursor-pointer"
          @click="handleStatClick(stat)"
        >
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm text-gray-500 mb-1">{{ stat.label }}</p>
              <h3 class="text-2xl font-bold text-gray-900">{{ stat.value }}</h3>
              <p :class="getTrendClass(stat.trend)" class="text-sm mt-2 flex items-center gap-1">
                <span>{{ getTrendIcon(stat.trend) }}</span>
                {{ stat.change }}
              </p>
            </div>
            <span class="text-3xl">{{ stat.icon }}</span>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Module Hub -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">{{ moduleHubTitle }}</h2>
            <select v-model="selectedCategory" class="px-4 py-2 border rounded-lg text-sm">
              <option value="all">Tümü</option>
              <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                {{ cat.label }}
              </option>
            </select>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div
              v-for="module in filteredModules"
              :key="module.id"
              :class="getModuleCardClass(module.color)"
              class="p-4 rounded-lg cursor-pointer hover:shadow-lg transition-all"
              @click="openModule(module)"
            >
              <div class="text-3xl mb-2">{{ module.icon }}</div>
              <h3 class="font-semibold text-sm mb-1">{{ module.name }}</h3>
              <p v-if="module.badge" class="text-xs opacity-70">{{ module.badge }}</p>
            </div>
          </div>
        </div>

        <!-- Activity Feed -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Son Aktiviteler</h2>
          <div class="space-y-3 max-h-[600px] overflow-y-auto">
            <div
              v-for="activity in recentActivities"
              :key="activity.id"
              class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition-all"
            >
              <div class="flex items-start gap-3">
                <span class="text-2xl">{{ activity.icon }}</span>
                <div class="flex-1">
                  <p class="text-sm font-semibold text-gray-900">{{ activity.title }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ activity.description }}</p>
                  <p class="text-xs text-gray-400 mt-1">{{ activity.time }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Integrated Workflows -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Hızlı İş Akışları</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="workflow in workflows"
            :key="workflow.id"
            class="border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 cursor-pointer transition-all"
            @click="executeWorkflow(workflow)"
          >
            <div class="flex items-center gap-3 mb-3">
              <span class="text-2xl">{{ workflow.icon }}</span>
              <h3 class="font-semibold">{{ workflow.name }}</h3>
            </div>
            <div class="space-y-2">
              <div v-for="(step, idx) in workflow.steps" :key="idx" class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs flex items-center justify-center font-semibold">
                  {{ idx + 1 }}
                </div>
                <span class="text-sm text-gray-600">{{ step }}</span>
              </div>
            </div>
            <button class="w-full mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
              Başlat
            </button>
          </div>
        </div>
      </div>

      <!-- Role-Specific Content -->
      <div v-if="isSeller" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Seller: My Products Performance -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Ürün Performansım</h2>
          <div class="space-y-3">
            <div v-for="product in sellerProducts" :key="product.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div class="flex items-center gap-3">
                <img :src="product.image" :alt="product.name" class="w-12 h-12 rounded object-cover" />
                <div>
                  <p class="font-semibold text-sm">{{ product.name }}</p>
                  <p class="text-xs text-gray-500">{{ product.sales }} satış</p>
                </div>
              </div>
              <div class="text-right">
                <p class="font-bold text-green-600">{{ product.revenue }}</p>
                <p class="text-xs text-gray-500">Stok: {{ product.stock }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Seller: Pending Orders -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Bekleyen Siparişlerim</h2>
          <div class="space-y-3">
            <div v-for="order in sellerOrders" :key="order.id" class="p-3 border-l-4 border-orange-500 bg-orange-50 rounded">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-semibold text-sm">Sipariş #{{ order.id }}</p>
                  <p class="text-xs text-gray-600 mt-1">{{ order.product }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ order.buyer }}</p>
                </div>
                <div class="text-right">
                  <p class="font-bold text-green-600">{{ order.amount }}</p>
                  <p class="text-xs text-orange-600 mt-1">{{ order.status }}</p>
                </div>
              </div>
              <button class="mt-2 w-full px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white rounded text-xs font-semibold">
                Hemen İşle
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="isBuyer" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Buyer: My Orders -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Siparişlerim</h2>
          <div class="space-y-3">
            <div v-for="order in buyerOrders" :key="order.id" class="p-3 bg-gray-50 rounded-lg">
              <div class="flex justify-between items-start">
                <div class="flex gap-3">
                  <img :src="order.image" :alt="order.product" class="w-16 h-16 rounded object-cover" />
                  <div>
                    <p class="font-semibold text-sm">{{ order.product }}</p>
                    <p class="text-xs text-gray-500">{{ order.seller }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ order.date }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="font-bold">{{ order.amount }}</p>
                  <span :class="getOrderStatusClass(order.status)" class="text-xs px-2 py-1 rounded-full">
                    {{ order.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Buyer: Recommendations -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Sizin İçin Seçtiklerimiz</h2>
          <div class="grid grid-cols-2 gap-3">
            <div v-for="product in recommendations" :key="product.id" class="border rounded-lg p-3 hover:shadow-md cursor-pointer transition-all">
              <img :src="product.image" :alt="product.name" class="w-full h-32 object-cover rounded mb-2" />
              <p class="font-semibold text-sm">{{ product.name }}</p>
              <p class="text-xs text-gray-500">{{ product.seller }}</p>
              <div class="flex justify-between items-center mt-2">
                <p class="font-bold text-blue-600">{{ product.price }}</p>
                <button class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                  Sepete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="isAdmin" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Admin: Platform Metrics -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Platform Metrikleri</h2>
          <div class="space-y-4">
            <div v-for="metric in platformMetrics" :key="metric.label">
              <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600">{{ metric.label }}</span>
                <span class="font-semibold">{{ metric.value }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div :class="metric.color" class="h-2 rounded-full" :style="{ width: metric.percentage }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Admin: Pending Approvals -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Onay Bekleyenler</h2>
          <div class="space-y-3">
            <div v-for="approval in pendingApprovals" :key="approval.id" class="p-3 border-l-4 border-yellow-500 bg-yellow-50 rounded">
              <p class="font-semibold text-sm">{{ approval.type }}</p>
              <p class="text-xs text-gray-600 mt-1">{{ approval.description }}</p>
              <p class="text-xs text-gray-400 mt-1">{{ approval.time }}</p>
              <div class="flex gap-2 mt-2">
                <button class="flex-1 px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs">
                  Onayla
                </button>
                <button class="flex-1 px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
                  Reddet
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Admin: Active Disputes -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Anlaşmazlıklar</h2>
          <div class="space-y-3">
            <div v-for="dispute in activeDisputes" :key="dispute.id" class="p-3 border-l-4 border-red-500 bg-red-50 rounded">
              <p class="font-semibold text-sm">{{ dispute.title }}</p>
              <p class="text-xs text-gray-600 mt-1">{{ dispute.parties }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ dispute.issue }}</p>
              <button class="mt-2 w-full px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold">
                İncele
              </button>
            </div>
          </div>
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
            <span class="font-medium text-sm">{{ action.label }}</span>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import c2cService from '@/services/c2cMarketplace'

const router = useRouter()

// User role management
type UserRole = 'seller' | 'buyer' | 'admin'
const currentRole = ref<UserRole>('seller')
const canSwitchRoles = ref(true) // Set to true for demo, false in production

// Computed role checks
const isSeller = computed(() => currentRole.value === 'seller')
const isBuyer = computed(() => currentRole.value === 'buyer')
const isAdmin = computed(() => currentRole.value === 'admin')

// Dashboard title based on role
const dashboardTitle = computed(() => {
  switch(currentRole.value) {
    case 'seller': return 'Satıcı Paneliniz'
    case 'buyer': return 'Alıcı Paneliniz'
    case 'admin': return 'Platform Yönetim Paneli'
  }
})

const roleLabel = computed(() => {
  switch(currentRole.value) {
    case 'seller': return 'Satıcı'
    case 'buyer': return 'Alıcı'
    case 'admin': return 'Platform Admin'
  }
})

const roleBadgeClass = computed(() => {
  switch(currentRole.value) {
    case 'seller': return 'bg-blue-100 text-blue-700'
    case 'buyer': return 'bg-green-100 text-green-700'
    case 'admin': return 'bg-purple-100 text-purple-700'
  }
})

const moduleHubTitle = computed(() => {
  switch(currentRole.value) {
    case 'seller': return 'Satıcı Araçları'
    case 'buyer': return 'Alışveriş Araçları'
    case 'admin': return 'Yönetim Modülleri'
  }
})

// User Info
const user = JSON.parse(localStorage.getItem('user') || '{}')
const userName = ref(user.name || 'Demo User')
const userEmail = ref(user.email || 'demo@sportoonline.com')
const userAvatar = ref(user.avatar || 'https://ui-avatars.com/api/?name=' + userName.value)

// State
const showNotifications = ref(false)
const showQuickActions = ref(false)
const selectedCategory = ref('all')
const unreadNotifications = ref(5)

// Role-based stats
const stats = computed(() => {
  if (isSeller.value) {
    return [
      { label: 'Toplam Kazanç', value: '₺45,230', change: '+18.5%', trend: 'up', icon: 'money' },
      { label: 'Aktif Ürünlerim', value: '23', change: '+3 yeni', trend: 'up', icon: 'box' },
      { label: 'Bekleyen Sipariş', value: '8', change: '+2 yeni', trend: 'up', icon: 'cart' },
      { label: 'Mağaza Puanım', value: '4.8', change: '+0.2', trend: 'up', icon: 'star' }
    ]
  } else if (isBuyer.value) {
    return [
      { label: 'Toplam Harcama', value: '₺12,450', change: '+5.2%', trend: 'up', icon: 'card' },
      { label: 'Aktif Sipariş', value: '3', change: 'Yolda', trend: 'neutral', icon: 'box' },
      { label: 'Favorilerim', value: '45', change: '+8 yeni', trend: 'up', icon: 'heart' },
      { label: 'Kazanılan Puan', value: '1,240', change: '+120', trend: 'up', icon: 'gift' }
    ]
  } else {
    return [
      { label: 'Platform Geliri', value: '₺324,500', change: '+22.5%', trend: 'up', icon: 'money' },
      { label: 'Toplam Satıcı', value: '1,234', change: '+45 yeni', trend: 'up', icon: 'users' },
      { label: 'Günlük İşlem', value: '567', change: '+12.3%', trend: 'up', icon: 'chart' },
      { label: 'Aktif Kampanya', value: '23', change: '-2', trend: 'down', icon: 'target' }
    ]
  }
})

// Categories
const categories = computed(() => {
  if (isSeller.value) {
    return [
      { value: 'products', label: 'Ürünler' },
      { value: 'orders', label: 'Siparişler' },
      { value: 'marketing', label: 'Pazarlama' },
      { value: 'analytics', label: 'Analiz' },
      { value: 'settings', label: 'Ayarlar' }
    ]
  } else if (isBuyer.value) {
    return [
      { value: 'shopping', label: 'Alışveriş' },
      { value: 'orders', label: 'Siparişler' },
      { value: 'account', label: 'Hesabım' },
      { value: 'support', label: 'Destek' }
    ]
  } else {
    return [
      { value: 'platform', label: 'Platform' },
      { value: 'operations', label: 'Operasyonlar' },
      { value: 'marketing', label: 'Pazarlama' },
      { value: 'analytics', label: 'Analitik' },
      { value: 'system', label: 'Sistem' }
    ]
  }
})

// Role-based modules
const modules = computed(() => {
  if (isSeller.value) {
    return [
      { id: 'my-products', name: 'Ürünlerim', category: 'products', icon: 'box', color: 'blue', route: '/seller/products', badge: '23 aktif' },
      { id: 'add-product', name: 'Yeni Ürün', category: 'products', icon: 'plus', color: 'blue', route: '/seller/products/add' },
      { id: 'stock', name: 'Stok Yönetimi', category: 'products', icon: 'chart', color: 'blue', route: '/seller/stock' },
      { id: 'seller-orders', name: 'Siparişlerim', category: 'orders', icon: 'cart', color: 'green', route: '/seller/orders', badge: '8 bekliyor' },
      { id: 'shipments', name: 'Kargo Takip', category: 'orders', icon: 'truck', color: 'green', route: '/seller/shipments' },
      { id: 'my-campaigns', name: 'Kampanyalarım', category: 'marketing', icon: 'target', color: 'purple', route: '/seller/campaigns' },
      { id: 'discounts', name: 'İndirim Oluştur', category: 'marketing', icon: 'tag', color: 'purple', route: '/seller/discounts' },
      { id: 'seller-analytics', name: 'Satış Analizi', category: 'analytics', icon: 'chart', color: 'orange', route: '/seller/analytics' },
      { id: 'customer-insights', name: 'Müşteri İstatistikleri', category: 'analytics', icon: 'users', color: 'orange', route: '/seller/customers' },
      { id: 'store-settings', name: 'Mağaza Ayarları', category: 'settings', icon: 'store', color: 'red', route: '/seller/store' },
      { id: 'seller-reviews', name: 'Değerlendirmeler', category: 'settings', icon: 'star', color: 'red', route: '/seller/reviews', badge: '4.8 puan' },
      { id: 'seller-messages', name: 'Müşteri Mesajları', category: 'settings', icon: 'chat', color: 'red', route: '/seller/messages', badge: '3 yeni' },
      { id: 'payouts', name: 'Ödemelerim', category: 'settings', icon: 'money', color: 'red', route: '/seller/payouts' }
    ]
  } else if (isBuyer.value) {
    return [
      { id: 'browse', name: 'Ürün Ara', category: 'shopping', icon: 'search', color: 'blue', route: '/marketplace' },
      { id: 'favorites', name: 'Favorilerim', category: 'shopping', icon: 'heart', color: 'blue', route: '/favorites', badge: '45 ürün' },
      { id: 'cart', name: 'Sepetim', category: 'shopping', icon: 'cart', color: 'blue', route: '/cart', badge: '3 ürün' },
      { id: 'buyer-orders', name: 'Siparişlerim', category: 'orders', icon: 'box', color: 'green', route: '/orders', badge: '3 aktif' },
      { id: 'tracking', name: 'Kargo Takibi', category: 'orders', icon: 'truck', color: 'green', route: '/orders/tracking' },
      { id: 'addresses', name: 'Adreslerim', category: 'account', icon: 'pin', color: 'purple', route: '/account/addresses' },
      { id: 'payment-methods', name: 'Ödeme Yöntemleri', category: 'account', icon: 'card', color: 'purple', route: '/account/payments' },
      { id: 'buyer-reviews', name: 'Değerlendirmelerim', category: 'account', icon: 'star', color: 'purple', route: '/reviews' },
      { id: 'buyer-messages', name: 'Mesajlarım', category: 'support', icon: 'chat', color: 'orange', route: '/messages', badge: '2 yeni' },
      { id: 'returns', name: 'İade & Değişim', category: 'support', icon: 'return', color: 'orange', route: '/returns' },
      { id: 'help', name: 'Yardım Merkezi', category: 'support', icon: 'help', color: 'orange', route: '/help' }
    ]
  } else {
    return [
      { id: 'dashboard', name: 'Platform Dashboard', category: 'platform', icon: '📊', color: 'blue', route: '/admin/dashboard' },
      { id: 'improved-dashboard', name: 'Gelişmiş Dashboard', category: 'platform', icon: '🎯', color: 'blue', route: '/admin/dashboard-new' },
      { id: 'sellers', name: 'Satıcı Yönetimi', category: 'platform', icon: '👥', color: 'blue', route: '/admin/sellers', badge: '1,234 satıcı' },
      { id: 'seller-applications', name: 'Satıcı Başvuruları', category: 'platform', icon: '📋', color: 'blue', route: '/admin/seller-applications' },
      { id: 'customers', name: 'Müşteri Yönetimi', category: 'platform', icon: '👤', color: 'blue', route: '/admin/customers' },
      { id: 'all-orders', name: 'Sipariş Yönetimi', category: 'operations', icon: '🛒', color: 'green', route: '/admin/orders', badge: '567 bugün' },
      { id: 'categories', name: 'Kategori Yönetimi', category: 'operations', icon: '📑', color: 'green', route: '/admin/categories' },
      { id: 'banners', name: 'Banner Yönetimi', category: 'marketing', icon: '🖼️', color: 'purple', route: '/admin/banners' },
      { id: 'pages', name: 'Sayfa Yönetimi', category: 'marketing', icon: '📄', color: 'purple', route: '/admin/pages' },
      { id: 'reports', name: 'Raporlar & Analiz', category: 'analytics', icon: '📈', color: 'orange', route: '/admin/reports' },
      { id: 'settings-admin', name: 'Sistem Ayarları', category: 'system', icon: '⚙️', color: 'red', route: '/admin/settings' },
      { id: 'notifications', name: 'Bildirim Merkezi', category: 'system', icon: '🔔', color: 'red', route: '/admin/notifications' },
      { id: 'theme', name: 'Tema Yönetimi', category: 'system', icon: '🎨', color: 'red', route: '/admin/theme' }
    ]
  }
})

// Filtered modules
const filteredModules = computed(() => {
  if (selectedCategory.value === 'all') return modules.value
  return modules.value.filter(m => m.category === selectedCategory.value)
})

// Role-based workflows
const workflows = computed(() => {
  if (isSeller.value) {
    return [
      {
        id: 'product-launch',
        name: 'Yeni Ürün Lansmanı',
        steps: ['Ürün Ekle', 'Fotoğraf Yükle', 'Stok Gir', 'Kampanya Oluştur', 'Yayınla'],
        icon: 'rocket'
      },
      {
        id: 'bulk-discount',
        name: 'Toplu İndirim',
        steps: ['Ürün Seç', 'İndirim Oranı Belirle', 'Süre Ayarla', 'Uygula'],
        icon: '🏷️'
      },
      {
        id: 'order-process',
        name: 'Sipariş İşleme',
        steps: ['Sipariş Onayla', 'Ürün Hazırla', 'Kargo Gönder', 'Takip No Paylaş'],
        icon: 'box'
      },
      {
        id: 'stock-update',
        name: 'Stok Güncelleme',
        steps: ['Stok Sayımı', 'Sistem Güncellemesi', 'Düşük Stok Uyarısı'],
        icon: '📊'
      }
    ]
  } else if (isBuyer.value) {
    return [
      {
        id: 'quick-order',
        name: 'Hızlı Sipariş',
        steps: ['Ürün Seç', 'Sepete Ekle', 'Adres Seç', 'Ödeme Yap', 'Onayla'],
        icon: 'bolt'
      },
      {
        id: 'wishlist-to-cart',
        name: 'Favorilerden Sepete',
        steps: ['Favori Seç', 'Fiyat Kontrolü', 'Toplu Sepete Ekle', 'Ödeme'],
        icon: 'heart'
      },
      {
        id: 'return-process',
        name: 'İade Süreci',
        steps: ['İade Talebi', 'Sebep Belirt', 'Onay Bekle', 'Kargo Gönder', 'Para İadesi'],
        icon: 'return'
      }
    ]
  } else {
    return [
      {
        id: 'seller-onboarding',
        name: 'Satıcı Onay Süreci',
        steps: ['Başvuru İnceleme', 'Kimlik Doğrulama', 'Belge Kontrolü', 'Mağaza Kurulumu', 'Onay'],
        icon: '✅'
      },
      {
        id: 'dispute-resolution',
        name: 'Anlaşmazlık Çözümü',
        steps: ['Şikayet İnceleme', 'Tarafları Dinle', 'Kanıt Topla', 'Karar Ver', 'Uygula'],
        icon: '⚖️'
      },
      {
        id: 'platform-campaign',
        name: 'Platform Kampanyası',
        steps: ['Kategori Seç', 'Satıcılar Davet Et', 'İndirim Belirle', 'Banner Hazırla', 'Yayınla'],
        icon: '🎯'
      },
      {
        id: 'quality-check',
        name: 'Kalite Kontrolü',
        steps: ['Rastgele Ürün Seç', 'Satıcıya Bildir', 'Örnek İste', 'Test Et', 'Rapor'],
        icon: '🔍'
      },
      {
        id: 'fraud-detection',
        name: 'Dolandırıcılık Tespiti',
        steps: ['Şüpheli İşlem Tara', 'Hesap İncele', 'Satıcı/Alıcı Sorgula', 'Aksiyon Al'],
        icon: '🛡️'
      }
    ]
  }
})

// Recent activities
const recentActivities = ref([
  { id: 1, icon: 'cart', title: 'Yeni Sipariş', description: 'Ali Yılmaz sepetini tamamladı', time: '5 dk önce' },
  { id: 2, icon: 'star', title: 'Yeni Değerlendirme', description: 'Ayşe K. mağazanıza 5 yıldız verdi', time: '12 dk önce' },
  { id: 3, icon: 'box', title: 'Ürün Güncellendi', description: 'Nike Air Max stoğu güncellendi', time: '25 dk önce' },
  { id: 4, icon: 'chat', title: 'Yeni Mesaj', description: 'Mehmet Demir sorusu var', time: '1 saat önce' },
  { id: 5, icon: 'target', title: 'Kampanya Başladı', description: 'Yaz İndirimleri aktif', time: '2 saat önce' }
])

// Quick actions
const quickActions = computed(() => {
  if (isSeller.value) {
    return [
      { id: 'add-product', icon: 'plus', label: 'Yeni Ürün Ekle' },
      { id: 'process-orders', icon: 'box', label: 'Siparişleri İşle' },
      { id: 'create-campaign', icon: 'target', label: 'Kampanya Oluştur' },
      { id: 'view-analytics', icon: 'chart', label: 'Satış Analizi' },
      { id: 'messages', icon: 'chat', label: 'Mesajlar' }
    ]
  } else if (isBuyer.value) {
    return [
      { id: 'browse', icon: 'search', label: 'Ürün Ara' },
      { id: 'cart', icon: 'cart', label: 'Sepetim' },
      { id: 'track-order', icon: 'box', label: 'Sipariş Takip' },
      { id: 'favorites', icon: 'heart', label: 'Favorilerim' },
      { id: 'support', icon: 'help', label: 'Yardım' }
    ]
  } else {
    return [
      { id: 'approve-seller', icon: '✅', label: 'Satıcı Onayla' },
      { id: 'review-dispute', icon: '⚖️', label: 'Anlaşmazlık İncele' },
      { id: 'create-campaign', icon: '🎯', label: 'Platform Kampanyası' },
      { id: 'view-reports', icon: '📊', label: 'Raporlar' },
      { id: 'moderation', icon: '🛡️', label: 'Moderasyon' }
    ]
  }
})

// Seller-specific data
const sellerProducts = ref([
  { id: 1, name: 'Nike Air Max', image: 'https://picsum.photos/200/200?1', sales: 45, revenue: '₺12,350', stock: 23 },
  { id: 2, name: 'Adidas Ultraboost', image: 'https://picsum.photos/200/200?2', sales: 32, revenue: '₺9,800', stock: 15 },
  { id: 3, name: 'Puma RS-X', image: 'https://picsum.photos/200/200?3', sales: 28, revenue: '₺7,200', stock: 8 },
  { id: 4, name: 'Reebok Classic', image: 'https://picsum.photos/200/200?4', sales: 19, revenue: '₺4,950', stock: 32 }
])

const sellerOrders = ref([
  { id: 'ORD-1234', product: 'Nike Air Max', buyer: 'Ali Yılmaz', amount: '₺450', status: 'Hazırlanıyor' },
  { id: 'ORD-1235', product: 'Adidas Ultraboost', buyer: 'Ayşe Kaya', amount: '₺520', status: 'Kargo Bekliyor' },
  { id: 'ORD-1236', product: 'Puma RS-X', buyer: 'Mehmet Demir', amount: '₺380', status: 'Onay Bekliyor' }
])

// Buyer-specific data
const buyerOrders = ref([
  { id: 'ORD-5678', product: 'Nike Air Max', seller: 'Spor Dünyası', amount: '₺450', status: 'Yolda', date: '15 Kas 2025', image: 'https://picsum.photos/200/200?5' },
  { id: 'ORD-5679', product: 'Adidas Ultraboost', seller: 'Ayakkabı Merkezi', amount: '₺520', status: 'Hazırlanıyor', date: '16 Kas 2025', image: 'https://picsum.photos/200/200?6' },
  { id: 'ORD-5680', product: 'Puma RS-X', seller: 'Koşu Market', amount: '₺380', status: 'Teslim Edildi', date: '10 Kas 2025', image: 'https://picsum.photos/200/200?7' }
])

const recommendations = ref([
  { id: 1, name: 'New Balance 550', seller: 'Sneaker Store', price: '₺1,299', image: 'https://picsum.photos/200/200?8' },
  { id: 2, name: 'Converse Chuck', seller: 'Classic Shoes', price: '₺899', image: 'https://picsum.photos/200/200?9' },
  { id: 3, name: 'Vans Old Skool', seller: 'Street Style', price: '₺1,099', image: 'https://picsum.photos/200/200?10' },
  { id: 4, name: 'Asics Gel', seller: 'Run Shop', price: '₺1,499', image: 'https://picsum.photos/200/200?11' }
])

// Admin-specific data
const platformMetrics = ref([
  { label: 'Satıcı Aktivitesi', value: '87%', percentage: '87%', color: 'bg-green-500' },
  { label: 'Alıcı Memnuniyeti', value: '4.6/5', percentage: '92%', color: 'bg-blue-500' },
  { label: 'İşlem Başarı Oranı', value: '94%', percentage: '94%', color: 'bg-purple-500' },
  { label: 'Ortalama Teslimat', value: '3.2 gün', percentage: '78%', color: 'bg-orange-500' }
])

const pendingApprovals = ref([
  { id: 1, type: 'Yeni Satıcı Başvurusu', description: 'Spor Dünyası mağazası onay bekliyor', time: '10 dk önce' },
  { id: 2, type: 'Ürün Onayı', description: 'Nike Air Max Pro - kalite kontrolü', time: '1 saat önce' },
  { id: 3, type: 'İade Talebi', description: 'Sipariş #1234 için iade onayı', time: '2 saat önce' }
])

const activeDisputes = ref([
  { id: 1, title: 'Ürün Uyuşmazlığı', parties: 'Ali Y. vs Spor Mağaza', issue: 'Ürün açıklaması ile uyuşmuyor' },
  { id: 2, title: 'Geç Teslimat', parties: 'Ayşe K. vs Hızlı Kargo', issue: '10 gün gecikme' },
  { id: 3, title: 'Kalite Şikayeti', parties: 'Mehmet D. vs Ayakkabı Dünyası', issue: 'Ürün hasarlı geldi' }
])

// Methods
const getTrendIcon = (trend: string) => {
  if (trend === 'up') return '↗️'
  if (trend === 'down') return '↘️'
  return '→'
}

const getTrendClass = (trend: string) => {
  if (trend === 'up') return 'text-green-600 font-semibold'
  if (trend === 'down') return 'text-red-600 font-semibold'
  return 'text-gray-600'
}

const getModuleCardClass = (color: string) => {
  const colorMap: Record<string, string> = {
    blue: 'bg-blue-50 hover:bg-blue-100 border border-blue-200',
    green: 'bg-green-50 hover:bg-green-100 border border-green-200',
    purple: 'bg-purple-50 hover:bg-purple-100 border border-purple-200',
    orange: 'bg-orange-50 hover:bg-orange-100 border border-orange-200',
    red: 'bg-red-50 hover:bg-red-100 border border-red-200'
  }
  return colorMap[color] || 'bg-gray-50 hover:bg-gray-100'
}

const getOrderStatusClass = (status: string) => {
  if (status === 'Yolda') return 'bg-blue-100 text-blue-700'
  if (status === 'Hazırlanıyor') return 'bg-yellow-100 text-yellow-700'
  if (status === 'Teslim Edildi') return 'bg-green-100 text-green-700'
  return 'bg-gray-100 text-gray-700'
}

const handleStatClick = (stat: any) => {
  console.log('Stat clicked:', stat)
}

const openModule = (module: any) => {
  if (module.route) {
    router.push(module.route)
  }
}

const executeWorkflow = (workflow: any) => {
  console.log('Executing workflow:', workflow)
  alert(`${workflow.name} iş akışı başlatılıyor...`)
}

const executeQuickAction = (action: any) => {
  console.log('Quick action:', action)
  showQuickActions.value = false
  
  // Execute via API
  c2cService.executeQuickAction({ action_id: action.id })
    .then(response => {
      if (response.redirect) {
        router.push(response.redirect)
      }
    })
    .catch(error => {
      console.error('Quick action failed:', error)
    })
}

// Load dashboard data from API
const loadDashboardData = async () => {
  try {
    const data = await c2cService.getDashboardData()
    
    // Update current role from backend
    if (data.role) {
      currentRole.value = data.role as UserRole
    }
    
    // Update stats if provided by backend
    // This would override the mock data with real data
    console.log('Dashboard data loaded:', data)
    
    // You can map the backend data to your reactive refs here
    // Example:
    // if (data.products) sellerProducts.value = data.products
    // if (data.orders) sellerOrders.value = data.orders
    
  } catch (error) {
    console.error('Failed to load dashboard data:', error)
    // Fall back to mock data (current implementation)
  }
}

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  localStorage.removeItem('role')
  console.log('Logging out...')
  window.location.href = '/login'
}

onMounted(() => {
  console.log('C2C Marketplace Dashboard loaded for role:', currentRole.value)
  
  // Load real data from API
  loadDashboardData()
})
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from {
  transform: translateY(20px);
  opacity: 0;
}

.slide-up-leave-to {
  transform: translateY(20px);
  opacity: 0;
}

/* Scrollbar styling */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
