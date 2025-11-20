<template>
  <div class="output-box">
    <h3>{{ title }}</h3>
    <pre v-if="output">{{ output }}</pre>
    <span v-else class="pending">⏳ Çıktı bekleniyor...</span>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string
  output?: string
}>()
</script>

<style scoped>
.output-box {
  background-color: var(--card, #f5f5f5);
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
}
.output-box:hover {
  transform: scale(1.01);
}
h3 {
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
  color: var(--text-primary, #333);
}
pre {
  background-color: #fff;
  padding: 0.75rem;
  border-radius: 6px;
  font-family: monospace;
  white-space: pre-wrap;
  word-break: break-word;
  border: 1px solid #ddd;
}
.pending {
  color: orange;
  font-style: italic;
  font-size: 0.95rem;
}
</style>