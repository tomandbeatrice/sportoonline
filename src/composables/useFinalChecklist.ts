import { ref } from 'vue'
import type { ModülTestDurumu } from '../types/finalTypes'

export function useFinalChecklist() {
  const modüller = ref<ModülTestDurumu[]>([
    {
      id: 1,
      ad: 'Export Cleanup',
      aciklama: 'Dosya silme, loglama ve dashboard entegrasyonu test edildi.',
      testEdildi: true
    },
    {
      id: 2,
      ad: 'Kategori Sistemi',
      aciklama: 'Admin panelde kategori ekleme/silme API ile test edildi.',
      testEdildi: true
    },
    {
      id: 3,
      ad: 'Dashboard Grafikler',
      aciklama: 'Canlı veri akışı ile grafik render kontrolü yapıldı.',
      testEdildi: true
    },
    {
      id: 4,
      ad: 'Detay Modal',
      aciklama: 'Hata logları, zaman çizelgesi ve veri detayları test edildi.',
      testEdildi: true
    },
    {
      id: 5,
      ad: 'RoadmapTracker',
      aciklama: 'Modül ilerleme yüzdesi ve checklist uyumu kontrol edildi.',
      testEdildi: false
    },
    {
      id: 6,
      ad: 'SuccessRateChart',
      aciklama: 'Başarı/hata oranları bar chart ile test edilecek.',
      testEdildi: false
    },
    {
      id: 7,
      ad: 'ModuleComparison',
      aciklama: 'Radar chart ile süre, hata ve başarı karşılaştırması yapılacak.',
      testEdildi: false
    }
  ])

  return { modüller }
}