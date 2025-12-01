<template>
  <div class="orders-page max-w-7xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-8">Siparişlerim</h1>

    <div v-if="loading" class="text-center py-20">
      <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Siparişler yükleniyor...</p>
    </div>

    <div v-else-if="orders.length === 0" class="text-center py-20">
      <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
      </svg>
      <p class="text-xl text-gray-600 mb-4">Henüz siparişiniz yok</p>
      <router-link to="/" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 inline-block">
        Alışverişe Başla
      </router-link>
    </div>

    <div v-else class="space-y-6">
      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white rounded-lg shadow-lg overflow-hidden"
      >
        <!-- Sipariş Başlığı -->
        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
          <div>
            <h2 class="text-lg font-semibold">Sipariş #{{ order.id }}</h2>
            <p class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</p>
          </div>
          <span :class="getStatusBadgeClass(order.status)" class="px-4 py-2 rounded-full text-sm font-semibold">
            {{ getStatusText(order.status) }}
          </span>
        </div>

        <!-- Sipariş İçeriği -->
        <div class="p-6">
          <div class="space-y-4 mb-6">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center space-x-4 pb-4 border-b last:border-b-0"
            >
              <img
                :src="item.product.image || '/placeholder.png'"
                :alt="item.product.name"
                class="w-24 h-24 object-cover rounded"
              />
              <div class="flex-1">
                <h3 class="font-semibold text-lg">{{ item.product.name }}</h3>
                <p class="text-sm text-gray-600">Satıcı: {{ item.product.seller?.name || 'Bilinmiyor' }}</p>
                <p class="text-sm text-gray-600">Adet: {{ item.quantity }}</p>
                <div class="mt-2">
                  <span :class="getItemStatusClass(item.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                    {{ getItemStatusText(item.status) }}
                  </span>
                </div>
              </div>
              <div class="text-right">
                <p class="text-xl font-bold text-gray-900">{{ formatPrice(item.price * item.quantity) }} ₺</p>
                <p class="text-sm text-gray-500">{{ formatPrice(item.price) }} ₺ / adet</p>
              </div>
            </div>
          </div>

          <!-- Sipariş Özeti -->
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-700">Ara Toplam</span>
              <span class="font-semibold">{{ formatPrice(calculateSubtotal(order)) }} ₺</span>
            </div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-700">Kargo</span>
              <span class="font-semibold text-green-600">Ücretsiz</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t">
              <span class="text-lg font-bold">Toplam</span>
              <span class="text-2xl font-bold text-blue-600">{{ formatPrice(order.total || calculateSubtotal(order)) }} ₺</span>
            </div>
          </div>

          <!-- Teslimat Bilgileri -->
          <div v-if="order.address" class="mt-6 pt-6 border-t">
            <h4 class="font-semibold mb-2 flex items-center gap-2">
              <BadgeIcon name="package" cls="w-5 h-5" /> Teslimat Adresi
            </h4>
            <p class="text-gray-700">{{ order.address }}</p>
            <p v-if="order.phone" class="text-gray-700 mt-1 flex items-center gap-2">
              <BadgeIcon name="phone" cls="w-4 h-4" /> {{ order.phone }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

interface OrderItem {
  id: number;
  product: {
    name: string;
    image?: string;
    seller?: {
      name: string;
    };
  };
  quantity: number;
  price: number;
  status: string;
}

interface Order {
  id: number;
  status: string;
  total?: number;
  created_at: string;
  address?: string;
  phone?: string;
  items: OrderItem[];
}

const orders = ref<Order[]>([]);
const loading = ref(true);

function formatPrice(price: number) {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price);
}

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function calculateSubtotal(order: Order) {
  return order.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
}

function getStatusText(status: string) {
  const statusMap: Record<string, string> = {
    pending: 'Beklemede',
    paid: 'Ödendi',
    processing: 'Hazırlanıyor',
    shipped: 'Kargoya Verildi',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal Edildi',
    failed: 'Başarısız'
  };
  return statusMap[status] || status;
}

function getItemStatusText(status: string) {
  const statusMap: Record<string, string> = {
    pending: 'Beklemede',
    processing: 'Hazırlanıyor',
    shipped: 'Kargoda',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal Edildi'
  };
  return statusMap[status] || status;
}

function getStatusBadgeClass(status: string) {
  const classMap: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    paid: 'bg-green-100 text-green-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    failed: 'bg-red-100 text-red-800'
  };
  return classMap[status] || 'bg-gray-100 text-gray-800';
}

function getItemStatusClass(status: string) {
  const classMap: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  };
  return classMap[status] || 'bg-gray-100 text-gray-800';
}

async function loadOrders() {
  loading.value = true;
  try {
    const response = await axios.get('/api/orders');
    orders.value = response.data;
  } catch (error) {
    console.error('Siparişler yüklenirken hata:', error);
    alert('Siparişler yüklenemedi');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadOrders();
});
</script>

<style scoped>
/* İsteğe bağlı özel stiller */
</style>
