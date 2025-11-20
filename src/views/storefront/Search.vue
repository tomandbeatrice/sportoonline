<template>
  <div class="search">
    <h2>Arama Sonuçları</h2>
    <input v-model="query" placeholder="Ürün ara..." />
    <ul>
      <li v-for="product in filtered" :key="product.id">{{ product.name }}</li>
    </ul>
  </div>
</template>
<script setup lang="ts">
import { ref, computed } from 'vue'
const query = ref('')
const products = ref([
  { id: 1, name: 'Laptop' },
  { id: 2, name: 'Telefon' },
  { id: 3, name: 'Kulaklık' }
])
const filtered = computed(() =>
  products.value.filter(p => p.name.toLowerCase().includes(query.value.toLowerCase()))
)
</script>
<style scoped>
.search { max-width: 600px; margin: 2rem auto; }
input { margin-bottom: 12px; padding: 8px; width: 100%; }
ul { list-style: none; padding: 0; }
li { margin-bottom: 8px; }
</style>