<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Sigorta Yönetimi</h1>
          <p class="text-slate-500">Poliçeleri yönetin, hasar taleplerini inceleyin ve riskleri analiz edin.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-file-invoice text-slate-400"></i>
            Hasar Talepleri
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700">
            <i class="fas fa-plus"></i>
            Yeni Poliçe
          </button>
        </div>
      </div>

      <!-- Metrics Grid -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div v-for="metric in metrics" :key="metric.label" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">{{ metric.label }}</span>
            <div class="rounded-lg p-2" :class="metric.bgClass">
              <i class="fas" :class="[metric.icon, metric.textClass]"></i>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-2xl font-bold text-slate-900">{{ metric.value }}</span>
            <span class="ml-2 text-xs font-medium" :class="metric.trendUp ? 'text-emerald-600' : 'text-red-600'">
              {{ metric.trend }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Split View Content -->
    <div class="grid gap-6 lg:grid-cols-12">
      <!-- Left Panel: Policy List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Poliçe ara..." 
              class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"
            >
          </div>

          <!-- Filter Tabs -->
          <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              @click="activeTab = tab.id"
              class="whitespace-nowrap rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
              :class="activeTab === tab.id ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'"
            >
              {{ tab.label }}
              <span class="ml-1 opacity-60">({{ tab.count }})</span>
            </button>
          </div>

          <!-- List -->
          <div class="flex flex-col gap-3">
            <div 
              v-for="policy in filteredPolicies" 
              :key="policy.id"
              @click="selectedPolicy = policy"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedPolicy?.id === policy.id ? 'border-teal-500 bg-teal-50/30 ring-1 ring-teal-500' : 'border-slate-200 bg-white hover:border-teal-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                    <i class="fas fa-file-contract text-xl"></i>
                  </div>
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ policy.holder }}</h3>
                    <div class="flex items-center gap-1 text-xs text-slate-500">
                      <span>{{ policy.type }}</span>
                      <span class="mx-1">•</span>
                      <span>{{ policy.number }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[policy.status]"
                >
                  {{ statusLabels[policy.status] }}
                </span>
                <span class="text-xs font-medium text-slate-400">{{ policy.expiryDate }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedPolicy" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">Poliçe Detayları</h2>
                <div class="flex gap-2">
                  <button class="rounded-lg border border-slate-200 p-2 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                    <i class="fas fa-print"></i>
                  </button>
                  <button class="rounded-lg border border-slate-200 p-2 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                    <i class="fas fa-edit"></i>
                  </button>
                </div>
              </div>
              <div class="p-6">
                <div class="grid gap-6 sm:grid-cols-2">
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Sigortalı</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedPolicy.holder }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Poliçe No</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedPolicy.number }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Başlangıç Tarihi</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedPolicy.startDate }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Bitiş Tarihi</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedPolicy.expiryDate }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Prim Tutarı</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedPolicy.premium }}₺</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Teminat Tutarı</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedPolicy.coverage }}₺</p>
                  </div>
                </div>

                <div class="mt-8">
                  <h3 class="mb-4 text-sm font-semibold text-slate-900">Kapsam Detayları</h3>
                  <div class="space-y-3">
                    <div v-for="item in selectedPolicy.coverageDetails" :key="item" class="flex items-center gap-3">
                      <i class="fas fa-check text-emerald-500 text-sm"></i>
                      <span class="text-sm text-slate-600">{{ item }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Claims -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Hasar Geçmişi</h2>
              </div>
              <div class="p-6 space-y-4">
                <div v-if="selectedPolicy.claims.length > 0">
                  <div v-for="claim in selectedPolicy.claims" :key="claim.id" class="flex items-center justify-between border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                    <div>
                      <p class="text-sm font-semibold text-slate-900">{{ claim.type }}</p>
                      <p class="text-xs text-slate-500">{{ claim.date }} • {{ claim.amount }}₺</p>
                    </div>
                    <span 
                      class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                      :class="claim.status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'"
                    >
                      {{ claim.status === 'paid' ? 'Ödendi' : 'İnceleniyor' }}
                    </span>
                  </div>
                </div>
                <div v-else class="text-center py-4 text-sm text-slate-500">
                  Hasar kaydı bulunmamaktadır.
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <InsuranceAI 
              :policy="selectedPolicy" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-shield-alt text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Poliçe Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve yapay zeka analizini görmek için soldaki listeden bir poliçe seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import InsuranceAI from '@/components/admin/insurance/InsuranceAI.vue';

const policies = ref([]);
const loading = ref(false);

const metrics = ref([
  { label: 'Aktif Poliçe', value: '0', trend: '0', trendUp: true, icon: 'fa-file-contract', bgClass: 'bg-teal-100', textClass: 'text-teal-600' },
  { label: 'Toplam Prim', value: '0 ₺', trend: '0%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Hasar Oranı', value: '%0', trend: '0%', trendUp: true, icon: 'fa-chart-pie', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
  { label: 'Bekleyen Talep', value: '0', trend: '0', trendUp: false, icon: 'fa-clock', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
]);

const tabs = [
  { id: 'all', label: 'Tümü', count: 0 },
  { id: 'active', label: 'Aktif', count: 0 },
  { id: 'pending', label: 'Onay Bekleyen', count: 0 },
  { id: 'expired', label: 'Süresi Dolan', count: 0 },
];

const statusClasses = {
  active: 'bg-emerald-100 text-emerald-700',
  pending: 'bg-amber-100 text-amber-700',
  expired: 'bg-slate-100 text-slate-700',
  cancelled: 'bg-red-100 text-red-700'
};

const statusLabels = {
  active: 'Aktif',
  pending: 'Onay Bekliyor',
  expired: 'Süresi Doldu',
  cancelled: 'İptal Edildi'
};

// State
const searchQuery = ref('');
const activeTab = ref('all');
const selectedPolicy = ref(null);

// Computed
const filteredPolicies = computed(() => {
  return policies.value.filter(p => {
    const matchesSearch = p.holder.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          p.number.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || p.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

onMounted(async () => {
  loading.value = true;
  try {
    // Fetch Policies
    const response = await axios.get('/api/insurance');
    policies.value = response.data.data || response.data;
    
    if (policies.value.length > 0) {
      selectedPolicy.value = policies.value[0];
    }

    // Fetch Stats
    const statsResponse = await axios.get('/api/insurance/stats');
    const stats = statsResponse.data;
    
    metrics.value = [
      { label: 'Aktif Poliçe', value: stats.active_policies.toString(), trend: '+45', trendUp: true, icon: 'fa-file-contract', bgClass: 'bg-teal-100', textClass: 'text-teal-600' },
      { label: 'Toplam Prim', value: stats.total_premium + ' ₺', trend: '+12%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
      { label: 'Hasar Oranı', value: '%' + stats.claim_ratio, trend: '-2%', trendUp: true, icon: 'fa-chart-pie', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
      { label: 'Bekleyen Talep', value: stats.pending_claims.toString(), trend: '+3', trendUp: false, icon: 'fa-clock', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
    ];
    
    // Update tab counts
    tabs[0].count = policies.value.length;
    tabs[1].count = policies.value.filter(p => p.status === 'active').length;
    tabs[2].count = policies.value.filter(p => p.status === 'pending').length;
    tabs[3].count = policies.value.filter(p => p.status === 'expired').length;

  } catch (error) {
    console.error('Failed to fetch insurance data:', error);
  } finally {
    loading.value = false;
  }
});

// Methods
const handleAIAction = (action) => {
  console.log(`AI Action: ${action}`);
  // Implement action logic
};
</script>
