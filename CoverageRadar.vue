<template>
  <RadarChart :data="coverageData" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import RadarChart from '@/components/RadarChart.vue'
import { fetchCoverageStats } from '@/services/testService'

const coverageData = ref([])

onMounted(async () => {
  coverageData.value = await fetchCoverageStats()
})
</script>