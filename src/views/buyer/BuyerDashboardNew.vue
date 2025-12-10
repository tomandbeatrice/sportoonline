<template>
  <div class="min-h-screen bg-slate-50 px-4 py-8">
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <StatCard 
        label="Toplam Sipariş" 
        :value="stats.totalOrders" 
        icon="📦" 
        delta="+" 
        :hint="stats.recentOrders + ' bu ay'" 
        trend="up" 
      />
      <StatCard 
        label="Toplam Harcama" 
        :value="formatCurrency(stats.totalSpent)" 
        icon="💰" 
        hint="Tüm zamanlar" 
      />
      <StatCard 
        label="Favorilerim" 
        :value="stats.favorites" 
        icon="❤️" 
        hint="Kayıtlı ürün" 
      />
      <StatCard 
        label="Puan Bakiyesi" 
        :value="stats.points" 
        icon="⭐" 
        hint="Kullanılabilir" 
      />
    </div>

      <!-- Tabs -->
      <div class="bg-white rounded-2xl shadow-sm mb-8">
        <div class="border-b border-slate-100 px-6">
          <nav class="flex gap-6">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'py-4 text-sm font-medium border-b-2 transition-colors relative',
                activeTab === tab.id
                  ? 'border-green-600 text-green-600'
                  : 'border-transparent text-slate-500 hover:text-slate-700'
              ]"
            >
              {{ tab.icon }} {{ tab.name }}
              <span 
                v-if="tab.id === 'insights'" 
                class="absolute top-2 -right-2 w-2 h-2 bg-red-500 rounded-full animate-pulse"
              ></span>
            </button>
          </nav>
        </div>

        <!-- Insights Tab -->
        <div v-if="activeTab === 'insights'" class="p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="font-bold text-lg text-slate-900">Size Özel Öneriler</h3>
              <p class="text-slate-500 text-sm">Alışveriş alışkanlıklarınıza göre seçildi</p>
            </div>
            <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full">BETA</span>
          </div>
          <AIBuyerInsights />
        </div>

        <!-- Orders Tab -->
        <div v-if="activeTab === 'orders'" class="p-6">
          <div v-if="orders.length === 0" class="text-center py-12">
            <span class="text-6xl">📦</span>
            <p class="text-slate-500 mt-4">Henüz siparişiniz yok.</p>
            <router-link to="/products" aria-label="Alışverişe başla" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-green-500">
              Alışverişe Başla
            </router-link>
          </div>
          <div v-else class="space-y-4">
            <div
              v-for="order in orders"
              :key="order.id"
              class="border border-slate-100 rounded-xl p-4 hover:border-slate-200 transition"
            >
              <div class="flex items-center justify-between mb-4">
                <div>
                  <span class="font-bold text-slate-900">#{{ order.id }}</span>
                  <span class="text-slate-400 mx-2">•</span>
                  <span class="text-slate-500 text-sm">{{ order.date }}</span>
                </div>
                <span :class="getStatusClass(order.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                  {{ getStatusText(order.status) }}
                </span>
              </div>
              <div class="flex items-center gap-4">
                <div class="flex -space-x-2">
                  <img 
                    v-for="(item, i) in order.items.slice(0, 3)" 
                    :key="i"
                    :src="item.image" 
                    class="w-12 h-12 rounded-lg border-2 border-white object-cover"
                  />
                </div>
                <div class="flex-1">
                  <p class="text-sm text-slate-600">{{ order.items.length }} ürün</p>
                  <!-- Order Tracking Stepper -->
                  <div class="mt-3 flex items-center gap-1">
                    <div class="h-1.5 w-8 rounded-full" :class="getOrderStep(order.status) >= 1 ? 'bg-green-500' : 'bg-slate-200'"></div>
                    <div class="h-1.5 w-8 rounded-full" :class="getOrderStep(order.status) >= 2 ? 'bg-green-500' : 'bg-slate-200'"></div>
                    <div class="h-1.5 w-8 rounded-full" :class="getOrderStep(order.status) >= 3 ? 'bg-green-500' : 'bg-slate-200'"></div>
                    <div class="h-1.5 w-8 rounded-full" :class="getOrderStep(order.status) >= 4 ? 'bg-green-500' : 'bg-slate-200'"></div>
                    <span class="ml-2 text-xs font-medium text-slate-500">{{ getStatusLabel(order.status) }}</span>
                  </div>
                </div>
                <div class="text-right">
                  <p class="font-bold text-slate-900">{{ formatCurrency(order.total) }}</p>
                  <button aria-label="Tekrar sipariş ver" class="text-xs text-green-600 hover:underline mt-1">Tekrar Sipariş Ver</button>
                </div>
                <button 
                  @click="viewOrder(order)"
                  aria-label="Sipariş detaylarını göster"
                  class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-green-300"
                >
                  Detay
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Returns Tab -->
        <div v-if="activeTab === 'returns'" class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-lg text-slate-900">İade Taleplerim</h3>
            <router-link to="/returns/new" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
              + Yeni İade Talebi
            </router-link>
          </div>
          <div class="text-center py-12 border border-dashed border-slate-200 rounded-xl">
            <span class="text-6xl">🔄</span>
            <p class="text-slate-500 mt-4">Henüz iade talebiniz yok.</p>
            <router-link to="/returns" class="inline-block mt-4 text-green-600 hover:text-green-700 text-sm font-medium">
              Tüm İadeler →
            </router-link>
          </div>
        </div>

        <!-- Wallet Tab -->
        <div v-if="activeTab === 'wallet'" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
              <p class="text-indigo-100 text-sm font-medium mb-1">Cüzdan Bakiyesi</p>
              <h3 class="text-3xl font-bold">{{ formatCurrency(wallet.balance) }}</h3>
              <div class="mt-6 flex gap-3">
                <button class="flex-1 bg-white/20 hover:bg-white/30 backdrop-blur py-2 rounded-lg text-sm font-semibold transition">
                  + Para Yükle
                </button>
              </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600">⭐</div>
                <p class="text-sm text-slate-500 font-medium">Puan Bakiyesi</p>
              </div>
              <h3 class="text-2xl font-bold text-slate-900">{{ wallet.points }} Puan</h3>
              <p class="text-xs text-slate-500 mt-1">={{ formatCurrency(wallet.points * 0.1) }} değerinde</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">↩️</div>
                <p class="text-sm text-slate-500 font-medium">İade Bakiyesi</p>
              </div>
              <h3 class="text-2xl font-bold text-slate-900">{{ formatCurrency(wallet.refundBalance) }}</h3>
              <p class="text-xs text-slate-500 mt-1">Kullanılabilir</p>
            </div>
          </div>

          <h3 class="text-lg font-bold text-slate-900 mb-4">Cüzdan Hareketleri</h3>
          <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-6 py-4 font-semibold text-slate-700">Tarih</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">İşlem</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">Tutar</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">Durum</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="tx in wallet.transactions" :key="tx.id" class="hover:bg-slate-50 transition">
                  <td class="px-6 py-4 text-slate-500">{{ tx.date }}</td>
                  <td class="px-6 py-4 font-medium text-slate-900">{{ tx.description }}</td>
                  <td class="px-6 py-4 font-bold" :class="tx.type === 'credit' ? 'text-green-600' : 'text-red-600'">
                    {{ tx.type === 'credit' ? '+' : '-' }}{{ formatCurrency(tx.amount) }}
                  </td>
                  <td class="px-6 py-4">
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Tamamlandı</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Favorites Tab -->
        <div v-if="activeTab === 'favorites'" class="p-6">
          <div v-if="favorites.length === 0" class="text-center py-12">
            <span class="text-6xl">❤️</span>
            <p class="text-slate-500 mt-4">Favori ürününüz yok.</p>
          </div>
          <div v-else class="grid md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
              v-for="product in favorites"
              :key="product.id"
              class="border border-slate-100 rounded-xl overflow-hidden hover:shadow-lg transition"
            >
              <img :src="product.image" :alt="product.name" class="w-full h-40 object-cover" />
              <div class="p-4">
                <h3 class="font-medium text-slate-900 truncate">{{ product.name }}</h3>
                <p class="text-lg font-bold text-green-600 mt-1">{{ formatCurrency(product.price) }}</p>
                <div class="flex gap-2 mt-3">
                  <button aria-label="Sepete ekle" class="flex-1 bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-green-500">
                    🛒 Sepete Ekle
                  </button>
                  <button 
                    @click="removeFromFavorites(product.id)"
                    class="p-2 border border-slate-200 rounded-lg hover:bg-red-50 hover:border-red-200"
                  >
                    🗑️
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Tab -->
        <div v-if="activeTab === 'profile'" class="p-6">
          <div class="max-w-xl">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Profil Bilgileri</h3>
            <form @submit.prevent="updateProfile" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Ad Soyad</label>
                <input v-model="user.name" type="text" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500" />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">E-posta</label>
                <input v-model="user.email" type="email" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500" />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Telefon</label>
                <input v-model="user.phone" type="tel" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500" />
              </div>
              <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700">
                Güncelle
              </button>
            </form>
          </div>
        </div>

        <!-- Addresses Tab -->
        <div v-if="activeTab === 'addresses'" class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-900">Adreslerim</h3>
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
              + Yeni Adres
            </button>
          </div>
          <div class="grid md:grid-cols-2 gap-4">
            <div
              v-for="address in addresses"
              :key="address.id"
              class="border border-slate-100 rounded-xl p-4"
            >
              <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-slate-900">{{ address.title }}</span>
                <span v-if="address.isDefault" class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                  Varsayılan
                </span>
              </div>
              <p class="text-slate-600 text-sm mb-4">{{ address.fullAddress }}</p>
              <div class="flex gap-2">
                <button class="text-sm text-blue-600 hover:underline">Düzenle</button>
                <button class="text-sm text-red-600 hover:underline">Sil</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid md:grid-cols-3 gap-6">
        <router-link to="/orders" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition flex items-center gap-4">
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">📦</div>
          <div>
            <p class="font-semibold text-slate-900">Sipariş Takibi</p>
            <p class="text-sm text-slate-500">Kargolarınızı takip edin</p>
          </div>
        </router-link>
        <router-link to="/contact" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition flex items-center gap-4">
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">💬</div>
          <div>
            <p class="font-semibold text-slate-900">Destek</p>
            <p class="text-sm text-slate-500">Yardım & iletişim</p>
          </div>
        </router-link>
        <router-link to="/faq" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition flex items-center gap-4">
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl">❓</div>
          <div>
            <p class="font-semibold text-slate-900">SSS</p>
            <p class="text-sm text-slate-500">Sık sorulan sorular</p>
          </div>
        </router-link>
      </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import StatCard from '@/components/ui/StatCard.vue'
import AIBuyerInsights from '@/components/buyer/AIBuyerInsights.vue'

const router = useRouter()

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { 
    style: 'currency', 
    currency: 'TRY', 
    maximumFractionDigits: 0 
  }).format(value ?? 0)
}

const unreadNotifications = ref(2)
const activeTab = ref('insights')

const tabs = [
  { id: 'insights', name: 'Sizin İçin', icon: '✨' },
  { id: 'orders', name: 'Siparişlerim', icon: '📦' },
  { id: 'returns', name: 'İade Taleplerim', icon: '🔄' },
  { id: 'wallet', name: 'Cüzdanım', icon: '💰' },
  { id: 'favorites', name: 'Favorilerim', icon: '❤️' },
  { id: 'profile', name: 'Profilim', icon: '👤' },
  { id: 'addresses', name: 'Adreslerim', icon: '📍' },
]

const user = ref({
  name: 'Ahmet Yılmaz',
  email: 'ahmet@example.com',
  phone: '0532 XXX XX XX'
})

const stats = ref({
  totalOrders: 15,
  recentOrders: 3,
  totalSpent: 8450,
  favorites: 12,
  points: 850
})

const wallet = ref({
  balance: 1250.00,
  points: 850,
  refundBalance: 450.00,
  transactions: [
    { id: 1, date: '05.12.2025', description: 'Para Yükleme', amount: 1000, type: 'credit' },
    { id: 2, date: '03.12.2025', description: 'Sipariş #1542 Ödemesi', amount: 2197, type: 'debit' },
    { id: 3, date: '01.12.2025', description: 'İade #1530', amount: 450, type: 'credit' },
  ]
})

const orders = ref([
  {
    id: 1542,
    date: '03.12.2025',
    status: 'shipped',
    total: 2197,
    items: [
      { image: 'https://placehold.co/100x100/F97316/FFFFFF?text=Nike' },
      { image: 'https://placehold.co/100x100/3B82F6/FFFFFF?text=Adidas' }
    ]
  },
  {
    id: 1538,
    date: '28.11.2025',
    status: 'delivered',
    total: 1450,
    items: [
      { image: 'https://placehold.co/100x100/22C55E/FFFFFF?text=Puma' }
    ]
  },
  {
    id: 1520,
    date: '15.11.2025',
    status: 'delivered',
    total: 3200,
    items: [
      { image: 'https://placehold.co/100x100/8B5CF6/FFFFFF?text=NB' },
      { image: 'https://placehold.co/100x100/EC4899/FFFFFF?text=Reebok' },
      { image: 'https://placehold.co/100x100/EAB308/FFFFFF?text=Asics' }
    ]
  }
])

const favorites = ref([
  { id: 1, name: 'Nike Air Max 270', price: 2499, image: 'https://placehold.co/200x200/F97316/FFFFFF?text=Nike' },
  { id: 2, name: 'Adidas Ultraboost', price: 2899, image: 'https://placehold.co/200x200/3B82F6/FFFFFF?text=Adidas' },
  { id: 3, name: 'Puma RS-X', price: 1799, image: 'https://placehold.co/200x200/22C55E/FFFFFF?text=Puma' },
  { id: 4, name: 'New Balance 574', price: 1599, image: 'https://placehold.co/200x200/8B5CF6/FFFFFF?text=NB' },
])

const addresses = ref([
  { id: 1, title: 'Ev', fullAddress: 'Atatürk Cad. No:123, Kadıköy, İstanbul', isDefault: true },
  { id: 2, title: 'İş', fullAddress: 'Levent Mah. Plaza Sok. No:5, Beşiktaş, İstanbul', isDefault: false }
])

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

const getStatusLabel = (status: string) => {
  const texts: Record<string, string> = {
    'pending': 'Bekliyor',
    'processing': 'Hazırlanıyor',
    'shipped': 'Kargoda',
    'delivered': 'Teslim Edildi',
    'cancelled': 'İptal'
  }
  return texts[status] || status
}

// Alias for template compatibility
const getStatusText = getStatusLabel

const getOrderStep = (status: string) => {
  const steps: Record<string, number> = {
    'pending': 1,
    'processing': 2,
    'shipped': 3,
    'delivered': 4,
    'cancelled': 0
  }
  return steps[status] || 1
}

const viewOrder = (order: any) => {
  router.push(`/orders/${order.id}`)
}

const removeFromFavorites = (id: number) => {
  favorites.value = favorites.value.filter(f => f.id !== id)
}

const updateProfile = () => {
  alert('Profil güncellendi!')
}

const handleLogout = () => {
  router.push('/login')
}
</script>
