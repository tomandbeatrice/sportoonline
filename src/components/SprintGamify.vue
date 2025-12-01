<template>
  <GamifyBoard :members="teamStats" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import GamifyBoard from '@/components/GamifyBoard.vue'
import { getTeamStats } from '@/services/motivationTracker'

const teamStats = ref([])

onMounted(async () => {
  teamStats.value = await getTeamStats()
})
</script>