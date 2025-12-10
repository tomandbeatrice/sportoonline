<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import FinanceAIInsight from '@/components/admin/finance/FinanceAIInsight.vue'
import logger from '@/utils/logger'

interface Transaction {
  id: number
  type: string
  user?: { name: string; email?: string }
  amount: number
  created_at: string
  status: string
  method?: string
  description?: string
  order_id?: number
}

interface Stats {
  total_revenue: number
  pending_withdrawals: number
  today_transactions: number
  monthly_commission: number
}

// State
const transactions = ref<Transaction[]>([])
const stats = ref<Stats>({
  total_revenue: 0,
  pending_withdrawals: 0,
  today_transactions: 0,
  monthly_commission: 0
})
const loading = ref(false)
const selectedTransaction = ref<Transaction | null>(null)
const searchQuery = ref('')
const activeTab = ref('all') // all, pending, completed
const rejectModal = ref(false)
const rejectReason = ref('')
const rejectingId = ref<number | null>(null)

// Fetch transactions from API
const fetchTransactions = async () => {
  loading.value = true
  try {
    const params: Record<string, string> = {}
    if (activeTab.value !== 'all') {
      params.status = activeTab.value
    }
    if (searchQuery.value) {
      params.search = searchQuery.value
    }
    
    const response = await axios.get('/api/admin/payment-logs', { params })
    transactions.value = response.data.transactions?.data || []
    stats.value = response.data.stats || stats.value
    
    if (transactions.value.length > 0 && !selectedTransaction.value) {
      selectedTransaction.value = transactions.value[0]
    }
  } catch (error) {
    logger.error('Failed to fetch transactions:', error)
  } finally {
    loading.value = false
  }
}

// Watch for tab/search changes
watch([activeTab, searchQuery], () => {
  fetchTransactions()
}, { debounce: 300 } as any)

const filteredTransactions = computed(() => {
  return transactions.value
})

const selectTransaction = (transaction: Transaction) => {
  selectedTransaction.value = transaction
}

// Approve payment
const approvePayment = async (id: number) => {
  try {
    await axios.post(`/api/admin/payment-logs/${id}/approve`)
    await fetchTransactions()
  } catch (error) {
    logger.error('Failed to approve payment:', error)
  }
}

// Open reject modal
const openRejectModal = (id: number) => {
  rejectingId.value = id
  rejectReason.value = ''
  rejectModal.value = true
}

// Reject payment
const rejectPayment = async () => {
  if (!rejectingId.value || !rejectReason.value) return
  
  try {
    await axios.post(`/api/admin/payment-logs/${rejectingId.value}/reject`, {
      reason: rejectReason.value
    })
    rejectModal.value = false
    await fetchTransactions()
  } catch (error) {
    logger.error('Failed to reject payment:', error)
  }
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value || 0)
}

const formatDate = (dateStr: string) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusBadge = (status: string) => {
  switch (status) {
    case 'completed': return 'bg-emerald-100 text-emerald-700'
    case 'pending': return 'bg-orange-100 text-orange-700'
    case 'rejected': return 'bg-red-100 text-red-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const getTypeIcon = (type: string) => {
  switch (type) {
    case 'withdrawal': return '💸'
    case 'deposit': return '💰'
    case 'commission': return '🧾'
    default: return '📄'
  }
}

onMounted(() => {
  fetchTransactions()
})
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          💰 Finans Yönetimi
          <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">AI Audit</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Nakit akışı, hakedişler ve risk analizi</p>
      </div>
      <div class="flex gap-3">
        <button class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition flex items-center gap-2">
          <span>📊</span> Raporlar
        </button>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center gap-2">
          <span>➕</span> Manuel İşlem
        </button>
      </div>
    </header>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4 px-6 py-4 bg-white border-b border-slate-200 shrink-0">
      <div class="bg-emerald-50 rounded-xl p-4">
        <div class="text-xs text-emerald-600 font-medium">Toplam Gelir</div>
        <div class="text-xl font-bold text-emerald-700">{{ formatCurrency(stats.total_revenue) }}</div>
      </div>
      <div class="bg-orange-50 rounded-xl p-4">
        <div class="text-xs text-orange-600 font-medium">Bekleyen Ödemeler</div>
        <div class="text-xl font-bold text-orange-700">{{ formatCurrency(stats.pending_withdrawals) }}</div>
      </div>
      <div class="bg-indigo-50 rounded-xl p-4">
        <div class="text-xs text-indigo-600 font-medium">Bugünkü İşlem</div>
        <div class="text-xl font-bold text-indigo-700">{{ stats.today_transactions }}</div>
      </div>
      <div class="bg-purple-50 rounded-xl p-4">
        <div class="text-xs text-purple-600 font-medium">Aylık Komisyon</div>
        <div class="text-xl font-bold text-purple-700">{{ formatCurrency(stats.monthly_commission) }}</div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
      <!-- Left Panel: List -->
      <div class="flex-1 flex flex-col min-w-0 border-r border-slate-200 bg-white">
        
        <!-- Tabs -->
        <div class="flex border-b border-slate-200">
          <button 
            @click="activeTab = 'all'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            Tüm İşlemler
          </button>
          <button 
            @click="activeTab = 'pending'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'pending' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            ⏳ Bekleyenler
          </button>
          <button 
            @click="activeTab = 'completed'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'completed' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            ✅ Tamamlananlar
          </button>
        </div>

        <!-- Tab Content -->
        <div class="flex-1 overflow-y-auto p-4">
          
          <!-- Search & Filters -->
          <div class="flex gap-2 mb-4">
            <div class="relative flex-1">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="İşlem no, kullanıcı veya açıklama ara..." 
                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
              >
            </div>
            <button class="px-3 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
              🌪️ Filtrele
            </button>
          </div>

          <!-- Transaction List -->
          <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
          </div>
          
          <div v-else-if="filteredTransactions.length === 0" class="text-center py-12">
            <div class="text-4xl mb-4">💳</div>
            <p class="text-slate-500">İşlem bulunamadı</p>
          </div>

          <div v-else class="space-y-2">
            <div 
              v-for="transaction in filteredTransactions" 
              :key="transaction.id"
              @click="selectTransaction(transaction)"
              class="p-3 rounded-xl border transition cursor-pointer group relative overflow-hidden"
              :class="selectedTransaction?.id === transaction.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-200 hover:shadow-md'"
            >
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-xl">
                  {{ getTypeIcon(transaction.type) }}
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex justify-between items-start">
                    <h3 class="font-bold text-slate-900 truncate">#{{ transaction.id }} - {{ transaction.user?.name || 'Sistem' }}</h3>
                    <span class="font-bold text-slate-900">{{ formatCurrency(transaction.amount) }}</span>
                  </div>
                  <p class="text-sm text-slate-500 truncate">{{ transaction.description || `Sipariş #${transaction.order_id}` }}</p>
                  <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                    <span>📅 {{ formatDate(transaction.created_at) }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span>{{ transaction.method || 'Otomatik' }}</span>
                  </div>
                </div>
                <div class="text-right space-y-1">
                  <span class="block px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide" :class="getStatusBadge(transaction.status)">
                    {{ transaction.status === 'pending' ? 'Bekliyor' : (transaction.status === 'completed' ? 'Tamamlandı' : 'Reddedildi') }}
                  </span>
                  <div v-if="transaction.status === 'pending'" class="flex gap-1">
                    <button @click.stop="approvePayment(transaction.id)" class="px-2 py-0.5 bg-emerald-500 text-white text-[10px] rounded hover:bg-emerald-600">✓</button>
                    <button @click.stop="openRejectModal(transaction.id)" class="px-2 py-0.5 bg-red-500 text-white text-[10px] rounded hover:bg-red-600">✕</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Right Panel: Detail & AI Insights -->
      <div class="w-96 bg-slate-50 border-l border-slate-200 flex flex-col overflow-hidden" v-if="selectedTransaction">
        <div class="p-6 overflow-y-auto">
          
          <!-- Transaction Header -->
          <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center text-3xl mx-auto mb-3">
              {{ getTypeIcon(selectedTransaction.type) }}
            </div>
            <h2 class="text-2xl font-bold text-slate-900">{{ formatCurrency(selectedTransaction.amount) }}</h2>
            <p class="text-slate-500 text-sm font-medium">{{ selectedTransaction.description }}</p>
            <p class="text-slate-400 text-xs mt-1">İşlem ID: #{{ selectedTransaction.id }}</p>
          </div>

          <!-- AI Insights Component -->
          <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
              <span>🧠</span> AI Denetimi
            </h3>
            <FinanceAIInsight 
              :transaction-id="selectedTransaction.id"
              :amount="selectedTransaction.amount"
              :user="selectedTransaction.user"
              :type="selectedTransaction.type"
            />
          </div>

          <!-- Transaction Details -->
          <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-3 border-b border-slate-100 bg-slate-50">
              <h4 class="font-bold text-slate-800 text-sm">📋 İşlem Detayları</h4>
            </div>
            <div class="p-3 space-y-3">
              <div class="flex justify-between text-sm">
                <span class="text-slate-500">Tarih</span>
                <span class="font-medium text-slate-900">{{ selectedTransaction.date }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-slate-500">Yöntem</span>
                <span class="font-medium text-slate-900">{{ selectedTransaction.method }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-slate-500">Kullanıcı</span>
                <span class="font-medium text-slate-900">{{ selectedTransaction.user }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-slate-500">Durum</span>
                <span class="font-medium capitalize" :class="selectedTransaction.status === 'completed' ? 'text-emerald-600' : 'text-orange-600'">
                  {{ selectedTransaction.status }}
                </span>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="grid grid-cols-2 gap-3" v-if="selectedTransaction.status === 'pending'">
            <button class="py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg shadow-emerald-200 transition">
              Onayla
            </button>
            <button class="py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-200 transition">
              Reddet
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-xl font-bold text-slate-900 mb-4">Ödemeyi Reddet</h3>
        <textarea 
          v-model="rejectReason"
          placeholder="Red sebebini yazın..."
          rows="4"
          class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-200 focus:border-red-500 resize-none"
        ></textarea>
        <div class="flex gap-3 mt-4">
          <button 
            @click="rejectModal = false"
            class="flex-1 py-2 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition"
          >
            İptal
          </button>
          <button 
            @click="rejectPayment"
            :disabled="!rejectReason"
            class="flex-1 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition disabled:opacity-50"
          >
            Reddet
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}
::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
