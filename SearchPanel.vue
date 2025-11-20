<template>
  <div>
    <input v-model="keyword" placeholder="Ürün ara..." />
    <button @click="search">Ara</button>
    <ul>
      <li v-for="product in results" :key="product.id">{{ product.name }}</li>
    </ul>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref } from 'vue'

const keyword = ref('')
const results = ref([])

async function search() {
  const res = await axios.get('/api/search/products', { params: { keyword: keyword.value } })
  results.value = res.data
}
</script>