<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
      <h1 class="text-3xl font-bold mb-8">Sipariş Takip</h1>

      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-500">Yükleniyor...</p>
      </div>

      <div v-else-if="order" class="bg-white rounded-lg shadow p-6">
        <!-- Sipariş Başlığı -->
        <div class="flex justify-between items-start mb-6 pb-6 border-b">
          <div>
            <h2 class="text-2xl font-bold">Sipariş #{{ order.id }}</h2>
            <p class="text-gray-600 mt-1">{{ formatDate(order.created_at) }}</p>
          </div>
          <span
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium',
              getStatusClass(order.status)
            ]"
          >
            {{ getStatusText(order.status) }}
          </span>
        </div>

        <!-- Kargo Takip -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold mb-4">Kargo Durumu</h3>
          <div class="relative">
            <!-- İlerleme Çubuğu -->
            <div class="absolute top-5 left-0 w-full h-1 bg-gray-200">
              <div
                :style="{ width: `${progressPercentage}%` }"
                class="h-full bg-blue-600 transition-all duration-500"
              ></div>
            </div>

            <!-- Adımlar -->
            <div class="relative flex justify-between">
              <div
                v-for="(step, index) in trackingSteps"
                :key="index"
                class="flex flex-col items-center"
              >
                <div
                  :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center font-bold z-10',
                    step.completed
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-200 text-gray-400'
                  ]"
                >
                  {{ step.completed ? '✓' : index + 1 }}
                </div>
                <p class="mt-2 text-sm text-center w-24">{{ step.label }}</p>
                <p v-if="step.date" class="text-xs text-gray-500 mt-1">
                  {{ formatDate(step.date) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Teslimat Bilgileri -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div>
            <h3 class="font-semibold mb-3">Teslimat Adresi</h3>
            <div class="text-gray-700 space-y-1">
              <p>{{ order.shipping_address.name }}</p>
              <p>{{ order.shipping_address.phone }}</p>
              <p>{{ order.shipping_address.address }}</p>
              <p>{{ order.shipping_address.city }}, {{ order.shipping_address.district }}</p>
            </div>
          </div>
          <div>
            <h3 class="font-semibold mb-3">Ödeme Bilgileri</h3>
            <div class="text-gray-700 space-y-1">
              <p>Ödeme Yöntemi: Kredi Kartı</p>
              <p>Toplam: <span class="font-bold">{{ order.total }} TL</span></p>
              <p>
                Durum:
                <span
                  :class="
                    order.payment_status === 'paid'
                      ? 'text-green-600'
                      : 'text-yellow-600'
                  "
                >
                  {{ order.payment_status === 'paid' ? 'Ödendi' : 'Beklemede' }}
                </span>
              </p>
            </div>
          </div>
        </div>

        <!-- Sipariş Ürünleri -->
        <div>
          <h3 class="font-semibold mb-4">Sipariş İçeriği</h3>
          <div class="space-y-4">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg"
            >
              <img
                :src="item.product.image_url"
                :alt="item.product.name"
                class="w-20 h-20 object-cover rounded"
              />
              <div class="flex-1">
                <h4 class="font-medium">{{ item.product.name }}</h4>
                <p class="text-sm text-gray-600">Adet: {{ item.quantity }}</p>
                <p class="text-sm text-gray-600">Satıcı: {{ item.product.vendor_id }}</p>
              </div>
              <div class="text-right">
                <p class="font-semibold">{{ item.price }} TL</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Aksiyon Butonları -->
        <div class="mt-8 flex justify-end space-x-4">
          <button
            v-if="order.status === 'pending'"
            @click="cancelOrder"
            class="px-6 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50"
          >
            Siparişi İptal Et
          </button>
          <button
            @click="contactSupport"
            class="px-6 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50"
          >
            Destek Talebi Oluştur
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const order = ref(null)
const loading = ref(true)

const trackingSteps = computed(() => [
  {
    label: 'Sipariş Alındı',
    completed: ['pending', 'processing', 'shipped', 'delivered'].includes(order.value?.status),
    date: order.value?.created_at
  },
  {
    label: 'Hazırlanıyor',
    completed: ['processing', 'shipped', 'delivered'].includes(order.value?.status),
    date: order.value?.processing_date
  },
  {
    label: 'Kargoya Verildi',
    completed: ['shipped', 'delivered'].includes(order.value?.status),
    date: order.value?.shipped_date
  },
  {
    label: 'Teslim Edildi',
    completed: order.value?.status === 'delivered',
    date: order.value?.delivered_date
  }
])

const progressPercentage = computed(() => {
  const completedSteps = trackingSteps.value.filter(s => s.completed).length
  return (completedSteps / trackingSteps.value.length) * 100
})

const fetchOrder = async () => {
  loading.value = true
  try {
    const { data } = await api.get(`/orders/${route.params.id}`)
    order.value = data
  } catch (error) {
    console.error('Sipariş yüklenemedi:', error)
    alert('Sipariş bulunamadı!')
  } finally {
    loading.value = false
  }
}

const cancelOrder = async () => {
  if (!confirm('Siparişi iptal etmek istediğinizden emin misiniz?')) return

  try {
    await api.put(`/orders/${order.value.id}/status`, { status: 'cancelled' })
    alert('Sipariş iptal edildi.')
    fetchOrder()
  } catch (error) {
    console.error('Sipariş iptal edilemedi:', error)
    alert('Bir hata oluştu!')
  }
}

const contactSupport = () => {
  alert('Destek talebi özelliği yakında eklenecek!')
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Beklemede',
    processing: 'Hazırlanıyor',
    shipped: 'Kargoda',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal Edildi'
  }
  return texts[status] || status
}

onMounted(() => {
  fetchOrder()
})
</script>
