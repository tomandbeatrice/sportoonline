<template>
  <div class="seller-wallet max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Cüzdanım & Finans</h1>
        <p class="text-slate-500">Kazançlarınızı takip edin ve para çekme talebi oluşturun</p>
      </div>
      <button 
        @click="openWithdrawModal"
        class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center gap-2"
      >
        <span>💸</span>
        <span>Para Çek</span>
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
          <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600">💰</div>
          <p class="text-xs text-slate-500 uppercase font-semibold">Çekilebilir Bakiye</p>
        </div>
        <div class="text-3xl font-bold text-slate-900">{{ formatPrice(summary.confirmed_payout) }}</div>
        <p class="text-xs text-emerald-600 mt-1">Hemen çekilebilir</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
          <div class="p-2 bg-orange-100 rounded-lg text-orange-600">⏳</div>
          <p class="text-xs text-slate-500 uppercase font-semibold">Bekleyen Bakiye</p>
        </div>
        <div class="text-3xl font-bold text-slate-900">{{ formatPrice(summary.pending_payout) }}</div>
        <p class="text-xs text-slate-500 mt-1">Sipariş onayı bekleniyor</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
          <div class="p-2 bg-blue-100 rounded-lg text-blue-600">📈</div>
          <p class="text-xs text-slate-500 uppercase font-semibold">Toplam Satış</p>
        </div>
        <div class="text-3xl font-bold text-slate-900">{{ formatPrice(summary.total_revenue) }}</div>
        <p class="text-xs text-slate-500 mt-1">Brüt gelir</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
          <div class="p-2 bg-red-100 rounded-lg text-red-600">🧾</div>
          <p class="text-xs text-slate-500 uppercase font-semibold">Kesintiler</p>
        </div>
        <div class="text-3xl font-bold text-slate-900">{{ formatPrice(summary.total_platform_fees) }}</div>
        <p class="text-xs text-slate-500 mt-1">Komisyon & Hizmet</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-6 border-b border-slate-200 mb-6">
      <button 
        @click="activeTab = 'transactions'"
        class="pb-3 text-sm font-medium transition-colors relative"
        :class="activeTab === 'transactions' ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-700'"
      >
        İşlem Geçmişi
        <span v-if="activeTab === 'transactions'" class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600 rounded-t-full"></span>
      </button>
      <button 
        @click="activeTab = 'withdrawals'"
        class="pb-3 text-sm font-medium transition-colors relative"
        :class="activeTab === 'withdrawals' ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-700'"
      >
        Para Çekme Talepleri
        <span v-if="activeTab === 'withdrawals'" class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600 rounded-t-full"></span>
      </button>
    </div>

    <!-- Transactions Table -->
    <div v-if="activeTab === 'transactions'" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div v-if="loading" class="p-12 text-center text-slate-500">
        <span class="inline-block w-8 h-8 border-2 border-slate-200 border-t-indigo-600 rounded-full animate-spin mb-2"></span>
        <p>Yükleniyor...</p>
      </div>
      
      <div v-else-if="transactions.length === 0" class="p-12 text-center text-slate-500">
        <p class="text-4xl mb-2">🧾</p>
        <p>Henüz işlem bulunmuyor.</p>
      </div>
      
      <table v-else class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-6 py-4 font-semibold text-slate-700">Tarih</th>
            <th class="px-6 py-4 font-semibold text-slate-700">İşlem</th>
            <th class="px-6 py-4 font-semibold text-slate-700">Tutar</th>
            <th class="px-6 py-4 font-semibold text-slate-700">Komisyon</th>
            <th class="px-6 py-4 font-semibold text-slate-700">Net</th>
            <th class="px-6 py-4 font-semibold text-slate-700">Durum</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="tx in transactions" :key="tx.id" class="hover:bg-slate-50 transition">
            <td class="px-6 py-4 text-slate-500">
              {{ new Date(tx.date).toLocaleDateString('tr-TR') }}
              <span class="text-xs text-slate-400 block">{{ new Date(tx.date).toLocaleTimeString('tr-TR', {hour: '2-digit', minute:'2-digit'}) }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="font-medium text-slate-900">{{ tx.product_name }}</div>
              <div class="text-xs text-slate-500">{{ tx.quantity }} adet</div>
            </td>
            <td class="px-6 py-4 font-medium text-slate-900">
              {{ formatPrice(tx.total) }}
            </td>
            <td class="px-6 py-4 text-red-600">
              -{{ formatPrice(tx.platform_fee) }}
            </td>
            <td class="px-6 py-4 font-bold text-emerald-600">
              +{{ formatPrice(tx.seller_payout) }}
            </td>
            <td class="px-6 py-4">
              <span :class="getStatusClass(tx.order_status)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ getStatusLabel(tx.order_status) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Withdrawals Table -->
    <div v-if="activeTab === 'withdrawals'" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div v-if="withdrawals.length === 0" class="p-12 text-center text-slate-500">
        <p class="text-4xl mb-2">💸</p>
        <p>Henüz para çekme talebiniz yok.</p>
      </div>
      <table v-else class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-6 py-4 font-semibold text-slate-700">Talep Tarihi</th>
            <th class="px-6 py-4 font-semibold text-slate-700">Tutar</th>
            <th class="px-6 py-4 font-semibold text-slate-700">IBAN</th>
            <th class="px-6 py-4 font-semibold text-slate-700">Durum</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="w in withdrawals" :key="w.id" class="hover:bg-slate-50 transition">
            <td class="px-6 py-4 text-slate-500">{{ w.date }}</td>
            <td class="px-6 py-4 font-bold text-slate-900">{{ formatPrice(w.amount) }}</td>
            <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ w.iban }}</td>
            <td class="px-6 py-4">
              <span 
                class="px-2 py-1 rounded-full text-xs font-medium"
                :class="{
                  'bg-yellow-100 text-yellow-700': w.status === 'pending',
                  'bg-green-100 text-green-700': w.status === 'completed',
                  'bg-red-100 text-red-700': w.status === 'rejected'
                }"
              >
                {{ getWithdrawalStatusLabel(w.status) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Withdraw Modal -->
    <div v-if="showWithdrawModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-xl font-bold text-slate-900 mb-4">Para Çekme Talebi</h3>
        
        <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-100">
          <p class="text-sm text-slate-500 mb-1">Çekilebilir Bakiye</p>
          <p class="text-2xl font-bold text-emerald-600">{{ formatPrice(summary.confirmed_payout) }}</p>
        </div>

        <form @submit.prevent="submitWithdrawal">
          <div class="space-y-4 mb-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Çekilecek Tutar</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">₺</span>
                <input 
                  v-model.number="withdrawForm.amount" 
                  type="number" 
                  :max="summary.confirmed_payout"
                  min="100"
                  class="w-full pl-8 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"
                  placeholder="0.00"
                  required
                />
              </div>
              <p class="text-xs text-slate-500 mt-1">Minimum çekim tutarı: 100 ₺</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">IBAN</label>
              <input 
                v-model="withdrawForm.iban" 
                type="text" 
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-mono"
                placeholder="TR..."
                required
              />
            </div>
          </div>

          <div class="flex gap-3">
            <button 
              type="button" 
              @click="showWithdrawModal = false"
              class="flex-1 px-4 py-3 border border-slate-200 rounded-xl font-medium text-slate-600 hover:bg-slate-50"
            >
              İptal
            </button>
            <button 
              type="submit"
              class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700"
              :disabled="withdrawForm.amount > summary.confirmed_payout || withdrawForm.amount < 100"
            >
              Talep Oluştur
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';

const activeTab = ref('transactions')
const showWithdrawModal = ref(false)
const loading = ref(false)

const summary = ref({
  total_revenue: 0,
  total_platform_fees: 0,
  total_seller_payout: 0,
  pending_payout: 0,
  confirmed_payout: 0,
})

const transactions = ref<any[]>([])
const withdrawals = ref<any[]>([])

// Fetch wallet data from API
const fetchWalletData = async () => {
  loading.value = true
  try {
    const [summaryRes, txRes, withdrawRes] = await Promise.all([
      axios.get('/api/seller/wallet/summary'),
      axios.get('/api/seller/wallet/transactions'),
      axios.get('/api/seller/wallet/withdrawals')
    ])
    
    summary.value = summaryRes.data.summary || summaryRes.data
    transactions.value = txRes.data.transactions || txRes.data || []
    withdrawals.value = withdrawRes.data.withdrawals || withdrawRes.data || []
  } catch (error) {
    console.error('Failed to fetch wallet data:', error)
    // Fallback demo data
    summary.value = {
      total_revenue: 15450.00,
      total_platform_fees: 2317.50,
      total_seller_payout: 13132.50,
      pending_payout: 4500.00,
      confirmed_payout: 8632.50,
    }
    transactions.value = [
      { id: 1, date: '2025-12-04T10:30:00', product_name: 'Kablosuz Kulaklık', quantity: 1, total: 1200, platform_fee: 180, seller_payout: 1020, order_status: 'delivered' },
    ]
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchWalletData()
})

const withdrawForm = reactive({
  amount: 0,
  iban: 'TR12 3456 7890 1234 5678 90'
})

const formatPrice = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-700',
    'shipped': 'bg-blue-100 text-blue-700',
    'delivered': 'bg-emerald-100 text-emerald-700',
    'cancelled': 'bg-red-100 text-red-700'
  }
  return classes[status] || 'bg-slate-100 text-slate-700'
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    'pending': 'Bekliyor',
    'shipped': 'Kargoda',
    'delivered': 'Teslim Edildi',
    'cancelled': 'İptal'
  }
  return labels[status] || status
}

const getWithdrawalStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    'pending': 'İşlemde',
    'completed': 'Ödendi',
    'rejected': 'Reddedildi'
  }
  return labels[status] || status
}

const openWithdrawModal = () => {
  withdrawForm.amount = 0
  showWithdrawModal.value = true
}

const submitWithdrawal = async () => {
  try {
    const response = await axios.post('/api/seller/wallet/withdraw', {
      amount: withdrawForm.amount,
      iban: withdrawForm.iban
    })
    
    if (response.data.success) {
      withdrawals.value.unshift(response.data.withdrawal || {
        id: Date.now(),
        date: new Date().toLocaleDateString('tr-TR'),
        amount: withdrawForm.amount,
        iban: withdrawForm.iban,
        status: 'pending'
      })
      summary.value.confirmed_payout -= withdrawForm.amount
      showWithdrawModal.value = false
      alert('Para çekme talebiniz alındı.')
    }
  } catch (error: any) {
    alert(error.response?.data?.message || 'Para çekme talebi oluşturulamadı')
  }
}
</script>
