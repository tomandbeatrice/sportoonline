<template>
  <div class="admin-financial-report max-w-7xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-8">Platform Finansal Raporu</h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-blue-50 p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Toplam Ciro</div>
        <div class="text-3xl font-bold text-blue-600">{{ formatPrice(summary.total_revenue) }} ₺</div>
      </div>
      <div class="bg-green-50 p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Platform Geliri</div>
        <div class="text-3xl font-bold text-green-600">{{ formatPrice(summary.total_platform_fees) }} ₺</div>
      </div>
      <div class="bg-purple-50 p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Satıcı Ödemeleri</div>
        <div class="text-3xl font-bold text-purple-600">{{ formatPrice(summary.total_seller_payouts) }} ₺</div>
      </div>
      <div class="bg-orange-50 p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Toplam Sipariş</div>
        <div class="text-3xl font-bold text-orange-600">{{ summary.total_orders }}</div>
      </div>
    </div>

    <!-- Sales by Seller -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="px-6 py-4 border-b">
        <h2 class="text-xl font-semibold">Satıcı Bazlı Satışlar</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satıcı</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam Ciro</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Platform Geliri</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satıcı Kazancı</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş Sayısı</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="seller in salesBySeller" :key="seller.seller_id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ seller.seller_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatPrice(seller.total_revenue) }} ₺</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">{{ formatPrice(seller.platform_fees) }} ₺</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-600">{{ formatPrice(seller.seller_payout) }} ₺</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ seller.order_count }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b">
        <h2 class="text-xl font-semibold">Son İşlemler</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satıcı</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Toplam</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komisyon</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="transaction in recentTransactions" :key="transaction.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ new Date(transaction.date).toLocaleDateString('tr-TR') }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transaction.seller_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transaction.product_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                {{ formatPrice(transaction.total) }} ₺
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                {{ formatPrice(transaction.platform_fee) }} ₺
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span :class="getStatusClass(transaction.status)" class="px-2 py-1 rounded-full text-xs">
                  {{ transaction.status }}
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
  total_seller_payouts: 0,
  total_orders: 0,
});

const salesBySeller = ref<any[]>([]);
const recentTransactions = ref<any[]>([]);

async function loadFinancialReport() {
  try {
    const response = await axios.get('/api/admin/financial-report');
    summary.value = response.data.summary;
    salesBySeller.value = response.data.sales_by_seller;
    recentTransactions.value = response.data.recent_transactions;
  } catch (error) {
    console.error('Finansal rapor yüklenirken hata:', error);
    alert('Finansal rapor yüklenemedi.');
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
