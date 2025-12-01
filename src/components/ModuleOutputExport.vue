<template>
  <div class="export-wrapper">
    <button @click="exportOutput" :disabled="!output">
      📤 Export Et
    </button>
    <span v-if="!output" class="hint">
      ⏳ Çıktı hazır olduğunda export aktif olur
    </span>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  output?: string
  title: string
}>()

const exportOutput = () => {
  const content = props.output?.trim()
  if (!content) return

  const blob = new Blob([content], {
    type: 'text/plain;charset=utf-8',
  })

  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `${props.title.replace(/\s+/g, '_')}.txt`
  link.click()
}
</script>

<style scoped>
.export-wrapper {
  margin-top: 0.5rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
button {
  background-color: #0078d4;
  color: white;
  border: none;
  padding: 0.6rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.95rem;
  transition: background 0.3s ease;
}
button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}
button:hover:not(:disabled) {
  background-color: #005fa3;
}
.hint {
  margin-top: 0.3rem;
  font-size: 0.85rem;
  color: #888;
}
</style>