<template>
  <section class="p-6 space-y-4">
    <h2 class="text-xl font-bold text-indigo-700">Kampanya Türü Başarı Analizi</h2>
    <table class="w-full text-sm border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 text-left">Tür</th>
          <th class="p-2 text-right">Sipariş</th>
          <th class="p-2 text-right">Görüntüleme</th>
          <th class="p-2 text-right">Dönüşüm (%)</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in analytics" :key="item.type">
          <td class="p-2">{{ item.type }}</td>
          <td class="p-2 text-right">{{ item.orders }}</td>
          <td class="p-2 text-right">{{ item.views }}</td>
          <td class="p-2 text-right">{{ item.conversionRate }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const analytics = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/campaign-type-analytics')
  analytics.value = res.data
})
</script>