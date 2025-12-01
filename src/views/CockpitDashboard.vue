<template>
  <main class="cockpit-dashboard">
    <!-- Üst Panel: Trend, Export, Risk -->
    <ModuleTrend />
    <ExportPanel />
    <ModuleRisk />

    <!-- Davranış, Motivasyon ve Risk Paneli -->
    <section class="insight-section">
      <ModuleBehavior />
      <MotivationPulse :modulStats="modulStats" />
      <ModuleRiskScore />
    </section>

    <!-- AI Destekli Öneri Paneli -->
    <section class="suggestion-section">
      <SuggestionBox />
    </section>

    <!-- Modül Çıktı Paneli -->
    <section class="output-section">
      <h2>📊 Sprint Cockpit – Modül Çıktıları</h2>

      <ModuleOutputAutoPanel
        title="Ürün Modülü Çıktısı"
        endpoint="/api/module/product"
      />
      <ModuleOutputAutoPanel
        title="Kategori Modülü Çıktısı"
        endpoint="/api/module/category"
      />
    </section>
  </main>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ModuleTrend from '@/components/ModuleTrend.vue'
import ExportPanel from '@/components/ExportPanel.vue'
import ModuleRisk from '@/components/ModuleRisk.vue'
import ModuleBehavior from '@/components/ModuleBehavior.vue'
import MotivationPulse from '@/components/MotivationPulse.vue'
import ModuleRiskScore from '@/components/ModuleRiskScore.vue'
import SuggestionBox from '@/components/SuggestionBox.vue'
import ModuleOutputAutoPanel from './ModuleOutputAutoPanel.vue'

const modulStats = ref([])

onMounted(async () => {
  const res = await fetch('/api/module/stats')
  modulStats.value = await res.json()
})
</script>

<style scoped>
.cockpit-dashboard {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  padding: 2rem;
  background-color: #f0f2f5;
}

.output-section,
.insight-section,
.suggestion-section {
  background-color: #f9f9f9;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
}

.output-section h2 {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  color: #333;
}
</style>