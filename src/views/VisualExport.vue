<template>
  <div ref="exportRef" class="export-area">
    <h3>Sprint Export Paneli</h3>
    <img :src="props.demoImage" alt="Sprint Görseli" />
    <p>{{ props.summary }}</p>
    <button @click="exportPDF">PDF Olarak Dışa Aktar</button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import html2pdf from 'html2pdf.js'
import { sendExportToExternalSystem } from '@/services/exportService'

const props = defineProps<{ demoImage: string; summary: string }>()

const exportRef = ref<HTMLElement | null>(null)

async function exportPDF() {
  if (exportRef.value) {
    html2pdf().from(exportRef.value).save()
    await sendExportToExternalSystem({
      summary: props.summary,
      imageUrl: props.demoImage
    })
  }
}
</script>

<style scoped>
.export-area {
  background: #fff;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.export-area img {
  max-width: 100%;
  margin-bottom: 12px;
}
</style>