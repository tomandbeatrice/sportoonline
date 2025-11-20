<template>
  <div class="cockpit">
    <h2>Cockpit Paneli</h2>

    <section>
      <h3>Export Log Durumu</h3>
      <ul>
        <li v-for="log in exportLogs" :key="log.id">
          {{ log.filename }} — {{ log.status }} — {{ formatDate(log.created_at) }}
        </li>
      </ul>
    </section>

    <section>
      <h3>Segment Planlama</h3>
      <ul>
        <li v-for="segment in segments" :key="segment.id">
          {{ segment.name }} — {{ segment.rule }}
        </li>
      </ul>
    </section>

    <section>
      <h3>Kampanya Önerileri</h3>
      <ul>
        <li v-for="campaign in campaigns" :key="campaign.id">
          {{ campaign.title }} — {{ campaign.discount }}%
        </li>
      </ul>
    </section>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface ExportLog {
  id: number
  filename: string
  status: string
  created_at: string
}

interface Segment {
  id: number
  name: string
  rule: string
}

interface Campaign {
  id: number
  title: string
  discount: number
}

const exportLogs = ref<ExportLog[]>([])
const segments = ref<Segment[]>([])
const campaigns = ref<Campaign[]>([])

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleString('tr-TR')
}

onMounted(async () => {
  const [logRes, segmentRes, campaignRes] = await Promise.all([
    axios.get('/api/export-logs'),
    axios.get('/api/segments'),
    axios.get('/api/campaigns')
  ])
  exportLogs.value = logRes.data
  segments.value = segmentRes.data
  campaigns.value = campaignRes.data
})
</script>

<style scoped>
.cockpit {
  padding: 2rem;
}
section {
  margin-bottom: 2rem;
}
</style>