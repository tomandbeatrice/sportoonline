<template>
  <div>
    <h3>Öneri Sapma Analizi</h3>
    <table>
      <thead>
        <tr>
          <th>Tarih</th>
          <th>Segment</th>
          <th>Kampanya</th>
          <th>Güven (%)</th>
          <th>Başarı</th>
          <th>Sapma</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in history" :key="index">
          <td>{{ item.date }}</td>
          <td>{{ item.segment }}</td>
          <td>{{ item.campaign_type }}</td>
          <td>{{ item.confidence_score }}%</td>
          <td>{{ item.actual_success_score }}</td>
          <td :style="{ color: item.delta > 0 ? 'green' : item.delta < 0 ? 'red' : 'gray' }">
            {{ item.delta > 0 ? '+' : '' }}{{ item.delta }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const history = ref([
  {
    date: '2025-08-30 14:12',
    segment: 'Premium',
    campaign_type: 'Outlet Boost',
    confidence_score: 92,
    actual_success_score: 78,
    delta: -14
  },
  {
    date: '2025-08-29 11:45',
    segment: 'Standard',
    campaign_type: 'Flash Sale',
    confidence_score: 74,
    actual_success_score: 81,
    delta: 7
  }
])
</script>