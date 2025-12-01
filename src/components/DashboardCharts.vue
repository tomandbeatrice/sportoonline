<template>
  <div class="dashboard-charts">
    <div
      v-for="mod in modules"
      :key="mod.module"
      class="chart-block"
      :class="getStatusClass(mod.progress)"
    >
      <div class="header">
        <h3>{{ mod.module.toUpperCase() }}</h3>
        <span class="progress">{{ mod.progress }}%</span>
      </div>
      <ChartBlock :data="mod.data" :module="mod.module" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ChartBlock from './ChartBlock.vue'

const modules = ref([])

onMounted(async () => {
  try {
    const res = await fetch('/api/module-logs')
    modules.value = await res.json()
  } catch (err) {
    console.error('Veri alınamadı:', err)
  }
})

function getStatusClass(progress) {
  if (progress >= 90) return 'status-complete'
  if (progress >= 60) return 'status-progress'
  if (progress >= 30) return 'status-warning'
  return 'status-error'
}
</script>

<style scoped>
.dashboard-charts {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
.chart-block {
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 0 8px rgba(0,0,0,0.1);
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.progress {
  font-weight: bold;
  font-size: 1.1rem;
}
.status-complete {
  background-color: #e0f7fa;
  border-left: 6px solid #00acc1;
}
.status-progress {
  background-color: #fffde7;
  border-left: 6px solid #fbc02d;
}
.status-warning {
  background-color: #fff3e0;
  border-left: 6px solid #fb8c00;
}
.status-error {
  background-color: #ffebee;
  border-left: 6px solid #e53935;
}
</style>