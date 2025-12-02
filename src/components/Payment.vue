<template>
  <form @submit.prevent="pay">
    <input v-model="card.card_number" placeholder="Kart Numarası" />
    <input v-model="card.expiry" placeholder="Son Kullanma Tarihi" />
    <input v-model="card.cvv" placeholder="CVV" />
    <button type="submit">Ödeme Yap</button>
  </form>
</template>

<script setup>
import axios from 'axios'
import { ref } from 'vue'

const card = ref({
  card_number: '',
  expiry: '',
  cvv: ''
})

async function pay() {
  await axios.post('/api/pay', {
    ...card.value,
    order_id: 123 // örnek sipariş ID
  })
  alert('Ödeme başarılı')
}
</script>