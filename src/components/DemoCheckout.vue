<template>
  <div class="bg-white p-4 rounded shadow">
    <h3 class="text-lg font-bold mb-2">Sepet</h3>
    <ul>
      <li v-for="item in cart" :key="item.id" class="flex justify-between py-2 border-b">
        <span>{{ item.title }} ({{ item.variant }})</span>
        <span>₺{{ item.price }}</span>
      </li>
    </ul>
    <button @click="checkout" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Ödeme Yap</button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const cart = ref([
  { id: 1, title: 'Demo Ürün 1', variant: 'Kırmızı - M', price: 129 },
  { id: 2, title: 'Demo Ürün 2', variant: 'Siyah - L', price: 99 }
])

const checkout = async () => {
  await axios.post('/api/demo/checkout', { cart: cart.value })
  alert('Demo ödeme tamamlandı!')
}
</script>