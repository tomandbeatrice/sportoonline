import { ref } from 'vue'
import type { ModülKarşılaştırmaVerisi } from './comparisonTypes'

export function useComparisonData() {
  const modülVerisi = ref<ModülKarşılaştırmaVerisi[]>([
    { id: 1, ad: 'Export Cleanup', duration: 1200, errorCount: 1, successRate: 92 },
    { id: 2, ad: 'Kategori Sistemi', duration: 800, errorCount: 0, successRate: 100 },
    { id: 3, ad: 'Dashboard Grafikler', duration: 1500, errorCount: 2, successRate: 88 },
    { id: 4, ad: 'Detay Modal', duration: 950, errorCount: 1, successRate: 90 },
    { id: 5, ad: 'RoadmapTracker', duration: 400, errorCount: 0, successRate: 100 }
  ])

  return { modülVerisi }
}