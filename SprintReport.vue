<template>
  <div>
    <div ref="raporRef" class="rapor">
      <h2>Sprint Raporu</h2>
      <p><strong>Tarih:</strong> {{ tarih }}</p>
      <p><strong>Test Durumu:</strong> {{ testStatus }}</p>
      <img :src="demoImage" alt="Demo Görseli" />
      <ul>
        <li>Demo: Render alındı</li>
        <li>Export: Buton aktif</li>
        <li>Katkı: Frontend %40, Backend %35, QA %25</li>
      </ul>

      <TeamScorePanel />
    </div>

    <button @click="exportPDF">PDF Olarak Kaydet</button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import html2pdf from 'html2pdf.js'
import TeamScorePanel from './TeamScorePanel.vue'
import { useRoadmapStore } from '@/stores/roadmapStore'

defineProps({
  tarih: String,
  testStatus: String,
  demoImage: String
})

const raporRef = ref(null)
const roadmap = useRoadmapStore()

function exportPDF() {
  const element = raporRef.value
  const options = {
    margin: 0.5,
    filename: `sprint-raporu-${tarih}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
  }

  html2pdf().set(options).from(element).save().then(() => {
    roadmap.markAsDone('sprint-8')
  })
}
</script>

<style scoped>
.rapor {
  background: #fff;
  padding: 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
}
.rapor img {
  width: 100%;
  max-width: 300px;
  margin-top: 1rem;
}
</style>