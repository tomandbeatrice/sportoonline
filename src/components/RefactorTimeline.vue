<template>
  <TimelineChart :data="refactorEvents" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import TimelineChart from '@/components/TimelineChart.vue'
import { fetchRefactorEvents } from '@/services/riskLog'

const refactorEvents = ref([])

onMounted(async () => {
  refactorEvents.value = await fetchRefactorEvents()
})
</script>