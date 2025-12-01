<template>
  <section class="p-6 space-y-6">
    <h2 class="text-xl font-bold text-indigo-700">Kampanya Etki Analizi</h2>
    <table class="w-full text-sm border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 text-left">Kampanya</th>
          <th class="p-2 text-left">Tarih</th>
          <th class="p-2 text-right">Görüntüleme</th>
          <th class="p-2 text-right">Sipariş</th>
          <th class="p-2 text-right">Dönüşüm (%)</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in impact" :key="item.timestamp">
          <td class="p-2">{{ item.campaign }}</td>
          <td class="p-2">{{ item.timestamp }}</td>
          <td class="p-2 text-right">{{ item.views }}</td>
          <td class="p-2 text-right">{{ item.orders }}</td>
          <td class="p-2 text-right">{{ item.conversionRate }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const impact = ref([])

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  const res = await axios.get(`/api/seller/${sellerId}/campaign-impact-analysis`)
  impact.value = res.data
})
</script>