<template>
  <div class="cockpit-wrapper">
    <h1>Sprint Cockpit</h1>
    <div class="card-grid">
      <SprintCard
        v-for="(item, index) in demoItems"
        :key="index"
        :demoImage="item.src"
        :testStatus="item.test"
        :tarih="item.tarih"
        @exported="handleExport"
      />
    </div>

    <div class="export-log">
      <h2>Export Edilen Sprintler</h2>
      <ul>
        <li v-for="(log, i) in exportLogs" :key="i">
          {{ log.tarih }} – {{ log.test }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import SprintCard from '@/components/SprintCard.vue'

const demoItems = [
  { src: '/images/sprint1.png', test: 'Yeşil', tarih: '2025-08-01' },
  { src: '/images/sprint2.webp', test: 'Sarı', tarih: '2025-08-08' },
  { src: '/images/sprint3.png', test: 'Kırmızı', tarih: '2025-08-15' }
]

const exportLogs = ref([])

function handleExport(data) {
  exportLogs.value.push(data)
}
</script>

<style scoped>
.cockpit-wrapper {
  padding: 2rem;
}
.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}
.export-log {
  margin-top: 3rem;
  background: #f9f9f9;
  padding: 1rem;
  border-radius: 8px;
}
</style>