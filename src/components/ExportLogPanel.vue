<template>
  <div>
    <h2>Export Log Paneli</h2>
    <select v-model="vendorId" @change="fetchLogs">
      <option value="">Tüm Vendorlar</option>
      <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
        {{ vendor.name }}
      </option>
    </select>

    <select v-model="segment" @change="fetchLogs">
      <option value="">Tüm Segmentler</option>
      <option value="campaign">Kampanya</option>
      <option value="feedback">Geri Bildirim</option>
      <option value="score">Skor</option>
    </select>

    <ul>
      <li v-for="log in logs" :key="log.id">
        <strong>{{ log.vendor_name }}</strong> — {{ log.segment }}  
        <span>({{ formatDate(log.exported_at) }})</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const logs = ref([])
const vendors = ref([])
const vendorId = ref('')
const segment = ref('')

onMounted(async () => {
  const res = await fetch('/api/exports/logs')
  logs.value = await res.json()

  const vendorRes = await fetch('/api/seller/list')
  vendors.value = await vendorRes.json()
})

async function fetchLogs() {
  const params = new URLSearchParams()
  if (vendorId.value) params.append('vendor_id', vendorId.value)
  if (segment.value) params.append('segment', segment.value)

  const res = await fetch(`/api/exports/logs?${params.toString()}`)
  logs.value = await res.json()
}

function formatDate(date) {
  return new Date(date).toLocaleString('tr-TR')
}
</script>