import { ref } from 'vue'
import type { ModülDurumu } from './roadmapTypes'

export function useRoadmapData() {
  const modüller = ref<ModülDurumu[]>([
    { id: 1, ad: 'Export Cleanup', yüzde: 100 },
    { id: 2, ad: 'Kategori Sistemi', yüzde: 100 },
    { id: 3, ad: 'Dashboard Grafikler', yüzde: 100 },
    { id: 4, ad: 'Detay Modal', yüzde: 100 },
    { id: 5, ad: 'SuccessRateChart', yüzde: 100 },
    { id: 6, ad: 'ModuleComparison', yüzde: 0 },
    { id: 7, ad: 'RoadmapTracker', yüzde: 10 }
  ])

  return { modüller }
}