<template>
  <div class="output-panel">
    <ModuleOutput :title="title" :output="output" />
    <ModuleOutputExport :title="title" :output="output" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import ModuleOutput from './ModuleOutput.vue'
import ModuleOutputExport from './ModuleOutputExport.vue'

const props = defineProps<{
  title: string
  endpoint: string
}>()

const output = ref<string | undefined>()

onMounted(async () => {
  try {
    const { data } = await axios.get(props.endpoint)
    output.value = data?.output ?? '⚠️ Veri boş geldi.'
  } catch {
    output.value = '❌ API bağlantısı başarısız.'
  }
})
</script>

<style scoped>
.output-panel {
  background-color: #fefefe;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  margin-bottom: 1.5rem;
  transition: transform 0.2s ease;
}
.output-panel:hover {
  transform: scale(1.01);
}
</style>