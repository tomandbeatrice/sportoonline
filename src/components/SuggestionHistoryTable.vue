<template>
  <div>
    <h3>Öneri Doğruluk Geçmişi</h3>
    <table>
      <thead>
        <tr>
          <th>Tarih</th>
          <th>Segment</th>
          <th>Kampanya</th>
          <th>İndirim</th>
          <th>Güven (%)</th>
          <th>Başarı Skoru</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in history" :key="index">
          <td>{{ item.date }}</td>
          <td>{{ item.segment }}</td>
          <td>{{ item.campaign_type }}</td>
          <td>%{{ item.suggested_discount }}</td>
          <td>{{ item.confidence_score }}%</td>
          <td>{{ item.actual_success_score }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const history = ref([
  {
    date: '2025-08-30 14:12',
    segment: 'Premium',
    campaign_type: 'Outlet Boost',
    suggested_discount: 30,
    confidence_score: 92,
    actual_success_score: 78
  },
  {
    date: '2025-08-29 11:45',
    segment: 'Standard',
    campaign_type: 'Flash Sale',
    suggested_discount: 20,
    confidence_score: 74,
    actual_success_score: 81
  }
])
</script>