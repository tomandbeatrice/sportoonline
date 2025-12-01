import { ref } from 'vue'
import type { ModülBaşarıVerisi } from '../types/successRateTypes'

export function useSuccessRateData() {
  const modülVerisi = ref<ModülBaşarıVerisi[]>([
    { id: 1, ad: 'Export Cleanup', successCount: 12, failCount: 1 },
    { id: 2, ad: 'Kategori Sistemi', successCount: 9, failCount: 3 },
    { id: 3, ad: 'Dashboard Grafikler', successCount: 15, failCount: 2 },
    { id: 4, ad: 'Detay Modal', successCount: 8, failCount: 1 },
    { id: 5, ad: 'SuccessRateChart', successCount: 0, failCount: 0 },
    { id: 6, ad: 'ModuleComparison', successCount: 0, failCount: 0 },
    { id: 7, ad: 'RoadmapTracker', successCount: 1, failCount: 0 }
  ])

  return { modülVerisi }
}