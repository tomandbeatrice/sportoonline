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
            <h1 class="text-lg font-bold text-slate-900">İade Talebi Oluştur</h1>
            <p class="text-sm text-slate-500">Sipariş #{{ order?.order_number }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="max-w-3xl mx-auto px-4 py-8">
      <div class="animate-pulse space-y-4">
        <div class="h-32 bg-slate-200 rounded-xl"></div>
        <div class="h-48 bg-slate-200 rounded-xl"></div>
        <div class="h-24 bg-slate-200 rounded-xl"></div>
      </div>
    </div>

    <!-- Not Eligible -->
    <div v-else-if="!eligibility?.eligible" class="max-w-3xl mx-auto px-4 py-8">
      <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        <h2 class="text-lg font-bold text-red-800 mb-2">İade Yapılamıyor</h2>
        <p class="text-red-600">{{ eligibility?.message || 'Bu sipariş için iade talebi oluşturamazsınız.' }}</p>
        <button @click="$router.push('/orders')" class="mt-4 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
          Siparişlerime Dön
        </button>
      </div>
    </div>

    <!-- Return Form -->
    <div v-else class="max-w-3xl mx-auto px-4 py-6 space-y-6">
      <!-- Time Remaining -->
      <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <p class="text-sm font-medium text-amber-800">İade süresi dolmak üzere!</p>
          <p class="text-xs text-amber-600">Kalan süre: {{ eligibility?.days_remaining }} gün</p>
        </div>
      </div>

      <!-- Select Product -->
      <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
          <h2 class="font-semibold text-slate-900">İade Edilecek Ürün</h2>
        </div>
        <div class="divide-y divide-slate-100">
          <label 
            v-for="item in eligibleItems" 
            :key="item.id"
            class="flex items-center gap-4 p-4 cursor-pointer hover:bg-slate-50 transition-colors"
            :class="{ 'bg-indigo-50 border-l-4 border-indigo-500': selectedItem?.id === item.id }"
          >
            <input 
              type="radio" 
              :value="item" 
              v-model="selectedItem" 
              class="w-4 h-4 text-indigo-600"
              :disabled="!item.can_return"
            />
            <div class="w-16 h-16 bg-slate-100 rounded-lg overflow-hidden flex-shrink-0">
              <img v-if="item.product?.image_url" :src="item.product.image_url" :alt="item.product?.name" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full flex items-center justify-center text-2xl">📦</div>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="font-medium text-slate-900 truncate">{{ item.product?.name }}</h3>
              <p class="text-sm text-slate-500">Adet: {{ item.quantity }} • {{ item.price }} TL</p>
              <p v-if="!item.can_return" class="text-xs text-red-500 mt-1">
                {{ item.existing_return ? 'Bu ürün için zaten iade talebi var' : 'İade edilemez' }}
              </p>
            </div>
          </label>
        </div>
      </div>

      <!-- Quantity -->
      <div v-if="selectedItem && selectedItem.quantity > 1" class="bg-white rounded-xl border border-slate-200 p-4">
        <label class="block text-sm font-medium text-slate-700 mb-2">İade Edilecek Miktar</label>
        <div class="flex items-center gap-4">
          <button 
            @click="quantity > 1 && quantity--" 
            class="w-10 h-10 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50"
            :disabled="quantity <= 1"
          >-</button>
          <input 
            v-model.number="quantity" 
            type="number" 
            min="1" 
            :max="selectedItem.quantity"
            class="w-20 text-center border border-slate-200 rounded-lg py-2"
          />
          <button 
            @click="quantity < selectedItem.quantity && quantity++" 
            class="w-10 h-10 rounded-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50"
            :disabled="quantity >= selectedItem.quantity"
          >+</button>
          <span class="text-sm text-slate-500">/ {{ selectedItem.quantity }}</span>
        </div>
      </div>

      <!-- Reason Category -->
      <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
          <h2 class="font-semibold text-slate-900">İade Sebebi</h2>
        </div>
        <div class="p-4 grid grid-cols-2 gap-3">
          <button 
            v-for="reason in reasons" 
            :key="reason.value"
            @click="form.reason_category = reason.value"
            class="p-3 rounded-lg border text-left transition-all"
            :class="form.reason_category === reason.value 
              ? 'border-indigo-500 bg-indigo-50 text-indigo-700' 
              : 'border-slate-200 hover:border-slate-300'"
          >
            <span class="text-sm font-medium">{{ reason.label }}</span>
          </button>
        </div>
      </div>

      <!-- Description -->
      <div class="bg-white rounded-xl border border-slate-200 p-4">
        <label class="block text-sm font-medium text-slate-700 mb-2">Detaylı Açıklama</label>
        <textarea 
          v-model="form.description"
          rows="4"
          class="w-full border border-slate-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="İade sebebinizi detaylı olarak açıklayın..."
        ></textarea>
      </div>

      <!-- Images -->
      <div class="bg-white rounded-xl border border-slate-200 p-4">
        <label class="block text-sm font-medium text-slate-700 mb-2">Fotoğraflar (Opsiyonel)</label>
        <p class="text-xs text-slate-500 mb-3">Ürünün durumunu gösteren fotoğraflar ekleyin (max 5 adet)</p>
        
        <div class="flex flex-wrap gap-3">
          <div 
            v-for="(image, index) in form.images" 
            :key="index"
            class="relative w-20 h-20 rounded-lg overflow-hidden border border-slate-200"
          >
            <img :src="image" :alt="`İade fotoğraf ${index + 1}`" class="w-full h-full object-cover" />
            <button 
              @click="removeImage(index)"
              aria-label="Fotoğrafı kaldır"
              class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs"
            >×</button>
          </div>
          
          <label v-if="form.images.length < 5" class="w-20 h-20 border-2 border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:border-indigo-500 transition-colors">
            <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="text-xs text-slate-400 mt-1">Ekle</span>
            <input type="file" accept="image/*" @change="handleImageUpload" class="hidden" />
          </label>
        </div>
      </div>

      <!-- Refund Info -->
      <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="font-medium text-emerald-800">Tahmini İade Tutarı</h3>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ estimatedRefund }} TL</p>
            <p class="text-xs text-emerald-600 mt-1">İade onaylandıktan sonra orijinal ödeme yönteminize aktarılacaktır.</p>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <button 
        @click="submitReturn"
        :disabled="!canSubmit || submitting"
        class="w-full py-4 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
      >
        <svg v-if="submitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>{{ submitting ? 'Gönderiliyor...' : 'İade Talebi Oluştur' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const orderId = computed(() => route.params.orderId)
const loading = ref(true)
const submitting = ref(false)

const order = ref<any>(null)
const eligibility = ref<any>(null)
const reasons = ref<any[]>([])
const selectedItem = ref<any>(null)
const quantity = ref(1)

const form = ref({
  reason_category: '',
  description: '',
  images: [] as string[],
})

const eligibleItems = computed(() => {
  return eligibility.value?.items?.filter((item: any) => item.can_return) || []
})

const estimatedRefund = computed(() => {
  if (!selectedItem.value) return 0
  return (parseFloat(selectedItem.value.price) * quantity.value).toFixed(2)
})

const canSubmit = computed(() => {
  return selectedItem.value && form.value.reason_category && eligibility.value?.eligible
})

const fetchEligibility = async () => {
  loading.value = true
  try {
    const [eligibilityRes, reasonsRes] = await Promise.all([
      api.get(`/returns/check-eligibility/${orderId.value}`),
      api.get('/returns/reasons'),
    ])
    
    eligibility.value = eligibilityRes.data
    reasons.value = reasonsRes.data
    
    // İlk iade edilebilir ürünü seç
    if (eligibleItems.value.length > 0) {
      selectedItem.value = eligibleItems.value[0]
    }
  } catch (error) {
    console.error('Error fetching eligibility:', error)
    toast.error('Bilgiler alınamadı')
  } finally {
    loading.value = false
  }
}

const handleImageUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return
  
  // Convert to base64
  const reader = new FileReader()
  reader.onload = (e) => {
    if (form.value.images.length < 5) {
      form.value.images.push(e.target?.result as string)
    }
  }
  reader.readAsDataURL(file)
}

const removeImage = (index: number) => {
  form.value.images.splice(index, 1)
}

const submitReturn = async () => {
  if (!canSubmit.value) return
  
  submitting.value = true
  try {
    const payload = {
      order_id: orderId.value,
      order_item_id: selectedItem.value.id,
      quantity: quantity.value,
      reason_category: form.value.reason_category,
      description: form.value.description,
      images: form.value.images,
    }
    
    const { data } = await api.post('/returns', payload)
    
    toast.success('İade talebi oluşturuldu!')
    router.push(`/returns/${data.return.id}`)
  } catch (error: any) {
    console.error('Error submitting return:', error)
    toast.error(error.response?.data?.message || 'İade talebi oluşturulamadı')
  } finally {
    submitting.value = false
  }
}

// Ürün değiştiğinde miktarı sıfırla
watch(selectedItem, () => {
  quantity.value = 1
})

onMounted(() => {
  fetchEligibility()
})
</script>
