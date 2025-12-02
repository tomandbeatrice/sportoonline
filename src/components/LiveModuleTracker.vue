<template>
  <div class="tracker">
    <ModuleTrendGraph :data="liveData" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ModuleTrendGraph from '@/components/ModuleTrendGraph.vue'
import { fetchLiveModuleData } from '@/services/analytics'

const liveData = ref([])

onMounted(async () => {
  liveData.value = await fetchLiveModuleData()
})
</script>