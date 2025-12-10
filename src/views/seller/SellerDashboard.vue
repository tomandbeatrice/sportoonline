<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header removed - now handled by SellerLayout -->

    <div class="px-4 sm:px-6 py-8">
      <!-- Welcome Banner -->
      <div class="bg-gradient-to-r from-orange-500 via-orange-600 to-red-600 rounded-3xl p-8 text-white mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
          <div>
            <h1 class="text-3xl font-bold mb-2">Hoş geldin, {{ store.name }}! 👋</h1>
            <p class="text-orange-100">
              Bugün {{ stats.todayOrders }} yeni sipariş aldın. 
              <span v-if="stats.todayRevenue > 0">Günlük gelirin: {{ formatCurrency(stats.todayRevenue) }}</span>
            </p>
          </div>
          <div class="flex gap-3">
            <router-link to="/seller/products" aria-label="Ürün ekle" class="bg-white/20 backdrop-blur hover:bg-white/30 text-white px-6 py-3 rounded-xl font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white">
              📦 Ürün Ekle
            </router-link>
            <router-link to="/seller/campaigns" aria-label="Kampanya oluştur" class="bg-white text-orange-600 px-6 py-3 rounded-xl font-semibold hover:bg-orange-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white">
              🎯 Kampanya Oluştur
            </router-link>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div v-if="!stats || Object.keys(stats).length === 0" class="mb-8">
        <EmptyState :title="'İstatistik bulunamadı'" :description="'Henüz sipariş veya gelir verisi yok.'">
          <template #icon>
            <span class="text-5xl">📊</span>
          </template>
        </EmptyState>
      </div>
      <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <StatCard 
          label="Bugünkü Sipariş" 
          :value="stats.todayOrders" 
          icon="📦" 
          :delta="'%'+Math.abs(stats.orderChange)+' düne göre'" 
          :trend="stats.orderChange >= 0 ? 'up' : 'down'" 
          :status="stats.orderChange >= 0 ? 'success' : 'warning'"
        />
        <StatCard 
          label="Bugünkü Gelir" 
          :value="formatCurrency(stats.todayRevenue)" 
          icon="💰" 
          :delta="'%'+Math.abs(stats.revenueChange)+' düne göre'" 
          :trend="stats.revenueChange >= 0 ? 'up' : 'down'" 
          :status="stats.revenueChange >= 0 ? 'success' : 'warning'"
        />
        <StatCard 
          label="Bekleyen Sipariş" 
          :value="stats.pendingOrders" 
          icon="⏳" 
          hint="Onay bekliyor" 
          status="neutral"
        />
        <StatCard 
          label="Mağaza Puanı" 
          :value="stats.rating" 
          icon="⭐" 
          hint="{{ stats.totalReviews }} değerlendirme" 
          status="neutral"
        />
      </div>

      <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-lg font-bold text-slate-900">Gelir Trendi</h3>
              <p class="text-sm text-slate-500">Son 7 günlük satış performansı</p>
            </div>
            <select v-model="chartPeriod" class="text-sm border border-slate-200 rounded-lg px-3 py-2">
              <option value="7">Son 7 Gün</option>
              <option value="30">Son 30 Gün</option>
              <option value="90">Son 3 Ay</option>
            </select>
          </div>
          <div class="h-[300px] flex items-center justify-center text-slate-400">
            <RevenueChart 
              :data="chartData" 
              :labels="chartLabels" 
              :period="chartPeriod" 
            />
          </div>
        </div>

        <!-- Quick Actions & AI Insights -->
        <div class="space-y-6">
          <!-- AI Insights -->
          <AISellerInsights @action="handleAIAction" />

          <!-- Quick Actions -->
          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Hızlı İşlemler</h3>
          <div class="space-y-3">
            <router-link to="/seller/products" aria-label="Ürün yönetimi" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-300">
              <span class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-xl">📦</span>
              <div>
                <p class="font-medium text-slate-900">Ürün Yönetimi</p>
                <p class="text-xs text-slate-500">{{ stats.totalProducts }} ürün</p>
              </div>
            </router-link>
            <router-link to="/seller/orders" aria-label="Siparişler" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-300">
              <span class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center text-xl">🛒</span>
              <div>
                <p class="font-medium text-slate-900">Siparişler</p>
                <p class="text-xs text-slate-500">{{ stats.pendingOrders }} bekleyen</p>
              </div>
            </router-link>
            <router-link to="/seller/campaigns" aria-label="Kampanyalar" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-300">
              <span class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-xl">🎯</span>
              <div>
                <p class="font-medium text-slate-900">Kampanyalar</p>
                <p class="text-xs text-slate-500">{{ stats.activeCampaigns }} aktif</p>
              </div>
            </router-link>
            <router-link to="/seller/financial-report" aria-label="Finansal rapor" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-300">
              <span class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-xl">💵</span>
              <div>
                <p class="font-medium text-slate-900">Finansal Rapor</p>
                <p class="text-xs text-slate-500">Gelir & komisyon</p>
              </div>
            </router-link>
          </div>
        </div>
      </div>
      </div>

      <div class="grid lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-900">Son Siparişler</h3>
            <router-link to="/seller/orders" class="text-sm text-orange-600 hover:underline">
              Tümünü Gör →
            </router-link>
          </div>
          <div class="space-y-4">
            <div 
              v-for="order in recentOrders" 
              :key="order.id"
              class="flex items-center justify-between p-4 rounded-xl bg-slate-50"
            >
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center text-sm font-bold">
                  #{{ order.id }}
                </div>
                <div>
                  <p class="font-medium text-slate-900">{{ order.customerName }}</p>
                  <p class="text-xs text-slate-500">{{ order.itemCount }} ürün • {{ order.time }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="font-bold text-slate-900">{{ formatCurrency(order.total) }}</p>
                <span :class="getStatusClass(order.status)" class="text-xs px-2 py-0.5 rounded-full">
                  {{ getStatusText(order.status) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-900">En Çok Satan Ürünler</h3>
            <router-link to="/seller/products" class="text-sm text-orange-600 hover:underline">
              Tümünü Gör →
            </router-link>
          </div>
          <div class="space-y-4">
            <div 
              v-for="(product, index) in topProducts" 
              :key="product.id"
              class="flex items-center gap-4"
            >
              <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center font-bold text-sm">
                {{ index + 1 }}
              </span>
              <img :src="product.image" :alt="product.name" class="w-12 h-12 rounded-lg object-cover" />
              <div class="flex-1 min-w-0">
                <p class="font-medium text-slate-900 truncate">{{ product.name }}</p>
                <p class="text-xs text-slate-500">{{ product.soldCount }} satış</p>
              </div>
              <p class="font-bold text-slate-900">{{ formatCurrency(product.revenue) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Alerts & Tips -->
      <div class="grid lg:grid-cols-3 gap-6">
        <!-- Alerts -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
          <h3 class="text-lg font-bold text-slate-900 mb-4">⚠️ Dikkat Edilmesi Gerekenler</h3>
          <div class="space-y-3">
            <div v-if="alerts.lowStock.length > 0" class="flex items-start gap-3 p-4 rounded-xl bg-orange-50 border border-orange-100">
              <span class="text-xl">📦</span>
              <div>
                <p class="font-medium text-orange-800">Düşük Stok Uyarısı</p>
                <p class="text-sm text-orange-600">{{ alerts.lowStock.length }} ürünün stoğu azaldı</p>
              </div>
            </div>
            <div v-if="alerts.pendingReviews > 0" class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 border border-blue-100">
              <span class="text-xl">💬</span>
              <div>
                <p class="font-medium text-blue-800">Yanıtlanmamış Yorumlar</p>
                <p class="text-sm text-blue-600">{{ alerts.pendingReviews }} yorum yanıt bekliyor</p>
              </div>
            </div>
            <div v-if="alerts.returnRequests > 0" class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-100">
              <span class="text-xl">🔄</span>
              <div>
                <p class="font-medium text-red-800">İade Talepleri</p>
                <p class="text-sm text-red-600">{{ alerts.returnRequests }} iade talebi bekliyor</p>
              </div>
            </div>
            <div v-if="!alerts.lowStock.length && !alerts.pendingReviews && !alerts.returnRequests" class="text-center py-8 text-slate-400">
              <span class="text-4xl">✅</span>
              <p class="mt-2">Harika! Şu an dikkat edilmesi gereken bir durum yok.</p>
            </div>
          </div>
        </div>

        <!-- Tips -->
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-6 text-white">
          <h3 class="text-lg font-bold mb-4">💡 Satış İpucu</h3>
          <p class="text-purple-100 mb-4">{{ currentTip.text }}</p>
          <button @click="nextTip" aria-label="Sonraki satış ipucunu göster" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-300">
            Sonraki İpucu →
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingSkeleton from '@/components/ui/LoadingSkeleton.vue'
import StatCard from '@/components/ui/StatCard.vue'
import RevenueChart from '@/components/seller/RevenueChart.vue'
import AISellerInsights from '@/components/seller/AISellerInsights.vue'

const router = useRouter()

const chartData = ref([1200, 1900, 1500, 2200, 1800, 2800, 4580])
const chartLabels = ref(['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'])

const handleAIAction = (action: string) => {
  console.log('AI Action:', action)
}

const navItems = [
  { name: 'Dashboard', path: '/seller/dashboard', icon: '📊' },
  { name: 'Ürünler', path: '/seller/products', icon: '📦' },
  { name: 'Siparişler', path: '/seller/orders', icon: '🛒' },
  { name: 'Kampanyalar', path: '/seller/campaigns', icon: '🎯' },
  { name: 'Finans', path: '/seller/financial-report', icon: '💵' },
]

const store = ref({
  name: 'Örnek Mağaza',
  subscription: 'professional'
})

const subscriptionLabel = computed(() => {
  const labels: Record<string, string> = {
    'free': 'Ücretsiz Plan',
    'starter': 'Başlangıç',
    'professional': 'Profesyonel',
    'enterprise': 'Kurumsal'
  }
  return labels[store.value.subscription] || 'Ücretsiz Plan'
})

const unreadNotifications = ref(3)
const chartPeriod = ref('7')

const stats = ref({
  todayOrders: 12,
  todayRevenue: 4580,
  pendingOrders: 5,
  rating: 4.7,
  totalReviews: 234,
  totalProducts: 48,
  activeCampaigns: 2,
  orderChange: 15,
  revenueChange: 23
})

const recentOrders = ref([
  { id: 1542, customerName: 'Ahmet Y.', itemCount: 3, time: '10 dk önce', total: 459, status: 'pending' },
  { id: 1541, customerName: 'Zeynep K.', itemCount: 1, time: '25 dk önce', total: 189, status: 'processing' },
  { id: 1540, customerName: 'Mehmet A.', itemCount: 2, time: '1 saat önce', total: 678, status: 'shipped' },
  { id: 1539, customerName: 'Ayşe D.', itemCount: 4, time: '2 saat önce', total: 1250, status: 'delivered' },
])

const topProducts = ref([
  { id: 1, name: 'Nike Air Max 270', image: 'https://placehold.co/100x100/F97316/FFFFFF?text=Nike', soldCount: 45, revenue: 8550 },
  { id: 2, name: 'Adidas Ultraboost', image: 'https://placehold.co/100x100/3B82F6/FFFFFF?text=Adidas', soldCount: 38, revenue: 7220 },
  { id: 3, name: 'Puma RS-X', image: 'https://placehold.co/100x100/22C55E/FFFFFF?text=Puma', soldCount: 29, revenue: 4060 },
  { id: 4, name: 'New Balance 574', image: 'https://placehold.co/100x100/8B5CF6/FFFFFF?text=NB', soldCount: 24, revenue: 3120 },
])

const alerts = ref({
  lowStock: ['Ürün 1', 'Ürün 2'],
  pendingReviews: 5,
  returnRequests: 2
})

const tips = [
  { text: 'Ürün fotoğraflarınızı profesyonel çekin. Kaliteli görseller satışları %40 artırabilir.' },
  { text: 'Hafta sonu kampanyaları %25 daha fazla dönüşüm sağlar.' },
  { text: 'Müşteri yorumlarına hızlı yanıt verin. Bu, mağaza puanınızı yükseltir.' },
  { text: 'Stok takibini düzenli yapın. Stok dışı ürünler müşteri kaybına neden olur.' },
]

const currentTipIndex = ref(0)
const currentTip = computed(() => tips[currentTipIndex.value])

const nextTip = () => {
  currentTipIndex.value = (currentTipIndex.value + 1) % tips.length
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-700',
    'processing': 'bg-blue-100 text-blue-700',
    'shipped': 'bg-purple-100 text-purple-700',
    'delivered': 'bg-green-100 text-green-700',
    'cancelled': 'bg-red-100 text-red-700'
  }
  return classes[status] || 'bg-slate-100 text-slate-700'
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    'pending': 'Bekliyor',
    'processing': 'Hazırlanıyor',
    'shipped': 'Kargoda',
    'delivered': 'Teslim Edildi',
    'cancelled': 'İptal'
  }
  return texts[status] || status
}

const handleLogout = () => {
  router.push('/login')
}
</script>
