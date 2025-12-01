<template>
  <div class="sprint-card">
    <div class="sprint-info">
      <img :src="demoImage" alt="Sprint Demo" />
      <p>Test Status: {{ testStatus }}</p>
      <p>Date: {{ tarih }}</p>
    </div>

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