<template>
  <div>
    <h2>Kampanya Öneri Ayarları</h2>
    <form @submit.prevent="save">
      <label>İndirim Eşiği (%):</label>
      <input type="number" v-model="rules.discount_threshold" min="0" max="100" />

      <label>Ürün Sayısı Eşiği:</label>
      <input type="number" v-model="rules.product_count_threshold" min="1" />

      <label>Kampanya Tipleri:</label>
      <input type="text" v-for="(type, i) in rules.campaign_types" :key="i" v-model="rules.campaign_types[i]" />

      <button type="submit">Kaydet</button>
    </form>
    <p v-if="status">{{ status }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const rules = ref({
  discount_threshold: 20,
  product_count_threshold: 10,
  campaign_types: ['Outlet Boost', 'Flash Sale']
})
const status = ref('')

onMounted(async () => {
  const res = await axios.get('/api/admin/suggestion-rules')
  if (res.data) rules.value = res.data
})

async function save() {
  await axios.put('/api/admin/suggestion-rules', rules.value)
  status.value = 'Ayarlar güncellendi ✅'
}
</script>