<template>
  <div class="timeline">
    <h3>Roadmap Zaman Çizelgesi</h3>
    <ul>
      <li v-for="sprint in sprints" :key="sprint.id" :class="sprint.status">
        {{ sprint.title }} – {{ sprint.status === 'done' ? 'Tamamlandı' : 'Bekliyor' }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { useRoadmapStore } from '@/stores/roadmapStore'
const roadmap = useRoadmapStore()
const sprints = roadmap.sprints

// Grafik tooltip verisi için örnek yapı
const timelineData = sprints.map(s => ({
  label: s.title,
  start: new Date(s.start),
  end: new Date(s.end),
  successRate: s.successRate,
  moduleCount: s.moduleCount,
  errorCount: s.errorCount
}))

const tooltip = {
  formatter: (val: any, ctx: any) => {
    const data = timelineData[ctx.dataPointIndex]
    return `
      ${data.label}<br/>
      Başarı: %${data.successRate}<br/>
      Modül: ${data.moduleCount}<br/>
      Hata: ${data.errorCount}
    `
  }
}
</script>

<style scoped>
.timeline ul {
  list-style: none;
  padding: 0;
}
.timeline li {
  padding: 8px;
  border-left: 4px solid #ccc;
  margin-bottom: 8px;
}
.timeline li.done {
  border-color: #00b894;
  color: #2d3436;
}
.timeline li.pending {
  border-color: #d63031;
  color: #636e72;
}
</style>