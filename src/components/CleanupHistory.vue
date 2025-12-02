<template>
  <div class="history-list">
    <h3>Cleanup Log Geçmişi</h3>
    <input v-model="search" placeholder="Hafta veya yıl ara..." />
    <ul>
      <li v-for="log in filteredLogs" :key="log.file">
        <strong>{{ log.timestamp }}</strong> – {{ log.deleted_count }} dosya silindi ({{ log.total_size_mb }} MB)
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const logs = ref([])
const search = ref('')

onMounted(async () => {
  const res = await fetch('/api/exports/cleanup-history')
  logs.value = await res.json()
})

const filteredLogs = computed(() =>
  logs.value.filter(log =>
    log.timestamp?.toLowerCase().includes(search.value.toLowerCase()) ||
    log.file?.toLowerCase().includes(search.value.toLowerCase())
  )
)
</script>

<style scoped>
.history-list {
  background: #fefefe;
  padding: 1rem;
  border-radius: 8px;
  margin-top: 2rem;
}
input {
  margin-bottom: 1rem;
  padding: 0.5rem;
  width: 100%;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  margin-bottom: 0.5rem;
}
</style>