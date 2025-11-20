<template>
  <div class="risk-score-panel">
    <h4>⚠️ Modül Risk Skoru</h4>
    <ul>
      <li v-for="modul in riskStats" :key="modul.name">
        {{ modul.name }} – Risk: {{ modul.risk }}%
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const riskStats = ref([])

onMounted(async () => {
  const res = await fetch('/api/module/risk')
  riskStats.value = await res.json()
})
</script>

<style scoped>
.risk-score-panel {
  background-color: #fff;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}
</style>