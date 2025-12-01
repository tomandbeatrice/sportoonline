<template>
  <div v-if="alerts.length">
    <h3>⚠️ Sapma Uyarıları</h3>
    <ul>
      <li v-for="(item, index) in alerts" :key="index" style="color: red; font-weight: bold">
        {{ item.date }} - {{ item.segment }} segmentinde "{{ item.campaign_type }}" kampanyası için tahmin {{ item.confidence_score }}%, başarı {{ item.actual_success_score }}% → Sapma: {{ item.delta }}
      </li>
    </ul>
  </div>
  <div v-else>
    <p>📈 Kritik sapma bulunamadı. AI önerileri stabil görünüyor.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const alerts = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/suggestion-history')
  const history = res.data.suggestion_history

  alerts.value = history.filter(item => Math.abs(item.delta) >= 15)
})
</script>