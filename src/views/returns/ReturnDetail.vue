<template>
  <div class="min-h-screen bg-slate-50 pb-20">
    <!-- Header -->
    <div class="bg-white border-b border-slate-200 px-4 py-4 sticky top-0 z-10">
      <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-4">
          <button @click="$router.back()" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <div>
            <h1 class="text-lg font-bold text-slate-900">İade Detayı</h1>
            <p class="text-sm text-slate-500">{{ returnData?.return_number }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="max-w-3xl mx-auto px-4 py-8">
      <div class="animate-pulse space-y-4">
        <div class="h-24 bg-slate-200 rounded-xl"></div>
        <div class="h-32 bg-slate-200 rounded-xl"></div>
        <div class="h-48 bg-slate-200 rounded-xl"></div>
      </div>
    </div>

    <div v-else-if="returnData" class="max-w-3xl mx-auto px-4 py-6 space-y-6">
      <!-- Status Card -->
      <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-6" :class="getStatusBgClass(returnData.status)">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full flex items-center justify-center" :class="getStatusIconBgClass(returnData.status)">
              <component :is="getStatusIcon(returnData.status)" class="w-8 h-8" :class="getStatusIconClass(returnData.status)" />
            </div>
            <div>
              <h2 class="text-xl font-bold" :class="getStatusTextClass(returnData.status)">
                {{ returnData.status_label }}
              </h2>
              <p class="text-sm opacity-80" :class="getStatusTextClass(returnData.status)">
                {{ getStatusDescription(returnData.status) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="px-6 py-4">
          <div class="relative">
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
            <div v-for="(log, index) in returnData.logs" :key="log.id" class="relative flex gap-4 pb-6 last:pb-0">
              <div class="w-8 h-8 rounded-full bg-white border-2 flex items-center justify-center z-10"
                :class="index === 0 ? 'border-indigo-500 text-indigo-500' : 'border-slate-300 text-slate-400'"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div>
                <p class="font-medium text-slate-900">{{ log.action_label }}</p>
                <p v-if="log.notes" class="text-sm text-slate-500 mt-1">{{ log.notes }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ formatDateTime(log.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product Info -->
      <div class="bg-white rounded-xl border border-slate-200 p-4">
        <h3 class="font-semibold text-slate-900 mb-4">İade Edilen Ürün</h3>
        <div class="flex gap-4">
          <div class="w-24 h-24 bg-slate-100 rounded-lg overflow-hidden flex-shrink-0">
            <img 
              v-if="returnData.order_item?.product?.image_url" 
              :src="returnData.order_item.product.image_url" 
              :alt="returnData.order_item.product.name"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-3xl">📦</div>
          </div>
          <div class="flex-1">
            <h4 class="font-medium text-slate-900">{{ returnData.order_item?.product?.name || 'Tüm Sipariş İadesi' }}</h4>
            <p class="text-sm text-slate-500 mt-1">Adet: {{ returnData.quantity }}</p>
            <p class="text-sm text-slate-500">Sipariş No: {{ returnData.order?.order_number }}</p>
            <p class="text-lg font-bold text-indigo-600 mt-2">{{ returnData.refund_amount }} TL</p>
          </div>
        </div>
      </div>

      <!-- Return Reason -->
      <div class="bg-white rounded-xl border border-slate-200 p-4">
        <h3 class="font-semibold text-slate-900 mb-4">İade Sebebi</h3>
        <div class="bg-slate-50 rounded-lg p-4">
          <p class="font-medium text-slate-900">{{ returnData.reason_category_label }}</p>
          <p v-if="returnData.description" class="text-sm text-slate-600 mt-2">{{ returnData.description }}</p>
        </div>
        
        <!-- Images -->
        <div v-if="returnData.images?.length" class="mt-4">
          <p class="text-sm text-slate-500 mb-2">Eklenen Fotoğraflar:</p>
          <div class="flex gap-2 flex-wrap">
            <img 
              v-for="(image, index) in returnData.images" 
              :key="index"
              :src="image"
              class="w-20 h-20 rounded-lg object-cover cursor-pointer hover:opacity-80"
              @click="openImage(image)"
            />
          </div>
        </div>
      </div>

      <!-- Refund Info -->
      <div class="bg-white rounded-xl border border-slate-200 p-4">
        <h3 class="font-semibold text-slate-900 mb-4">Para İadesi</h3>
        <div class="space-y-3">
          <div class="flex justify-between">
            <span class="text-slate-600">Durum</span>
            <span 
              class="px-2 py-1 text-xs font-medium rounded-full"
              :class="getRefundStatusClass(returnData.refund_status)"
            >
              {{ returnData.refund_status_label }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600">İade Yöntemi</span>
            <span class="font-medium text-slate-900">{{ getRefundMethodLabel(returnData.refund_method) }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600">İade Tutarı</span>
            <span class="font-bold text-lg text-indigo-600">{{ returnData.refund_amount }} TL</span>
          </div>
          <div v-if="returnData.refunded_at" class="flex justify-between">
            <span class="text-slate-600">İade Tarihi</span>
            <span class="text-slate-900">{{ formatDate(returnData.refunded_at) }}</span>
          </div>
        </div>
      </div>

      <!-- Return Shipping Code (Satıcıdan gelen ücretsiz kargo kodu) -->
      <div v-if="returnData.return_shipping_code && returnData.status === 'approved'" class="bg-green-50 border border-green-200 rounded-xl p-4">
        <h3 class="font-semibold text-green-800 mb-2">🎉 Ücretsiz İade Kargo Kodu</h3>
        <p class="text-sm text-green-700 mb-4">Satıcı size ücretsiz iade kargo kodu gönderdi. Bu kodu kargo gönderimi sırasında kullanabilirsiniz.</p>
        
        <div class="bg-white rounded-lg p-4 border border-green-300">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-slate-600">Kargo Firması</span>
            <span class="font-medium text-slate-900">{{ getCarrierLabel(returnData.return_shipping_carrier) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-sm text-slate-600">Kargo Kodu</span>
            <div class="flex items-center gap-2">
              <code class="bg-green-100 text-green-800 px-3 py-1 rounded font-mono text-lg font-bold">
                {{ returnData.return_shipping_code }}
              </code>
              <button 
                @click="copyShippingCode"
                class="p-2 hover:bg-green-100 rounded-lg text-green-600"
                title="Kodu Kopyala"
              >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
        <p class="text-xs text-green-600 mt-3">
          💡 Bu kodu {{ getCarrierLabel(returnData.return_shipping_carrier) }} şubesine giderek veya online kargo girişi yaparak kullanabilirsiniz.
        </p>
      </div>

      <!-- Shipping Info (if approved) -->
      <div v-if="returnData.status === 'approved' && !returnData.tracking_number" class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <h3 class="font-semibold text-amber-800 mb-2">Kargo Bilgilerini Girin</h3>
        <p class="text-sm text-amber-700 mb-4">Ürünü kargoya verdikten sonra takip numarasını girin.</p>
        
        <div class="space-y-3">
          <input 
            v-model="shippingForm.tracking_number"
            type="text"
            placeholder="Kargo Takip Numarası"
            class="w-full px-4 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
          />
          <select 
            v-model="shippingForm.shipping_carrier"
            required
            class="w-full px-4 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
          >
            <option disabled value="">Kargo Firması Seçin</option>
            <option value="yurtici">Yurtiçi Kargo</option>
            <option value="aras">Aras Kargo</option>
            <option value="mng">MNG Kargo</option>
            <option value="ptt">PTT Kargo</option>
            <option value="surat">Sürat Kargo</option>
          </select>
          <button 
            @click="submitShipping"
            :disabled="!shippingForm.tracking_number || !shippingForm.shipping_carrier || submitting"
            class="w-full py-2 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 transition-colors disabled:opacity-50"
          >
            {{ submitting ? 'Gönderiliyor...' : 'Kargo Bilgilerini Kaydet' }}
          </button>
        </div>
      </div>

      <!-- Tracking Info -->
      <div v-if="returnData.tracking_number" class="bg-white rounded-xl border border-slate-200 p-4">
        <h3 class="font-semibold text-slate-900 mb-4">Kargo Bilgileri</h3>
        <div class="space-y-2">
          <div class="flex justify-between">
            <span class="text-slate-600">Takip No</span>
            <span class="font-medium text-slate-900">{{ returnData.tracking_number }}</span>
          </div>
          <div v-if="returnData.shipping_carrier" class="flex justify-between">
            <span class="text-slate-600">Kargo Firması</span>
            <span class="text-slate-900">{{ getCarrierLabel(returnData.shipping_carrier) }}</span>
          </div>
        </div>
      </div>

      <!-- Cancel Button -->
      <button 
        v-if="canCancel"
        @click="cancelReturn"
        class="w-full py-3 border-2 border-red-200 text-red-600 font-medium rounded-xl hover:bg-red-50 transition-colors"
      >
        İade Talebini İptal Et
      </button>

      <!-- Notes -->
      <div v-if="returnData.rejection_reason" class="bg-red-50 border border-red-200 rounded-xl p-4">
        <h3 class="font-semibold text-red-800 mb-2">Red Sebebi</h3>
        <p class="text-sm text-red-700">{{ returnData.rejection_reason }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, h } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const returnId = computed(() => route.params.id)
const loading = ref(true)
const submitting = ref(false)
const returnData = ref<any>(null)

const shippingForm = ref({
  tracking_number: '',
  shipping_carrier: 'yurtici',
})

const canCancel = computed(() => {
  return returnData.value && ['pending', 'approved'].includes(returnData.value.status)
})

const getCarrierLabel = (carrier: string) => {
  const labels: Record<string, string> = {
    yurtici: 'Yurtiçi Kargo',
    aras: 'Aras Kargo',
    mng: 'MNG Kargo',
    ptt: 'PTT Kargo',
    surat: 'Sürat Kargo',
    ups: 'UPS',
    fedex: 'FedEx',
  }
  return labels[carrier] || carrier
}

const copyShippingCode = async () => {
  if (returnData.value?.return_shipping_code) {
    try {
      await navigator.clipboard.writeText(returnData.value.return_shipping_code)
      toast.success('Kargo kodu kopyalandı!')
    } catch {
      toast.error('Kopyalama başarısız')
    }
  }
}

const fetchReturn = async () => {
  loading.value = true
  try {
    const { data } = await api.get(`/returns/${returnId.value}`)
    returnData.value = data
  } catch (error) {
    console.error('Error fetching return:', error)
    toast.error('İade detayları yüklenemedi')
    router.push('/returns')
  } finally {
    loading.value = false
  }
}

const submitShipping = async () => {
  if (!shippingForm.value.tracking_number || !shippingForm.value.shipping_carrier) {
    toast.warning('Lütfen takip numarası ve kargo firmasını seçin')
    return
  }
  
  submitting.value = true
  try {
    const { data } = await api.post(`/returns/${returnId.value}/ship`, shippingForm.value)
    returnData.value = data.return
    toast.success('Kargo bilgileri kaydedildi')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Bir hata oluştu')
  } finally {
    submitting.value = false
  }
}

const cancelReturn = async () => {
  if (!confirm('İade talebini iptal etmek istediğinize emin misiniz?')) return
  
  try {
    await api.post(`/returns/${returnId.value}/cancel`)
    toast.success('İade talebi iptal edildi')
    router.push('/returns')
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Bir hata oluştu')
  }
}

const openImage = (src: string) => {
  window.open(src, '_blank')
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

const formatDateTime = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
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
    cancelled: 'bg-slate-100',
  }
  return classes[status] || 'bg-slate-50'
}

const getStatusIconBgClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-amber-100',
    approved: 'bg-blue-100',
    shipped: 'bg-purple-100',
    received: 'bg-indigo-100',
    inspecting: 'bg-cyan-100',
    refunded: 'bg-emerald-100',
    completed: 'bg-green-100',
    rejected: 'bg-red-100',
    cancelled: 'bg-slate-200',
  }
  return classes[status] || 'bg-slate-100'
}

const getStatusIconClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'text-amber-600',
    approved: 'text-blue-600',
    shipped: 'text-purple-600',
    received: 'text-indigo-600',
    inspecting: 'text-cyan-600',
    refunded: 'text-emerald-600',
    completed: 'text-green-600',
    rejected: 'text-red-600',
    cancelled: 'text-slate-600',
  }
  return classes[status] || 'text-slate-600'
}

const getStatusTextClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'text-amber-800',
    approved: 'text-blue-800',
    shipped: 'text-purple-800',
    received: 'text-indigo-800',
    inspecting: 'text-cyan-800',
    refunded: 'text-emerald-800',
    completed: 'text-green-800',
    rejected: 'text-red-800',
    cancelled: 'text-slate-700',
  }
  return classes[status] || 'text-slate-700'
}

const getStatusIcon = (status: string) => {
  // Return a simple icon component
  return {
    render() {
      return h('svg', {
        fill: 'none',
        viewBox: '0 0 24 24',
        stroke: 'currentColor',
      }, [
        h('path', {
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round',
          'stroke-width': '2',
          d: status === 'completed' || status === 'refunded' 
            ? 'M5 13l4 4L19 7'
            : status === 'rejected' || status === 'cancelled'
            ? 'M6 18L18 6M6 6l12 12'
            : 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
        })
      ])
    }
  }
}

const getStatusDescription = (status: string) => {
  const descriptions: Record<string, string> = {
    pending: 'İade talebiniz inceleniyor',
    approved: 'Ürünü kargoya verin ve takip numarasını girin',
    shipped: 'Ürün satıcıya gönderildi',
    received: 'Ürün satıcı tarafından teslim alındı',
    inspecting: 'Ürün inceleniyor',
    refunded: 'Para iadesi yapıldı',
    completed: 'İade süreci tamamlandı',
    rejected: 'İade talebiniz reddedildi',
    cancelled: 'İade talebi iptal edildi',
  }
  return descriptions[status] || ''
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

const getRefundMethodLabel = (method: string) => {
  const labels: Record<string, string> = {
    original: 'Orijinal Ödeme Yöntemi',
    wallet: 'Cüzdana',
    bank_transfer: 'Banka Transferi',
  }
  return labels[method] || 'Belirtilmemiş'
}

onMounted(() => {
  fetchReturn()
})
</script>
