<template>
  <GitTimeline :items="gitLog" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import GitTimeline from '@/components/GitTimeline.vue'
import { fetchGitActivity } from '@/services/gitService'

const gitLog = ref([])

onMounted(async () => {
  gitLog.value = await fetchGitActivity()
})
</script>