<template>
  <PulseChart :data="teamPulseData" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import PulseChart from '@/components/PulseChart.vue'
import { fetchTeamPulse } from '@/services/motivationEngine'

const teamPulseData = ref([])

onMounted(async () => {
  teamPulseData.value = await fetchTeamPulse()
})
</script>