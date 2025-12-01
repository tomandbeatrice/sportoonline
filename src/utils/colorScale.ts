// 🔵 Yoğunluk Bazlı Renk Skalası (log sayısına göre hücre rengi)
export function getDensityColor(count: number): string {
  if (count === 0) return '#f3f4f6'       // gri - hiç log yok
  if (count < 5) return '#cbd5e1'         // açık mavi - düşük yoğunluk
  if (count < 15) return '#60a5fa'        // mavi - orta yoğunluk
  return '#1d4ed8'                        // koyu mavi - yüksek yoğunluk
}

// 🟢 Modül Bazlı Sabit Renk Skalası (grafik çizgileri için)
const modulColorMap: Record<string, string> = {
  Export: '#10b981',   // yeşil
  Log: '#3b82f6',      // mavi
  Kargo: '#f59e0b',    // turuncu
  Ödeme: '#ef4444',    // kırmızı
  Kullanıcı: '#8b5cf6', // mor (opsiyonel modül)
  Sistem: '#64748b',   // gri-mavi (opsiyonel modül)
}

export function getModulColor(modulName: string): string {
  return modulColorMap[modulName] || '#6b7280' // default: gri
}