<template>
  <div class="task-panel">
    <h3>Görev Önerileri</h3>
    <ul>
      <li v-for="task in tasks" :key="task.id">
        {{ task.owner }} → {{ task.title }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
const team = [
  { name: 'Engin', score: 92 },
  { name: 'Zeynep', score: 78 },
  { name: 'Baran', score: 85 }
]

const tasks = [
  { id: 1, title: 'Yeni modül entegrasyonu', owner: team[0].name },
  { id: 2, title: 'Test senaryosu yazımı', owner: team[2].name },
  { id: 3, title: 'Roadmap güncelleme mantığı', owner: team[1].name }
]
</script>

<style scoped>
.task-panel {
  margin-top: 2rem;
  background: #f3f3f3;
  padding: 16px;
  border-radius: 8px;
}
</style>