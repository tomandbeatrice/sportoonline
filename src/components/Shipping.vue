<template>
  <form @submit.prevent="assign">
    <select v-model="carrier">
      <option value="YURTICI">Yurtiçi</option>
      <option value="ARAS">Aras</option>
    </select>
    <button type="submit">Kargo Takip Kodu Oluştur</button>
  </form>
</template>

<script setup>
import axios from 'axios'
import { ref } from 'vue'

const carrier = ref('YURTICI')

async function assign() {
  const res = await axios.post('/api/shipping', {
    order_id: 123,
    carrier: carrier.value
  })
  alert(`Takip Kodu: ${res.data.tracking_code}`)
}
</script>