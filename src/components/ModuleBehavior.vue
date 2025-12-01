<template>
  <div class="behavior-panel">
    <h3>🧠 Davranış Analizi</h3>
    <BehaviorChart :data="behaviorData" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import BehaviorChart from './BehaviorChart.vue'

const behaviorData = ref([])

onMounted(async () => {
  const { data } = await axios.get('/api/behavior')
  behaviorData.value = data ?? []
})
</script>

<style scoped>
.behavior-panel {
  background: #fff;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}
</style>