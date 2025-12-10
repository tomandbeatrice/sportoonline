<template>
  <div class="min-h-screen bg-slate-50 pb-20">
    <!-- Header -->
    <div class="bg-white border-b border-slate-200 px-4 py-4 sticky top-0 z-10">
      <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <button @click="$router.back()" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
              <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <h1 class="text-lg font-bold text-slate-900">İade Taleplerim</h1>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border-b border-slate-200 px-4 py-3">
      <div class="max-w-4xl mx-auto">
        <div class="flex gap-2 overflow-x-auto scrollbar-hide">
          <button 
            v-for="status in statusFilters" 
            :key="status.value"
            @click="currentStatus = status.value"
            class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors"
            :class="currentStatus === status.value 
              ? 'bg-indigo-600 text-white' 
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
          >
            {{ status.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="max-w-4xl mx-auto px-4 py-8">
      <div class="space-y-4">
        <div v-for="i in 3" :key="i" class="animate-pulse bg-white rounded-xl border border-slate-200 p-4">
          <div class="flex gap-4">
            <div class="w-20 h-20 bg-slate-200 rounded-lg"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-slate-200 rounded w-3/4"></div>
              <div class="h-3 bg-slate-200 rounded w-1/2"></div>
              <div class="h-3 bg-slate-200 rounded w-1/4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="returns.length === 0" class="max-w-4xl mx-auto px-4 py-16 text-center">
      <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-12 h-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
        </svg>
      </div>
      <h2 class="text-xl font-bold text-slate-900 mb-2">Henüz İade Talebi Yok</h2>
      <p class="text-slate-500 mb-6">Siparişlerinizden iade talebi oluşturabilirsiniz.</p>
      <button @click="$router.push('/orders')" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
        Siparişlerime Git
      </button>
    </div>

    <!-- Returns List -->
    <div v-else class="max-w-4xl mx-auto px-4 py-6 space-y-4">
      <router-link 
        v-for="returnItem in returns" 
        :key="returnItem.id"
        :to="`/returns/${returnItem.id}`"
        class="block bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-md transition-all"
      >
        <!-- Status Bar -->
        <div class="px-4 py-2 border-b border-slate-100" :class="getStatusBgClass(returnItem.status)">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium" :class="getStatusTextClass(returnItem.status)">
              {{ returnItem.status_label }}
            </span>
            <span class="text-xs text-slate-500">{{ formatDate(returnItem.created_at) }}</span>
          </div>
        </div>
        
        <!-- Content -->
        <div class="p-4">
          <div class="flex gap-4">
            <!-- Product Image -->
            <div class="w-20 h-20 bg-slate-100 rounded-lg overflow-hidden flex-shrink-0">
              <img 
                v-if="returnItem.order_item?.product?.image_url" 
                :src="returnItem.order_item.product.image_url" 
                :alt="returnItem.order_item.product.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-3xl">📦</div>
            </div>
            
            <!-- Info -->
            <div class="flex-1 min-w-0">
              <h3 class="font-semibold text-slate-900 truncate">
                {{ returnItem.order_item?.product?.name || 'Tüm Sipariş İadesi' }}
              </h3>
              <p class="text-sm text-slate-500 mt-1">
                İade No: {{ returnItem.return_number }}
              </p>
              <p class="text-sm text-slate-500">
                Sebep: {{ returnItem.reason_category_label }}
              </p>
              
              <div class="flex items-center justify-between mt-3">
                <span class="text-lg font-bold text-indigo-600">{{ returnItem.refund_amount }} TL</span>
                <span 
                  class="px-2 py-1 text-xs font-medium rounded-full"
                  :class="getRefundStatusClass(returnItem.refund_status)"
                >
                  {{ returnItem.refund_status_label }}
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Action Hint -->
        <div v-if="returnItem.status === 'approved'" class="px-4 py-2 bg-amber-50 border-t border-amber-100">
          <p class="text-xs text-amber-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Ürünü kargo ile göndermeniz bekleniyor
          </p>
        </div>
      </router-link>

      <!-- Pagination -->
      <div v-if="pagination.lastPage > 1" class="flex justify-center gap-2 mt-8">
        <button 
          v-for="page in pagination.lastPage" 
          :key="page"
          @click="fetchReturns(page)"
          class="w-10 h-10 rounded-lg border transition-colors"
          :class="page === pagination.currentPage 
            ? 'bg-indigo-600 text-white border-indigo-600' 
            : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useToast } from 'vue-toastification'
import api from '@/services/api'

const toast = useToast()

const loading = ref(true)
const returns = ref<any[]>([])
const currentStatus = ref('')

const pagination = ref({
  currentPage: 1,
  lastPage: 1,
  total: 0,
})

const statusFilters = [
  { value: '', label: 'Tümü' },
  { value: 'pending', label: 'Beklemede' },
  { value: 'approved', label: 'Onaylandı' },
  { value: 'shipped', label: 'Gönderildi' },
  { value: 'completed', label: 'Tamamlandı' },
  { value: 'rejected', label: 'Reddedildi' },
]

const fetchReturns = async (page = 1) => {
  loading.value = true
  try {
    const params: any = { page, per_page: 10 }
    if (currentStatus.value) {
      params.status = currentStatus.value
    }
    
    const { data } = await api.get('/returns', { params })
    
    returns.value = data.data
    pagination.value = {
      currentPage: data.current_page,
      lastPage: data.last_page,
      total: data.total,
    }
  } catch (error) {
    console.error('Error fetching returns:', error)
    toast.error('İade talepleri yüklenemedi')
  } finally {
    loading.value = false
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

const getStatusBgClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-amber-50',
    approved: 'bg-blue-50',
    shipped: 'bg-purple-50',
    received: 'bg-indigo-50',
    inspecting: 'bg-cyan-50',
    refunded: 'bg-emerald-50',
    completed: 'bg-green-50',
    rejected: 'bg-red-50',
    cancelled: 'bg-slate-50',
  }
  return classes[status] || 'bg-slate-50'
}

const getStatusTextClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'text-amber-700',
    approved: 'text-blue-700',
    shipped: 'text-purple-700',
    received: 'text-indigo-700',
    inspecting: 'text-cyan-700',
    refunded: 'text-emerald-700',
    completed: 'text-green-700',
    rejected: 'text-red-700',
    cancelled: 'text-slate-700',
  }
  return classes[status] || 'text-slate-700'
}

const getRefundStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-slate-100 text-slate-700',
    processing: 'bg-blue-100 text-blue-700',
    completed: 'bg-green-100 text-green-700',
    failed: 'bg-red-100 text-red-700',
  }
  return classes[status] || 'bg-slate-100 text-slate-700'
}

watch(currentStatus, () => {
  fetchReturns(1)
})

onMounted(() => {
  fetchReturns()
})
</script>
