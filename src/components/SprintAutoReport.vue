<template>
  <div v-if="reportReady">
    <DownloadPDF :data="reportData" />
    <DownloadCSV :data="reportData" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchSprintReport } from '@/services/reportService'
import DownloadPDF from '@/components/DownloadPDF.vue'
import DownloadCSV from '@/components/DownloadCSV.vue'

const reportData = ref(null)
const reportReady = ref(false)

onMounted(async () => {
  reportData.value = await fetchSprintReport()
  reportReady.value = true
})
</script>