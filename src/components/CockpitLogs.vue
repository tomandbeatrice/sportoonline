<template>
  <div>
    <h2>Cockpit Log Paneli</h2>
    <input v-model="search" placeholder="Sipariş ID veya mesaj ara" @input="fetchLogs" />
    <select v-model="status" @change="fetchLogs">
      <option value="">Tümü</option>
      <option value="confirmed">Onaylandı</option>
      <option value="shipped">Kargoya Verildi</option>
      <option value="delivered">Teslim Edildi</option>
    </select>

    <ul>
      <li v-for="log in logs" :key="log.id">
        <strong>#{{ log.order_id }}</strong> — {{ log.message }}  
        <span>({{ formatDate(log.created_at) }})</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const logs = ref([])
const search = ref('')
const status = ref('')

onMounted(fetchLogs)

async function fetchLogs() {
  const params = new URLSearchParams()
  if (search.value) params.append('search', search.value)
  if (status.value) params.append('status', status.value)

  const res = await fetch(`/api/module-logs?${params.toString()}`)
  logs.value = await res.json()
}

function formatDate(date) {
  return new Date(date).toLocaleString('tr-TR')
}
</script>