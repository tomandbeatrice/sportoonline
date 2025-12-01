<template>
  <div class="score-panel">
    <h3>Katkı Skoru</h3>
    <ul>
      <li v-for="member in team" :key="member.name">
        {{ member.name }} – {{ member.score }} puan
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
</script>

<style scoped>
.score-panel {
  background: #f4f6f8;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  font-family: 'Segoe UI', sans-serif;
}
.score-panel h3 {
  margin-bottom: 12px;
  color: #333;
  font-size: 18px;
}
.score-panel ul {
  list-style: none;
  padding: 0;
}
.score-panel li {
  padding: 6px 0;
  font-size: 15px;
  color: #555;
}
</style>