import { ref } from 'vue'
import type { ModülKarşılaştırmaVerisi } from '../types/comparisonTypes'

export function useModuleComparisonData() {
  const karşılaştırmaVerisi = ref<ModülKarşılaştırmaVerisi[]>([
    { id: 1, ad: 'Export Cleanup', süre: 120, hata: 1 },
    { id: 2, ad: 'Kategori Sistemi', süre: 95, hata: 3 },
    { id: 3, ad: 'Dashboard Grafikler', süre: 140, hata: 2 },
    { id: 4, ad: 'Detay Modal', süre: 110, hata: 1 }
  ])

  // Toplam süre ve hata analizi (opsiyonel görsel bileşen için)
  const toplamSüre = karşılaştırmaVerisi.value.reduce((toplam, modül) => toplam + modül.süre, 0)
  const toplamHata = karşılaştırmaVerisi.value.reduce((toplam, modül) => toplam + modül.hata, 0)

  return {
    karşılaştırmaVerisi,
    toplamSüre,
    toplamHata
  }
}