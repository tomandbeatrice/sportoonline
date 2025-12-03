<template>
  <div class="contribution-panel">
    <h4>👥 Modül Katkı Skoru</h4>
    <ul>
      <li v-for="modul in contributions" :key="modul.name">
        {{ modul.name }} – Katkı: {{ modul.score }}%
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const contributions = ref([])

onMounted(async () => {
  const res = await fetch('/api/module/contribution')
  contributions.value = await res.json()
})
</script>

<style scoped>
.contribution-panel {
  background: #fff;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
</style>