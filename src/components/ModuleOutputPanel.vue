<template>
  <div class="output-panel">
    <ModuleOutput :title="title" :output="output" />
    <ModuleOutputExport :title="title" :output="output" />
  </div>
</template>

<script setup lang="ts">
import ModuleOutput from './ModuleOutput.vue'
import ModuleOutputExport from './ModuleOutputExport.vue'

const props = defineProps<{
  title: string
  output?: string
}>()
</script>

<style scoped>
.output-panel {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background-color: #fefefe;
  border-radius: 10px;
  box-shadow: 0 0 6px rgba(0,0,0,0.04);
  transition: all 0.3s ease;
}
.output-panel:hover {
  transform: scale(1.01);
}
</style>