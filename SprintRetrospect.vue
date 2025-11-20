<template>
  <RetrospectDashboard :data="retrospectData" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import RetrospectDashboard from '@/components/RetrospectDashboard.vue'
import { fetchRetrospectStats } from '@/services/sprintService'

const retrospectData = ref([])

onMounted(async () => {
  retrospectData.value = await fetchRetrospectStats()
})
</script>