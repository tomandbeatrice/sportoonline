<template>
  <div class="product-list">
    <h2>Ürünler</h2>
    <ProductFilter :categories="categories" @filter="handleFilter" />
    <ul>
      <li v-for="product in products" :key="product.id">
        <span>{{ product.title || product.name }}</span> - <span>{{ product.price }}₺</span>
      </li>
    </ul>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ProductFilter from './ProductFilter.vue';

const products = ref([]);
const categories = ref([]);

async function loadProducts(filters = {}) {
  try {
    const res = await axios.get('/api/products', { params: filters });
    products.value = res.data.products.data || res.data.products || res.data;
    categories.value = res.data.categories || [];
  } catch (e) {
    console.error('Ürünler yüklenemedi:', e);
  }
}

function handleFilter(filters) {
  loadProducts(filters);
}

onMounted(() => {
  loadProducts();
});
</script>
<style scoped>
.product-list { max-width: 600px; margin: auto; }
</style>