<template>
  <div class="seller-dashboard max-w-4xl mx-auto p-8 bg-white rounded-lg shadow">
    <h1 class="text-3xl font-bold mb-6">Satıcı Paneli</h1>
    <div class="grid grid-cols-3 gap-6 mb-8">
      <div class="bg-blue-100 p-4 rounded">
        <span class="block text-sm text-gray-600">Toplam Ürünlerim</span>
        <span class="text-2xl font-bold">{{ stats.product_count }}</span>
      </div>
      <div class="bg-green-100 p-4 rounded">
        <span class="block text-sm text-gray-600">Toplam Satışlar</span>
        <span class="text-2xl font-bold">{{ stats.total_sales }}</span>
      </div>
      <div class="bg-yellow-100 p-4 rounded">
        <span class="block text-sm text-gray-600">Bekleyen Siparişler</span>
        <span class="text-2xl font-bold">{{ stats.pending_orders }}</span>
      </div>
    </div>
    <div>
      <h2 class="text-xl font-semibold mb-4">Ürünlerim</h2>
      <router-link to="/seller/products/new" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Yeni Ürün Ekle</router-link>
      <ul>
        <li v-for="product in products" :key="product.id" class="border-b py-2">
          {{ product.name }} - {{ product.price }}₺ - Stok: {{ product.stock }}
        </li>
      </ul>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({
  product_count: 0,
  total_sales: 0,
  pending_orders: 0
});
const products = ref([]);

onMounted(async () => {
  try {
    const [statsRes, productsRes] = await Promise.all([
      axios.get('/api/seller/stats'),
      axios.get('/api/seller/products')
    ]);
    stats.value = statsRes.data;
    products.value = productsRes.data;
  } catch (e) {
    console.error('Satıcı verileri yüklenemedi:', e);
  }
});
</script>
