<template>
  <div class="campaign-management space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Kampanya Yönetimi</h1>
        <p class="text-slate-500">Yapay zeka destekli kampanya analizi ve otomasyonu</p>
      </div>
      <div class="flex gap-3">
        <button class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition flex items-center gap-2">
          <span>📊</span> Rapor İndir
        </button>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center gap-2">
          <span>+</span> Yeni Kampanya
        </button>
      </div>
    </div>

    <!-- AI Analysis Section -->
    <section>
      <CampaignAIAnalysis />
    </section>

    <div class="grid lg:grid-cols-3 gap-8">
      <!-- AI Automation Wizard -->
      <div class="lg:col-span-1">
        <CampaignAutomation />
      </div>

      <!-- Campaign List -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden h-full">
          <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-slate-900">Aktif Kampanyalar</h3>
            <div class="flex gap-2">
              <button class="p-2 hover:bg-slate-100 rounded-lg text-slate-400 transition">🔍</button>
              <button class="p-2 hover:bg-slate-100 rounded-lg text-slate-400 transition">⚡</button>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-6 py-4 font-semibold text-slate-700">Kampanya</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">Durum</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">Performans</th>
                  <th class="px-6 py-4 font-semibold text-slate-700 text-right">İşlem</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="campaign in campaigns" :key="campaign.id" class="hover:bg-slate-50 transition group">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                        {{ campaign.icon }}
                      </div>
                      <div>
                        <div class="font-medium text-slate-900">{{ campaign.name }}</div>
                        <div class="text-xs text-slate-500">{{ campaign.dateRange }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span 
                      class="px-2.5 py-1 rounded-full text-xs font-medium border"
                      :class="{
                        'bg-emerald-50 text-emerald-700 border-emerald-100': campaign.status === 'active',
                        'bg-amber-50 text-amber-700 border-amber-100': campaign.status === 'scheduled',
                        'bg-slate-50 text-slate-600 border-slate-100': campaign.status === 'ended'
                      }"
                    >
                      {{ getStatusLabel(campaign.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                      <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Hedef: %{{ campaign.target }}</span>
                        <span class="font-bold text-slate-700">%{{ campaign.progress }}</span>
                      </div>
                      <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div 
                          class="h-full rounded-full transition-all duration-1000"
                          :class="campaign.progress >= campaign.target ? 'bg-emerald-500' : 'bg-indigo-500'"
                          :style="{ width: (campaign.progress / campaign.target * 100) + '%' }"
                        ></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <button class="text-slate-400 hover:text-indigo-600 transition opacity-0 group-hover:opacity-100">
                      Düzenle
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import CampaignAIAnalysis from '@/components/admin/campaign/CampaignAIAnalysis.vue'
import CampaignAutomation from '@/components/admin/campaign/CampaignAutomation.vue'

const campaigns = ref([
  { 
    id: 1, 
    name: 'Yaz Sonu İndirimi', 
    icon: '☀️',
    type: 'discount', 
    dateRange: '1-15 Eylül', 
    progress: 85, 
    target: 100,
    status: 'active' 
  },
  { 
    id: 2, 
    name: 'Okula Dönüş', 
    icon: '🎒',
    type: 'collection', 
    dateRange: '15-30 Eylül', 
    progress: 12, 
    target: 100,
    status: 'active' 
  },
  { 
    id: 3, 
    name: 'Black Friday Hazırlık', 
    icon: '🖤',
    type: 'special', 
    dateRange: 'Kasım 2024', 
    progress: 0, 
    target: 100,
    status: 'scheduled' 
  },
  { 
    id: 4, 
    name: 'Kış Koleksiyonu Lansmanı', 
    icon: '❄️',
    type: 'launch', 
    dateRange: 'Ekim 2024', 
    progress: 0, 
    target: 100,
    status: 'scheduled' 
  },
  { 
    id: 5, 
    name: 'Outlet Temizliği', 
    icon: '🏷️',
    type: 'clearance', 
    dateRange: 'Ağustos 2024', 
    progress: 98, 
    target: 100,
    status: 'ended' 
  }
])

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    active: 'Yayında',
    scheduled: 'Planlandı',
    pending: 'Onay Bekliyor',
    ended: 'Tamamlandı'
  }
  return labels[status] || status
}
</script>
