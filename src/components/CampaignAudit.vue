<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">🧾 Kampanya Denetim Kayıtları</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="fetchAuditLogs" class="btn btn-warning">Logları Getir</button>
    </div>

    <table class="table table-zebra w-full text-sm">
      <thead>
        <tr>
          <th>Zaman</th>
          <th>İşlem</th>
          <th>Tip</th>
          <th>Kullanıcı</th>
          <th>Durum</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in auditLogs" :key="log.id">
          <td>{{ log.timestamp }}</td>
          <td>{{ log.action }}</td>
          <td>
            <span :class="typeClass(log.type)">
              {{ log.type }}
            </span>
          </td>
          <td>{{ log.user }}</td>
          <td>{{ log.status }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'

type Campaign = { id: number; name: string }
type AuditLog = {
  id: number
  timestamp: string
  action: string
  type: 'info' | 'warning' | 'error'
  user: string
  status: string
}

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const auditLogs = ref<AuditLog[]>([])

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const fetchAuditLogs = async () => {
  if (!selectedId.value) return
  const { data } = await axios.get('/admin/campaign-audit', {
    params: { campaignId: selectedId.value }
  })
  auditLogs.value = data
}

const typeClass = (type: AuditLog['type']) => {
  return {
    info: 'text-blue-600 font-semibold',
    warning: 'text-yellow-600 font-semibold',
    error: 'text-red-600 font-semibold'
  }[type]
}

onMounted(fetchCampaigns)
</script>

<style scoped>
.table td {
  vertical-align: top;
}
</style>