<template>
  <section class="p-4">
    <h2 class="text-lg font-bold text-gray-700">🗂 Otomatik Planlama Geçmişi</h2>
    <table class="w-full text-sm mt-4">
      <thead>
        <tr class="bg-gray-100">
          <th class="text-left p-2">Tarih</th>
          <th class="text-left p-2">Planlanan Kampanya</th>
          <th class="text-left p-2">Doldurulan Günler</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.run_at">
          <td class="p-2">{{ log.run_at }}</td>
          <td class="p-2">{{ log.planned_count }}</td>
          <td class="p-2">{{ log.filled_dates.join(', ') }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const logs = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/auto-plan-history')
  logs.value = res.data
})
</script>