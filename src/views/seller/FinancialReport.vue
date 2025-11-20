<template>
  <div class="financial-report max-w-7xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-8">Finans Raporu</h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-blue-50 p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Toplam Gelir</div>
        <div class="text-3xl font-bold text-blue-600">{{ formatPrice(summary.total_revenue) }} ₺</div>
      </div>
      <div class="bg-red-50 p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Platform Komisyonu</div>
        <div class="text-3xl font-bold text-red-600">-{{ formatPrice(summary.total_platform_fees) }} ₺</div>
      </div>
      <div class="bg-green-50 p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Net Kazancınız</div>
        <div class="text-3xl font-bold text-green-600">{{ formatPrice(summary.total_seller_payout) }} ₺</div>
      </div>
    </div>

    <!-- Payout Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="bg-yellow-50 p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Bekleyen Ödeme</div>
        <div class="text-2xl font-bold text-yellow-600">{{ formatPrice(summary.pending_payout) }} ₺</div>
        <p class="text-xs text-gray-500 mt-2">Henüz ödenmemiş siparişler</p>
      </div>
      <div class="bg-green-50 p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Onaylanmış Ödeme</div>
        <div class="text-2xl font-bold text-green-600">{{ formatPrice(summary.confirmed_payout) }} ₺</div>
        <p class="text-xs text-gray-500 mt-2">Ödeme alınan siparişler</p>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b">
        <h2 class="text-xl font-semibold">İşlem Geçmişi</h2>
      </div>
      
      <div v-if="loading" class="p-8 text-center text-gray-500">
        Yükleniyor...
      </div>
      
      <div v-else-if="transactions.length === 0" class="p-8 text-center text-gray-500">
        Henüz işlem bulunmuyor.
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adet</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komisyon</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Net Kazanç</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="transaction in transactions" :key="transaction.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ new Date(transaction.date).toLocaleDateString('tr-TR') }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transaction.product_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transaction.quantity }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                {{ formatPrice(transaction.total) }} ₺
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                -{{ formatPrice(transaction.platform_fee) }} ₺
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                {{ formatPrice(transaction.seller_payout) }} ₺
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span :class="getStatusClass(transaction.order_status)" class="px-2 py-1 rounded-full text-xs">
                  {{ transaction.order_status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

const summary = ref({
  total_revenue: 0,
  total_platform_fees: 0,
  total_seller_payout: 0,
  pending_payout: 0,
  confirmed_payout: 0,
});

const transactions = ref<any[]>([]);
const loading = ref(true);

async function loadFinancialReport() {
  try {
    const response = await axios.get('/api/seller/financial-report');
    summary.value = response.data.summary;
    transactions.value = response.data.transactions;
  } catch (error) {
    console.error('Finansal rapor yüklenirken hata:', error);
    alert('Finansal rapor yüklenemedi.');
  } finally {
    loading.value = false;
  }
}

function formatPrice(price: number | null | undefined) {
  if (price === null || price === undefined) return '0,00';
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price);
}

function getStatusClass(status: string) {
  const classes: Record<string, string> = {
    paid: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    failed: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
}

onMounted(() => {
  loadFinancialReport();
});
</script>
