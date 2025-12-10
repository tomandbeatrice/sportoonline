<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import logger from '@/utils/logger'

// State
const plans = ref<any[]>([])
const commissions = ref<any[]>([])
const summary = ref<any>({})
const loading = ref(false)
const activeTab = ref('plans') // plans, commissions
const selectedMonth = ref(new Date().toISOString().slice(0, 7)) // YYYY-MM
const editingPlan = ref<any>(null)
const showEditModal = ref(false)

// Fetch subscription plans
const fetchPlans = async () => {
  try {
    const response = await axios.get('/api/subscriptions/plans')
    plans.value = response.data
  } catch (error) {
    logger.error('Failed to fetch plans:', error)
  }
}

// Fetch commission data
const fetchCommissions = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/admin/commissions', {
      params: { month: selectedMonth.value }
    })
    commissions.value = response.data.commissions?.data || []
    summary.value = response.data.summary || {}
  } catch (error) {
    logger.error('Failed to fetch commissions:', error)
  } finally {
    loading.value = false
  }
}

// Edit plan
const openEditModal = (plan: any) => {
  editingPlan.value = { ...plan }
  showEditModal.value = true
}

const savePlan = async () => {
  if (!editingPlan.value) return
  
  try {
    await axios.put(`/api/admin/subscription-plans/${editingPlan.value.id}`, editingPlan.value)
    await fetchPlans()
    showEditModal.value = false
    editingPlan.value = null
  } catch (error) {
    logger.error('Failed to update plan:', error)
  }
}

// Mark commission as paid
const markAsPaid = async (commissionId: number) => {
  try {
    await axios.post(`/api/admin/commissions/${commissionId}/mark-paid`)
    await fetchCommissions()
  } catch (error) {
    logger.error('Failed to mark as paid:', error)
  }
}

// Format helpers
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value || 0)
}

const formatPercent = (value: number) => {
  return `%${value || 0}`
}

onMounted(() => {
  fetchPlans()
  fetchCommissions()
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 p-6">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
        💎 Komisyon & Abonelik Yönetimi
      </h1>
      <p class="text-slate-500 mt-1">Abonelik planlarını düzenleyin ve satıcı komisyonlarını yönetin</p>
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 mb-6">
      <button 
        @click="activeTab = 'plans'"
        class="px-4 py-2 rounded-lg font-medium transition"
        :class="activeTab === 'plans' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100'"
      >
        📋 Abonelik Planları
      </button>
      <button 
        @click="activeTab = 'commissions'"
        class="px-4 py-2 rounded-lg font-medium transition"
        :class="activeTab === 'commissions' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100'"
      >
        💰 Komisyon Takibi
      </button>
    </div>

    <!-- Plans Tab -->
    <div v-if="activeTab === 'plans'" class="space-y-6">
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div 
          v-for="plan in plans" 
          :key="plan.id"
          class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition"
        >
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900">{{ plan.name }}</h3>
            <span 
              class="px-2 py-1 rounded-full text-xs font-bold"
              :class="plan.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
            >
              {{ plan.is_active ? 'Aktif' : 'Pasif' }}
            </span>
          </div>
          
          <div class="space-y-3 mb-6">
            <div class="flex justify-between text-sm">
              <span class="text-slate-500">Aylık Fiyat</span>
              <span class="font-bold text-slate-900">{{ formatCurrency(plan.price) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-slate-500">Yıllık Fiyat</span>
              <span class="font-bold text-slate-900">{{ formatCurrency(plan.yearly_price) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-slate-500">Komisyon Oranı</span>
              <span class="font-bold text-indigo-600">{{ formatPercent(plan.commission_rate) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-slate-500">Ürün Limiti</span>
              <span class="font-bold text-slate-900">{{ plan.product_limit || 'Sınırsız' }}</span>
            </div>
          </div>

          <button 
            @click="openEditModal(plan)"
            class="w-full py-2 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition"
          >
            ✏️ Düzenle
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="plans.length === 0" class="text-center py-12 bg-white rounded-2xl border border-slate-200">
        <div class="text-4xl mb-4">📋</div>
        <h3 class="text-lg font-bold text-slate-900">Plan Bulunamadı</h3>
        <p class="text-slate-500">Henüz abonelik planı tanımlanmamış.</p>
      </div>
    </div>

    <!-- Commissions Tab -->
    <div v-if="activeTab === 'commissions'" class="space-y-6">
      <!-- Month Selector & Summary -->
      <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
          <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-slate-700">Dönem:</label>
            <input 
              type="month" 
              v-model="selectedMonth"
              @change="fetchCommissions"
              class="px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
            >
          </div>
          <button 
            @click="fetchCommissions"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition"
          >
            🔄 Yenile
          </button>
        </div>

        <!-- Summary Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div class="bg-slate-50 rounded-xl p-4">
            <div class="text-sm text-slate-500">Toplam Satış</div>
            <div class="text-2xl font-bold text-slate-900">{{ formatCurrency(summary.total_sales) }}</div>
          </div>
          <div class="bg-emerald-50 rounded-xl p-4">
            <div class="text-sm text-emerald-600">Toplam Komisyon</div>
            <div class="text-2xl font-bold text-emerald-700">{{ formatCurrency(summary.total_commission) }}</div>
          </div>
          <div class="bg-indigo-50 rounded-xl p-4">
            <div class="text-sm text-indigo-600">Abonelik Gelirleri</div>
            <div class="text-2xl font-bold text-indigo-700">{{ formatCurrency(summary.total_subscription_fees) }}</div>
          </div>
          <div class="bg-amber-50 rounded-xl p-4">
            <div class="text-sm text-amber-600">Aktif Satıcı</div>
            <div class="text-2xl font-bold text-amber-700">{{ summary.seller_count || 0 }}</div>
          </div>
        </div>
      </div>

      <!-- Commissions Table -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
          <h3 class="font-bold text-slate-900">Satıcı Komisyonları</h3>
        </div>
        
        <div v-if="loading" class="flex justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>

        <table v-else-if="commissions.length > 0" class="w-full">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Satıcı</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Plan</th>
              <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Satış</th>
              <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Komisyon</th>
              <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Net</th>
              <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Durum</th>
              <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">İşlem</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="comm in commissions" :key="comm.id" class="hover:bg-slate-50">
              <td class="px-6 py-4">
                <div class="font-medium text-slate-900">{{ comm.user?.name }}</div>
                <div class="text-xs text-slate-500">{{ comm.user?.email }}</div>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                  {{ comm.subscription?.plan?.name || 'Ücretsiz' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right font-medium text-slate-900">{{ formatCurrency(comm.total_sales) }}</td>
              <td class="px-6 py-4 text-right font-medium text-emerald-600">{{ formatCurrency(comm.commission_amount) }}</td>
              <td class="px-6 py-4 text-right font-bold text-slate-900">{{ formatCurrency(comm.net_commission) }}</td>
              <td class="px-6 py-4 text-center">
                <span 
                  class="px-2 py-1 rounded-full text-xs font-bold"
                  :class="comm.status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                >
                  {{ comm.status === 'paid' ? 'Ödendi' : 'Bekliyor' }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <button 
                  v-if="comm.status !== 'paid'"
                  @click="markAsPaid(comm.id)"
                  class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700 transition"
                >
                  ✓ Ödendi İşaretle
                </button>
                <span v-else class="text-slate-400 text-xs">-</span>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-else class="text-center py-12">
          <div class="text-4xl mb-4">💰</div>
          <h3 class="text-lg font-bold text-slate-900">Komisyon Kaydı Yok</h3>
          <p class="text-slate-500">Bu dönem için komisyon kaydı bulunamadı.</p>
        </div>
      </div>
    </div>

    <!-- Edit Plan Modal -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-bold text-slate-900">Plan Düzenle</h3>
          <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div v-if="editingPlan" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Plan Adı</label>
            <input 
              v-model="editingPlan.name"
              type="text" 
              class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
            >
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Aylık Fiyat (₺)</label>
              <input 
                v-model.number="editingPlan.price"
                type="number" 
                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Yıllık Fiyat (₺)</label>
              <input 
                v-model.number="editingPlan.yearly_price"
                type="number" 
                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
              >
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Komisyon Oranı (%)</label>
              <input 
                v-model.number="editingPlan.commission_rate"
                type="number" 
                min="0" 
                max="100"
                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Ürün Limiti</label>
              <input 
                v-model.number="editingPlan.product_limit"
                type="number" 
                min="0"
                placeholder="0 = Sınırsız"
                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
              >
            </div>
          </div>

          <div class="flex items-center gap-2">
            <input 
              v-model="editingPlan.is_active"
              type="checkbox" 
              id="is_active"
              class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
            >
            <label for="is_active" class="text-sm text-slate-700">Plan Aktif</label>
          </div>

          <div class="flex gap-3 pt-4">
            <button 
              @click="showEditModal = false"
              class="flex-1 py-2 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition"
            >
              İptal
            </button>
            <button 
              @click="savePlan"
              class="flex-1 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition"
            >
              Kaydet
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
