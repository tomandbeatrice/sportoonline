<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div v-for="product in products" :key="product.id" class="border p-4 rounded bg-white">
      <img :src="product.image" class="w-full h-40 object-cover mb-2" />
      <h3 class="text-lg font-bold">{{ product.title }}</h3>
      <p class="text-sm text-gray-600">{{ product.description }}</p>
      <p class="mt-2 font-semibold text-green-600">₺{{ product.price }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])

onMounted(async () => {
  const res = await axios.get('/api/demo/products')
  products.value = res.data
})
</script>