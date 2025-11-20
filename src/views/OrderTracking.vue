<template>
  <div class="order-tracking-page min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
        <p class="text-gray-600 mt-4">Sipariş bilgileri yükleniyor...</p>
      </div>

      <div v-else-if="order" class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Sipariş #{{ order.id }}</h1>
              <p class="text-gray-600 mt-1">{{ formatDate(order.created_at) }}</p>
            </div>
            <span
              class="px-4 py-2 rounded-full text-sm font-semibold"
              :class="getStatusClass(order.status)"
            >
              {{ getStatusText(order.status) }}
            </span>
          </div>

          <!-- Payment Status -->
          <div class="flex items-center gap-4 pt-4 border-t">
            <div class="flex items-center gap-2">
              <svg
                class="w-5 h-5"
                :class="order.payment_status === 'paid' ? 'text-green-500' : 'text-orange-500'"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm font-semibold">
                {{ order.payment_status === 'paid' ? 'Ödeme Alındı' : 'Ödeme Bekleniyor' }}
              </span>
            </div>
            <div class="text-xl font-bold text-gray-900">
              ₺{{ formatMoney(order.total) }}
            </div>
          </div>
        </div>

        <!-- Order Timeline -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-6">Sipariş Durumu</h2>
          
          <div class="space-y-6">
            <div
              v-for="(step, index) in orderSteps"
              :key="index"
              class="flex items-start gap-4"
            >
              <div class="flex flex-col items-center">
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center border-2"
                  :class="{
                    'bg-blue-500 border-blue-500 text-white': step.completed,
                    'bg-white border-gray-300 text-gray-400': !step.completed
                  }"
                >
                  <svg v-if="step.completed" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span v-else>{{ index + 1 }}</span>
                </div>
                <div
                  v-if="index < orderSteps.length - 1"
                  class="w-0.5 h-16 mt-2"
                  :class="step.completed ? 'bg-blue-500' : 'bg-gray-300'"
                ></div>
              </div>
              <div class="flex-1 pt-2">
                <h3 class="font-semibold text-gray-900">{{ step.title }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ step.description }}</p>
                <p v-if="step.date" class="text-xs text-gray-500 mt-1">{{ formatDate(step.date) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping Info -->
        <div v-if="order.shipping_tracking_number" class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Kargo Bilgileri</h2>
          <div class="space-y-2">
            <div class="flex justify-between">
              <span class="text-gray-600">Kargo Firması:</span>
              <span class="font-semibold">{{ order.shipping_company || 'Belirtilmemiş' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Takip Numarası:</span>
              <span class="font-semibold">{{ order.shipping_tracking_number }}</span>
            </div>
          </div>
          <button
            @click="trackShipment"
            class="mt-4 w-full py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg"
          >
            Kargoyu Takip Et
          </button>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Sipariş Detayları</h2>
          
          <div class="space-y-4">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center gap-4 pb-4 border-b last:border-0"
            >
              <img
                :src="item.product?.image_url || '/placeholder.jpg'"
                :alt="item.product_name"
                class="w-20 h-20 object-cover rounded"
              />
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900">{{ item.product_name }}</h3>
                <p class="text-sm text-gray-600 mt-1">Adet: {{ item.quantity }}</p>
                <p class="text-sm text-gray-600">Birim Fiyat: ₺{{ formatMoney(item.unit_price) }}</p>
              </div>
              <div class="text-right">
                <p class="font-bold text-gray-900">₺{{ formatMoney(item.total_price) }}</p>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t space-y-2">
            <div class="flex justify-between text-gray-700">
              <span>Ara Toplam</span>
              <span>₺{{ formatMoney(order.subtotal) }}</span>
            </div>
            <div class="flex justify-between text-gray-700">
              <span>Kargo</span>
              <span>₺{{ formatMoney(order.shipping_cost || 0) }}</span>
            </div>
            <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
              <span>Toplam</span>
              <span>₺{{ formatMoney(order.total) }}</span>
            </div>
          </div>
        </div>

        <!-- Delivery Address -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Teslimat Adresi</h2>
          <div class="text-gray-700">
            <p class="font-semibold">{{ order.shipping_name }}</p>
            <p class="mt-2">{{ order.shipping_address }}</p>
            <p class="mt-1">{{ order.shipping_phone }}</p>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-600">Sipariş bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const order = ref(null)
const loading = ref(true)

const orderSteps = computed(() => {
  if (!order.value) return []

  const steps = [
    {
      title: 'Sipariş Alındı',
      description: 'Siparişiniz başarıyla oluşturuldu',
      completed: true,
      date: order.value.created_at
    },
    {
      title: 'Ödeme Onaylandı',
      description: 'Ödemeniz alındı ve onaylandı',
      completed: order.value.payment_status === 'paid',
      date: order.value.payment_status === 'paid' ? order.value.updated_at : null
    },
    {
      title: 'Hazırlanıyor',
      description: 'Siparişiniz hazırlanıyor',
      completed: ['processing', 'shipped', 'delivered'].includes(order.value.status),
      date: null
    },
    {
      title: 'Kargoya Verildi',
      description: 'Siparişiniz kargoya teslim edildi',
      completed: ['shipped', 'delivered'].includes(order.value.status),
      date: order.value.shipped_at
    },
    {
      title: 'Teslim Edildi',
      description: 'Siparişiniz teslim edildi',
      completed: order.value.status === 'delivered',
      date: order.value.delivered_at
    }
  ]

  return steps
})

const loadOrder = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/api/orders/${route.params.id}`)
    order.value = data.order || data
  } catch (error) {
    console.error('Failed to load order:', error)
  } finally {
    loading.value = false
  }
}

const trackShipment = () => {
  if (order.value.shipping_tracking_number) {
    // Open tracking URL in new tab
    window.open(`https://gonderitakip.com/${order.value.shipping_tracking_number}`, '_blank')
  }
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

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

onMounted(() => {
  loadOrder()
})
</script>
