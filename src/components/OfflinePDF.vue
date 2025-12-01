<template>
  <OfflineExportButton @click="generateOfflinePDF" />
</template>

<script setup lang="ts">
import { generateOfflinePDF as generatePDF } from '@/services/pdfOffline'

function generateOfflinePDF() {
  generatePDF()
}
</script>