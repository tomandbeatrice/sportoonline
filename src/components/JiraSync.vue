<template>
  <JiraStatus :items="jiraTasks" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import JiraStatus from '@/components/JiraStatus.vue'
import { syncWithJira } from '@/services/jiraService'

const jiraTasks = ref([])

onMounted(async () => {
  jiraTasks.value = await syncWithJira()
})
</script>