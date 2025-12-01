<template>
  <div class="card bg-white shadow-md p-4 mb-6">
    <h2 class="text-lg font-semibold mb-4">🔥 Modül & Gün Bazlı Yoğunluk Haritası</h2>
    <table class="table w-full text-center">
      <thead>
        <tr>
          <th>Modül</th>
          <th v-for="day in last7Days" :key="day">{{ day }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="modul in modulList" :key="modul">
          <td class="font-semibold">{{ modul }}</td>
          <td
            v-for="day in last7Days"
            :key="day"
            :style="{ backgroundColor: getColor(modulStats.value[modul]?.[day] || 0) }"
            class="text-sm font-medium cursor-pointer hover:scale-[1.03] transition"
            @click="handleCellClick(modul, day)"
          >
            {{ modulStats.value[modul]?.[day] || 0 }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

// Props ve Emit tanımı
defineProps<{
  logs: Array<{
    message: string
    created_at: string
    type: string
    user_id: number
    status: string
  }>
}>()

const emit = defineEmits<{
  (e: 'select', logs: Array<any>): void
}>()

// Modül anahtar kelimeleri
const moduleKeywords: Record<string, string[]> = {
  Export: ['export', 'csv', 'xlsx'],
  Payment: ['payment', 'checkout', 'card'],
  Shipment: ['shipment', 'cargo', 'tracking']
}

const modulList = Object.keys(moduleKeywords)

// Son 7 günün tarihleri
const last7Days = [...Array(7)].map((_, i) => {
  const date = new Date()
  date.setDate(date.getDate() - (6 - i))
  return date.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit' })
})

// Sağlam keyword eşleşmesi
const keywordMatch = (message: string, keywords: string[]) =>
  keywords.some(k => new RegExp(`\\b${k}\\b`, 'i').test(message))

// Modül & gün bazlı log sayısını hesapla
const modulStats = computed(() => {
  const stats: Record<string, Record<string, number>> = {}
  modulList.forEach(modul => {
    stats[modul] = Object.fromEntries(last7Days.map(day => [day, 0]))
  })

  logs.forEach(log => {
    const day = new Date(log.created_at).toLocaleDateString('tr-TR', {
      day: '2-digit',
      month: '2-digit'
    })

    for (const [modul, keywords] of Object.entries(moduleKeywords)) {
      if (keywordMatch(log.message, keywords)) {
        stats[modul][day]++
      }
    }
  })

  return stats
})

// Hücre rengi yoğunluğa göre
function getColor(count: number): string {
  if (count === 0) return '#ecf0f1'
  if (count <= 3) return '#f1c40f'
  if (count <= 6) return '#e67e22'
  return '#e74c3c'
}

// Hücreye tıklanınca ilgili logları dışarıya gönder
function handleCellClick(modul: string, day: string) {
  const filteredLogs = logs.filter(log =>
    keywordMatch(log.message, moduleKeywords[modul]) &&
    new Date(log.created_at).toLocaleDateString('tr-TR', {
      day: '2-digit',
      month: '2-digit'
    }) === day
  )

  emit('select', filteredLogs)
}
</script>