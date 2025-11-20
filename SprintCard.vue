<template>
  <div>
    <SprintCard
      :demoImage="demoImage"
      :testStatus="testStatus"
      :tarih="tarih"
      @exported="handleExport"
    />

    <div class="tamamlananlar">
      <h3>Tamamlanan Sprintler</h3>
      <ul>
        <li v-for="(sprint, index) in tamamlananlar" :key="index">
          {{ sprint.tarih }} - Test Durumu: {{ sprint.test }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import SprintCard from './SprintCard.vue'

const demoImage = '/assets/sprint-demo.png'
const testStatus = 'Yeşil'
const tarih = '2025-08-19'

const tamamlananlar = ref([])

function handleExport(veri) {
  tamamlananlar.value.push(veri)
}
</script>

<style scoped>
.tamamlananlar {
  margin-top: 2rem;
  background: #f9f9f9;
  padding: 1rem;
  border-radius: 8px;
}
</style>