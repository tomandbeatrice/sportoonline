<template>
  <section class="p-6 space-y-4">
    <h2 class="text-xl font-bold text-blue-700">Karar Destek: Kampanya Türü Takvimi</h2>
    <table class="w-full text-sm border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 text-left">Tür</th>
          <th class="p-2 text-right">Dönüşüm (%)</th>
          <th class="p-2 text-right">Son Çalıştırma</th>
          <th class="p-2 text-right">Önerilen Tekrar</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in data" :key="item.type">
          <td class="p-2">{{ item.type }}</td>
          <td class="p-2 text-right">{{ item.conversionRate }}</td>
          <td class="p-2 text-right">{{ item.lastRun }}</td>
          <td class="p-2 text-right text-green-600 font-semibold">{{ item.nextRecommended }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const data = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/campaign-decision-support')
  data.value = res.data
})
</script>