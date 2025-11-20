<template>
  <PriorityList :items="rankedTasks" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import PriorityList from '@/components/PriorityList.vue'
import { rankRoadmapTasks } from '@/services/roadmapAI'

const rankedTasks = ref([])

onMounted(async () => {
  rankedTasks.value = await rankRoadmapTasks()
})
</script>