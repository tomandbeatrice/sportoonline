<template>
  <div>
    <h3>Strateji Versiyon Karşılaştırması</h3>
    <table>
      <thead>
        <tr>
          <th>Segment</th>
          <th>Önceki ({{ previousDate }})</th>
          <th>Güncel ({{ currentDate }})</th>
          <th>Fark</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="segment in segments" :key="segment">
          <td>{{ segment }}</td>
          <td>{{ previous[segment] }}</td>
          <td>{{ current[segment] }}</td>
          <td :style="{ color: delta(segment) > 0 ? 'green' : delta(segment) < 0 ? 'red' : 'gray' }">
            {{ delta(segment).toFixed(2) }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const previousDate = ref('2025-08-28 10:00')
const currentDate = ref('2025-09-01 12:00')

const previous = ref({
  Premium: 1.0,
  Standard: 1.0,
  Low: 1.0
})

const current = ref({
  Premium: 1.15,
  Standard: 0.95,
  Low: 0.9
})

const segments = ref(Object.keys(current.value))

function delta(segment) {
  return (current.value[segment] || 0) - (previous.value[segment] || 0)
}
</script>