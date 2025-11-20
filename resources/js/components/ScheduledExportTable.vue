<template>
  <div>
    <h2>Export Planları</h2>
    <table v-if="plans.length">
      <tr>
        <th>Segment</th><th>Gün</th><th>Saat</th><th>İşlem</th>
      </tr>
      <tr v-for="plan in plans" :key="plan.segmentId + plan.day + plan.time">
        <td>{{ plan.segmentId }}</td>
        <td>{{ plan.day }}</td>
        <td>{{ plan.time }}</td>
        <td><button @click="deletePlan(plan)">Sil</button></td>
      </tr>
    </table>
    <p v-else>Henüz planlanmış export bulunamadı.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const plans = ref([])

const fetchPlans = async () => {
  const res = await fetch('/admin/scheduled-exports')
  plans.value = await res.json()
}

const deletePlan = async (plan) => {
  await fetch(`/admin/scheduled-exports/${plan.segmentId}/${plan.day}/${plan.time}`, { method: 'DELETE' })
  await fetchPlans()
}

onMounted(fetchPlans)
</script>