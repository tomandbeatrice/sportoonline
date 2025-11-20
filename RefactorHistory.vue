<template>
  <Timeline :items="refactorLog" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Timeline from '@/components/Timeline.vue'
import { getRefactorHistory } from '@/services/riskEngine'

const refactorLog = ref([])

onMounted(async () => {
  refactorLog.value = await getRefactorHistory()
})
</script>