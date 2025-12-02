<template>
  <OfflineExportButton @click="generateOfflinePDF" />
</template>

<script setup lang="ts">
import { generateOfflinePDF } from '@/services/pdfOffline'

function generateOfflinePDF() {
  generateOfflinePDF()
}
</script>