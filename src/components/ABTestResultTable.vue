<template>
  <div>
    <h3>A/B Test Sonuçları</h3>
    <table>
      <thead>
        <tr>
          <th>Segment</th>
          <th>A Kampanya</th>
          <th>A İndirim</th>
          <th>A Başarı</th>
          <th>B Kampanya</th>
          <th>B İndirim</th>
          <th>B Başarı</th>
          <th>Kazanan</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in results" :key="index">
          <td>{{ item.segment }}</td>
          <td>{{ item.campaign_type_a }}</td>
          <td>%{{ item.discount_a }}</td>
          <td>{{ item.success_a }}</td>
          <td>{{ item.campaign_type_b }}</td>
          <td>%{{ item.discount_b }}</td>
          <td>{{ item.success_b }}</td>
          <td :style="{ fontWeight: 'bold', color: item.success_a > item.success_b ? 'green' : item.success_b > item.success_a ? 'blue' : 'gray' }">
            {{ item.success_a > item.success_b ? 'A' : item.success_b > item.success_a ? 'B' : 'Eşit' }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const results = ref([
  {
    segment: 'Premium',
    campaign_type_a: 'Outlet Boost',
    discount_a: 30,
    success_a: 78,
    campaign_type_b: 'Flash Sale',
    discount_b: 25,
    success_b: 84
  },
  {
    segment: 'Standard',
    campaign_type_a: 'Flash Sale',
    discount_a: 20,
    success_a: 74,
    campaign_type_b: 'Organic Push',
    discount_b: 15,
    success_b: 68
  }
])
</script>