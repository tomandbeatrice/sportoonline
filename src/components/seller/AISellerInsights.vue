<template>
  <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-100">
    <div class="flex items-center gap-2 mb-4">
      <span class="text-2xl">🤖</span>
      <h3 class="text-lg font-bold text-indigo-900">AI Satıcı Asistanı</h3>
    </div>

    <div v-if="loading" class="space-y-3">
      <div class="h-16 bg-white/50 rounded-xl animate-pulse"></div>
      <div class="h-16 bg-white/50 rounded-xl animate-pulse"></div>
    </div>

    <div v-else class="space-y-3">
      <div 
        v-for="(insight, index) in insights" 
        :key="index"
        class="bg-white p-4 rounded-xl shadow-sm border border-indigo-50 flex gap-4 transition hover:shadow-md"
      >
        <div 
          class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
          :class="getIconClass(insight.type)"
        >
          {{ getIcon(insight.type) }}
        </div>
        <div>
          <h4 class="font-semibold text-slate-900 text-sm">{{ insight.title }}</h4>
          <p class="text-slate-600 text-xs mt-1">{{ insight.description }}</p>
          <button 
            v-if="insight.action"
            @click="$emit('action', insight.action)"
            class="mt-2 text-xs font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1"
          >
            {{ insight.actionLabel }} →
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

interface Insight {
  type: 'opportunity' | 'warning' | 'trend'
  title: string
  description: string
  action?: string
  actionLabel?: string
}

const loading = ref(true)
const insights = ref<Insight[]>([])

const getIcon = (type: string) => {
  switch (type) {
    case 'opportunity': return '💡'
    case 'warning': return '⚠️'
    case 'trend': return '📈'
    default: return 'ℹ️'
  }
}

const getIconClass = (type: string) => {
  switch (type) {
    case 'opportunity': return 'bg-yellow-100 text-yellow-700'
    case 'warning': return 'bg-red-100 text-red-700'
    case 'trend': return 'bg-green-100 text-green-700'
    default: return 'bg-slate-100 text-slate-700'
  }
}

onMounted(async () => {
  // Simulate AI analysis
  setTimeout(() => {
    insights.value = [
      {
        type: 'trend',
        title: 'Koşu Ayakkabılarına İlgi Artıyor',
        description: 'Son 3 günde "Koşu Ayakkabısı" aramaları %45 arttı. Stoklarınızı kontrol edin.',
        action: 'view_products',
        actionLabel: 'Ürünleri Gör'
      },
      {
        type: 'warning',
        title: 'Düşük Stok Uyarısı',
        description: '3 popüler ürününüzde stok seviyesi kritik (5 adetin altında).',
        action: 'restock',
        actionLabel: 'Stok Ekle'
      },
      {
        type: 'opportunity',
        title: 'Fiyat Avantajı',
        description: 'Rakip analizine göre "Spor Çanta" kategorisinde fiyatlarınız piyasa ortalamasının %10 altında. Kâr marjını artırabilirsiniz.',
        action: 'update_prices',
        actionLabel: 'Fiyatları Güncelle'
      }
    ]
    loading.value = false
  }, 1500)
})
</script>
