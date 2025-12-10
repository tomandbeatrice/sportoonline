<script setup lang="ts">
import { ref } from 'vue'

const segments = ref([
  {
    id: 'vip',
    name: 'VIP Müşteriler',
    count: 145,
    percentage: 12,
    trend: 'up',
    color: 'bg-purple-500',
    description: 'Yüksek harcama yapan ve sık alışveriş yapan sadık kitle.'
  },
  {
    id: 'loyal',
    name: 'Sadık Müşteriler',
    count: 450,
    percentage: 35,
    trend: 'stable',
    color: 'bg-blue-500',
    description: 'Düzenli alışveriş yapan güvenilir kitle.'
  },
  {
    id: 'potential',
    name: 'Potansiyel',
    count: 320,
    percentage: 25,
    trend: 'up',
    color: 'bg-emerald-500',
    description: 'Yeni kayıt olmuş veya ilk siparişini vermiş, büyümeye açık.'
  },
  {
    id: 'risk',
    name: 'Riskli (Churn)',
    count: 180,
    percentage: 15,
    trend: 'down',
    color: 'bg-orange-500',
    description: 'Son 90 gündür aktif olmayan, kaybedilmek üzere olanlar.'
  },
  {
    id: 'lost',
    name: 'Kaybedilen',
    count: 165,
    percentage: 13,
    trend: 'stable',
    color: 'bg-slate-400',
    description: 'Uzun süredir etkileşimi olmayan pasif hesaplar.'
  }
])

const selectedSegment = ref<string | null>(null)

const selectSegment = (id: string) => {
  selectedSegment.value = selectedSegment.value === id ? null : id
  // Emit event to filter parent list
}
</script>

<template>
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100">
      <h3 class="font-bold text-slate-800 flex items-center gap-2">
        <span class="text-indigo-600">🧩</span> AI Segmentasyon
      </h3>
      <p class="text-xs text-slate-500 mt-1">Müşteri tabanınızın yapay zeka ile analizi</p>
    </div>

    <div class="p-4">
      <!-- Visual Bar -->
      <div class="flex h-4 rounded-full overflow-hidden mb-6 cursor-pointer">
        <div 
          v-for="segment in segments" 
          :key="segment.id"
          class="h-full transition-all duration-300 hover:opacity-80 relative group"
          :class="segment.color"
          :style="{ width: segment.percentage + '%' }"
          @click="selectSegment(segment.id)"
        >
          <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10 pointer-events-none">
            {{ segment.name }} (%{{ segment.percentage }})
          </div>
        </div>
      </div>

      <!-- Legend / List -->
      <div class="space-y-3">
        <div 
          v-for="segment in segments" 
          :key="segment.id"
          @click="selectSegment(segment.id)"
          class="flex items-center justify-between p-3 rounded-lg border transition cursor-pointer"
          :class="selectedSegment === segment.id ? 'border-indigo-500 bg-indigo-50' : 'border-slate-100 hover:bg-slate-50'"
        >
          <div class="flex items-center gap-3">
            <div class="w-3 h-3 rounded-full" :class="segment.color"></div>
            <div>
              <div class="font-bold text-slate-900 text-sm">{{ segment.name }}</div>
              <div class="text-xs text-slate-500">{{ segment.description }}</div>
            </div>
          </div>
          <div class="text-right">
            <div class="font-bold text-slate-900">{{ segment.count }}</div>
            <div class="text-xs flex items-center justify-end gap-1" :class="segment.trend === 'up' ? 'text-emerald-600' : segment.trend === 'down' ? 'text-red-600' : 'text-slate-400'">
              <span v-if="segment.trend === 'up'">↑</span>
              <span v-if="segment.trend === 'down'">↓</span>
              <span v-if="segment.trend === 'stable'">-</span>
              %{{ segment.percentage }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
