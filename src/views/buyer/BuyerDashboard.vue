<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <router-link to="/dashboard" class="text-2xl font-bold text-gray-900">
              SportoOnline
            </router-link>
            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
              ALICI
            </span>
          </div>
          
          <nav class="flex items-center gap-4">
            <router-link to="/dashboard" class="text-sm text-gray-600 hover:text-gray-900">
              Ana Sayfa
            </router-link>
            
            <!-- Notification Bell -->
            <NotificationDropdown
              :notifications="notifications"
              :unreadCount="unreadCount"
              :markAsRead="markAsRead"
              :markAllAsRead="markAllAsRead"
              :clearAll="clearAll"
            />
            
            <button 
              @click="handleLogout"
              class="rounded-lg bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100"
            >
              Cikis
            </button>
          </nav>
        </div>
      </div>
    </header>

    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Hesabım</h1>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Toplam Sipariş</span>
            <BadgeIcon name="shopping-bag" cls="w-8 h-8 text-blue-500" />
          </div>
          <p class="text-3xl font-bold text-gray-900">{{ stats.total_orders || 0 }}</p>
          <p class="text-xs text-green-600 mt-1">+12% bu ay</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Toplam Harcama</span>
            <BadgeIcon name="currency-dollar" cls="w-8 h-8 text-green-500" />
          </div>
          <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(stats.total_spent || 0) }}</p>
          <p class="text-xs text-gray-500 mt-1">Son 6 ay</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Favoriler</span>
            <BadgeIcon name="heart" cls="w-8 h-8 text-red-500" />
          </div>
          <p class="text-3xl font-bold text-gray-900">{{ stats.favorites_count || 0 }}</p>
          <p class="text-xs text-gray-500 mt-1">Kayıtlı ürün</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Ortalama Sepet</span>
            <BadgeIcon name="chart-bar" cls="w-8 h-8 text-purple-500" />
          </div>
          <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(stats.avg_order_value || 0) }}</p>
          <p class="text-xs text-gray-500 mt-1">Sipariş başına</p>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Spending Trend -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Harcama Trendi</h3>
          <p class="text-sm text-gray-500 mb-6">Son 6 aylık alışveriş harcamalarınız</p>
          <div v-if="!spendingChartData" class="flex h-[300px] items-center justify-center">
            <div class="text-center">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
              <p class="text-sm text-gray-400">Veriler yükleniyor...</p>
            </div>
          </div>
          <LineChart v-else :data="spendingChartData" :height="300" />
        </div>

        <!-- Order Status Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Sipariş Durumları</h3>
          <p class="text-sm text-gray-500 mb-6">Siparişlerinizin güncel durumu</p>
          <div v-if="!orderStatusChartData" class="flex h-[300px] items-center justify-center">
            <div class="text-center">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
              <p class="text-sm text-gray-400">Veriler yükleniyor...</p>
            </div>
          </div>
          <DoughnutChart v-else :data="orderStatusChartData" :height="300" />
        </div>

        <!-- Category Spending -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Kategori Bazlı Harcama</h3>
          <p class="text-sm text-gray-500 mb-6">En çok harcama yaptığınız kategoriler</p>
          <div v-if="!categoryChartData" class="flex h-[300px] items-center justify-center">
            <div class="text-center">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
              <p class="text-sm text-gray-400">Veriler yükleniyor...</p>
            </div>
          </div>
          <BarChart v-else :data="categoryChartData" :height="300" />
        </div>

        <!-- Monthly Orders -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Aylık Sipariş Sayısı</h3>
          <p class="text-sm text-gray-500 mb-6">Son 6 aylık sipariş aktivitesi</p>
          <div v-if="!monthlyOrdersChartData" class="flex h-[300px] items-center justify-center">
            <div class="text-center">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mx-auto mb-4"></div>
              <p class="text-sm text-gray-400">Veriler yükleniyor...</p>
            </div>
          </div>
          <LineChart v-else :data="monthlyOrdersChartData" :height="300" />
        </div>
      </div>

      <!-- Tabs -->
      <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.name }}
          </button>
        </nav>
      </div>

      <!-- Siparislerim -->
      <div v-if="activeTab === 'orders'" class="space-y-4">
        <div v-if="loading" class="text-center py-12">
          <p class="text-gray-500">Yukleniyor...</p>
        </div>
        <div v-else-if="orders.length === 0" class="text-center py-12">
          <p class="text-gray-500">Henuz siparisiniz yok.</p>
        </div>
        <div v-else>
          <div
            v-for="order in orders"
            :key="order.id"
            class="bg-white rounded-lg shadow p-6"
          >
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="font-semibold text-lg">Siparis #{{ order.id }}</h3>
                <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
              </div>
              <span
                :class="[
                  'px-3 py-1 rounded-full text-sm font-medium',
                  getStatusClass(order.status)
                ]"
              >
                {{ getStatusText(order.status) }}
              </span>
            </div>

            <div class="space-y-3">
              <div
                v-for="item in order.items"
                :key="item.id"
                class="flex items-center space-x-4"
              >
                <img
                  :src="item.product.image_url"
                  :alt="item.product.name"
                  class="h-20 w-20 rounded object-cover"
                />
                <div class="flex-1">
                  <h4 class="font-medium line-clamp-1">{{ item.product.name }}</h4>
                  <p class="text-sm text-gray-500">Adet: {{ item.quantity }}</p>
                </div>
                <div class="text-right">
                  <p class="font-semibold">{{ item.price }} TL</p>
                </div>
              </div>
            </div>

            <div class="mt-4 pt-4 border-t flex justify-between items-center">
              <div>
                <p class="text-sm text-gray-600">Toplam</p>
                <p class="text-xl font-bold">{{ order.total }} TL</p>
              </div>
              <button
                @click="viewOrder(order.id)"
                class="px-4 py-2 border border-blue-600 text-blue-600 rounded hover:bg-blue-50"
              >
                Detaylari Gor
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Favorilerim -->
      <div v-if="activeTab === 'favorites'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-if="favorites.length === 0" class="col-span-3 text-center py-12">
          <p class="text-gray-500">Favori urunuz yok.</p>
        </div>
        <div
          v-for="product in favorites"
          :key="product.id"
          class="bg-white rounded-lg shadow overflow-hidden"
        >
          <img
            :src="product.image_url"
            :alt="product.name"
            class="w-full h-48 object-cover"
          />
          <div class="p-4">
            <h3 class="font-semibold mb-2">{{ product.name }}</h3>
            <p class="text-lg font-bold text-blue-600 mb-4">{{ product.price }} TL</p>
            <button
              @click="removeFromFavorites(product.id)"
              class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
            >
              Favorilerden Cikar
            </button>
          </div>
        </div>
      </div>

      <!-- Profil -->
      <div v-if="activeTab === 'profile'" class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <h2 class="text-2xl font-bold mb-6">Profil Bilgileri</h2>
        <form @submit.prevent="updateProfile" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
            <input
              v-model="user.name"
              type="text"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="user.email"
              type="email"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
            <input
              v-model="user.phone"
              type="tel"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
            <textarea
              v-model="user.address"
              rows="3"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            ></textarea>
          </div>
          <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Guncelle
          </button>
        </form>
      </div>

      <!-- Adreslerim -->
      <div v-if="activeTab === 'addresses'" class="space-y-4">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold">Teslimat Adresleri</h2>
          <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Yeni Adres Ekle
          </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="address in addresses"
            :key="address.id"
            class="bg-white rounded-lg shadow p-6"
          >
            <div class="flex justify-between items-start mb-2">
              <h3 class="font-semibold">{{ address.title }}</h3>
              <span
                v-if="address.isDefault"
                class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded"
              >
                Varsayilan
              </span>
            </div>
            <p class="text-sm text-gray-600 mb-4">{{ address.fullAddress }}</p>
            <div class="flex space-x-2">
              <button class="px-3 py-1 text-sm border border-blue-600 text-blue-600 rounded hover:bg-blue-50">
                Duzenle
              </button>
              <button
                @click="deleteAddress(address.id)"
                class="px-3 py-1 text-sm border border-red-600 text-red-600 rounded hover:bg-red-50"
              >
                Sil
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useOrderStore } from '@/stores/orderStore'
import { toast } from 'vue3-toastify'
import LineChart from '@/components/charts/LineChart.vue'
import BarChart from '@/components/charts/BarChart.vue'
import DoughnutChart from '@/components/charts/DoughnutChart.vue'
import NotificationDropdown from '@/components/NotificationDropdown.vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
import { useNotifications } from '@/composables/useNotifications'

const router = useRouter()
const orderStore = useOrderStore()

const activeTab = ref('orders')
const loading = ref(false)
const stats = ref<any>({
  total_orders: 0,
  total_spent: 0,
  favorites_count: 0,
  avg_order_value: 0
})
const apiOrders = ref<any[]>([])
const apiFavorites = ref<any[]>([])

// Real-time notifications
const buyerUserId = 10 // Mock buyer user ID
const { notifications, unreadCount, markAsRead, markAllAsRead, clearAll } = useNotifications(buyerUserId, 'buyer')

const user = ref({
  name: 'Ahmet Yilmaz',
  email: 'buyer@sportoonline.com',
  phone: '+90 555 123 4567',
  address: 'Istanbul, Turkiye'
})

const tabs = [
  { id: 'orders', name: 'Siparislerim' },
  { id: 'favorites', name: 'Favorilerim' },
  { id: 'profile', name: 'Profilim' },
  { id: 'addresses', name: 'Adreslerim' }
]

const updateProfile = async () => {
  toast.success('Profil guncellendi!')
}

const viewOrder = (orderId: number) => {
  toast.info(`Siparis #${orderId} detaylari (Mock)`)
}

const removeFromFavorites = (productId: number) => {
  orderStore.toggleFavorite({ id: productId })
  toast.success('Urun favorilerden cikarildi')
}

const deleteAddress = (addressId: number) => {
  if (confirm('Bu adresi silmek istediginizden emin misiniz?')) {
    addresses.value = addresses.value.filter((a: any) => a.id !== addressId)
  }
}

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  localStorage.removeItem('role')
  toast.success('Basariyla cikis yapildi')
  window.location.href = '/login'
}

// Mock data
const orders = ref([
  {
    id: 1001,
    status: 'delivered',
    total: 299.99,
    created_at: '2025-11-15',
    items: [
      {
        id: 1,
        product: {
          name: 'Nike Air Max',
          image_url: 'https://picsum.photos/200/200?1'
        },
        quantity: 1,
        price: 299.99
      }
    ]
  },
  {
    id: 1002,
    status: 'shipped',
    total: 450.00,
    created_at: '2025-11-18',
    items: [
      {
        id: 2,
        product: {
          name: 'Adidas Ultraboost',
          image_url: 'https://picsum.photos/200/200?2'
        },
        quantity: 1,
        price: 450.00
      }
    ]
  }
])

const favorites = computed(() => orderStore.favorites)

const addresses = ref([
  {
    id: 1,
    title: 'Ev',
    fullAddress: 'Kadikoy, Istanbul',
    isDefault: true
  },
  {
    id: 2,
    title: 'Is',
    fullAddress: 'Besiktas, Istanbul',
    isDefault: false
  }
])

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR')
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    pending: 'Beklemede',
    processing: 'Hazirlaniyor',
    shipped: 'Kargoda',
    delivered: 'Teslim Edildi',
    cancelled: 'Iptal Edildi'
  }
  return texts[status] || status
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { 
    minimumFractionDigits: 0,
    maximumFractionDigits: 0 
  }).format(value) + ' ₺'
}

// Chart Data Computed Properties
const spendingChartData = computed(() => {
  if (!apiOrders.value?.length) return null
  
  // Last 6 months
  const months = ['Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas']
  const spending = months.map(() => Math.floor(Math.random() * 3000) + 500)
  
  return {
    labels: months,
    datasets: [
      {
        label: 'Harcama (₺)',
        data: spending,
        borderColor: 'rgb(59, 130, 246)',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true
      }
    ]
  }
})

const orderStatusChartData = computed(() => {
  if (!apiOrders.value?.length) return null
  
  const statusCounts = apiOrders.value.reduce((acc: any, order: any) => {
    const status = order.status || 'pending'
    acc[status] = (acc[status] || 0) + 1
    return acc
  }, {})
  
  const labels = Object.keys(statusCounts).map(s => getStatusText(s))
  
  return {
    labels,
    datasets: [
      {
        data: Object.values(statusCounts),
        backgroundColor: [
          'rgba(34, 197, 94, 0.8)',
          'rgba(168, 85, 247, 0.8)',
          'rgba(59, 130, 246, 0.8)',
          'rgba(251, 146, 60, 0.8)'
        ],
        borderColor: [
          'rgb(34, 197, 94)',
          'rgb(168, 85, 247)',
          'rgb(59, 130, 246)',
          'rgb(251, 146, 60)'
        ],
        borderWidth: 2
      }
    ]
  }
})

const categoryChartData = computed(() => {
  // Mock category spending
  const categories = ['Ayakkabı', 'Giyim', 'Aksesuar', 'Elektronik', 'Diğer']
  const spending = categories.map(() => Math.floor(Math.random() * 2000) + 200)
  
  return {
    labels: categories,
    datasets: [
      {
        label: 'Harcama (₺)',
        data: spending,
        backgroundColor: [
          'rgba(168, 85, 247, 0.8)',
          'rgba(59, 130, 246, 0.8)',
          'rgba(34, 197, 94, 0.8)',
          'rgba(251, 146, 60, 0.8)',
          'rgba(148, 163, 184, 0.8)'
        ],
        borderColor: [
          'rgb(168, 85, 247)',
          'rgb(59, 130, 246)',
          'rgb(34, 197, 94)',
          'rgb(251, 146, 60)',
          'rgb(148, 163, 184)'
        ],
        borderWidth: 2
      }
    ]
  }
})

const monthlyOrdersChartData = computed(() => {
  const months = ['Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas']
  const orderCounts = months.map(() => Math.floor(Math.random() * 10) + 1)
  
  return {
    labels: months,
    datasets: [
      {
        label: 'Sipariş Sayısı',
        data: orderCounts,
        borderColor: 'rgb(251, 146, 60)',
        backgroundColor: 'rgba(251, 146, 60, 0.1)',
        tension: 0.4,
        fill: true
      }
    ]
  }
})

// Load Buyer Data
async function loadBuyerData() {
  loading.value = true
  
  try {
    const { buyerApi } = await import('@/services/api/buyerApi')
    
    const [statsData, ordersData, favoritesData] = await Promise.all([
      buyerApi.getStats(),
      buyerApi.getOrders(),
      buyerApi.getFavorites()
    ])
    
    stats.value = statsData
    apiOrders.value = ordersData
    apiFavorites.value = favoritesData
    
    console.log('✅ Buyer data loaded:', { stats: statsData, orders: ordersData.length, favorites: favoritesData.length })
  } catch (error) {
    console.error('❌ Buyer data yüklenemedi:', error)
    toast.error('Veriler yüklenirken hata oluştu')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadBuyerData()
})

</script>
