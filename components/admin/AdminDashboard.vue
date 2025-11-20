<template>
  <div class="admin-dashboard max-w-2xl mx-auto p-8 bg-white rounded-lg shadow">
    <h1 class="text-3xl font-bold mb-4">📊 Admin Panel Dashboard</h1>
    <p class="mb-6">Hoş geldin, Engin. Bugünkü sistem durumu: <span :class="systemStatusClass">{{ systemStatus }}</span></p>
    <div class="grid grid-cols-2 gap-6 mb-6">
      <div class="bg-gray-100 p-4 rounded">
        <span class="block text-sm text-gray-500">Toplam Kullanıcı</span>
        <span class="text-xl font-bold">{{ stats.user_count }}</span>
      </div>
      <div class="bg-gray-100 p-4 rounded">
        <span class="block text-sm text-gray-500">Toplam Ürün</span>
        <span class="text-xl font-bold">{{ stats.product_count }}</span>
      </div>
      <div class="bg-gray-100 p-4 rounded">
        <span class="block text-sm text-gray-500">Toplam Sipariş</span>
        <span class="text-xl font-bold">{{ stats.order_count }}</span>
      </div>
      <div class="bg-gray-100 p-4 rounded">
        <span class="block text-sm text-gray-500">Toplam Satıcı</span>
        <span class="text-xl font-bold">{{ stats.seller_count }}</span>
      </div>
    </div>
    <div class="mb-4">
      <span class="block text-sm text-gray-500">Son 24 Saatteki Siparişler</span>
      <span class="text-xl font-bold">{{ stats.last_24h_orders }}</span>
    </div>
    <div>
      <span class="block text-sm text-gray-500">Sistem Uptime</span>
      <span class="text-xl font-bold">{{ stats.uptime }}</span>
    </div>
    <div class="mt-8 text-gray-600">
      Tüm yönetim ve raporlama işlemlerini sol menüden başlatabilirsin.
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const stats = ref({
  user_count: 0,
  product_count: 0,
  order_count: 0,
  seller_count: 0,
  last_24h_orders: 0,
  uptime: '%99.98',
});
const systemStatus = ref('Stabil');
const systemStatusClass = computed(() =>
  systemStatus.value === 'Stabil' ? 'font-semibold text-green-600' : 'font-semibold text-red-600'
);

onMounted(async () => {
  try {
    const token = localStorage.getItem('token');
    const res = await axios.get('/api/admin/stats', {
      headers: token ? { Authorization: `Bearer ${token}` } : {}
    });
    stats.value = res.data;
    systemStatus.value = res.data.status || 'Stabil';
  } catch (e) {
    console.error('Admin istatistikleri yüklenemedi:', e);
    systemStatus.value = 'Sorun Var';
  }
});
</script>
<style scoped>
.admin-dashboard {
  font-family: 'Inter', sans-serif;
}
</style>