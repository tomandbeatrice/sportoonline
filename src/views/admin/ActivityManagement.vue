<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Aktivite Yönetimi</h1>
          <p class="text-slate-500">Etkinlikleri planlayın, katılımcıları yönetin ve güvenliği sağlayın.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-clipboard-check text-slate-400"></i>
            Denetim Kayıtları
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-rose-700">
            <i class="fas fa-plus"></i>
            Yeni Aktivite
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
      <!-- Left Panel: Activity List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Aktivite ara..." 
              class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500"
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
              v-for="activity in filteredActivities" 
              :key="activity.id"
              @click="selectedActivity = activity"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedActivity?.id === activity.id ? 'border-rose-500 bg-rose-50/30 ring-1 ring-rose-500' : 'border-slate-200 bg-white hover:border-rose-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <img :src="activity.image" class="h-12 w-12 rounded-lg object-cover bg-slate-100" alt="Activity" />
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ activity.name }}</h3>
                    <div class="flex items-center gap-1 text-xs text-amber-500">
                      <i class="fas fa-star"></i>
                      <span class="font-medium text-slate-700">{{ activity.rating }}</span>
                      <span class="text-slate-400">({{ activity.reviews }})</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[activity.status]"
                >
                  {{ statusLabels[activity.status] }}
                </span>
                <span class="text-xs font-medium text-slate-400">{{ activity.category }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedActivity" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">Aktivite Detayları</h2>
                <div class="flex gap-2">
                  <button class="rounded-lg border border-slate-200 p-2 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                    <i class="fas fa-external-link-alt"></i>
                  </button>
                  <button class="rounded-lg border border-slate-200 p-2 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                    <i class="fas fa-edit"></i>
                  </button>
                </div>
              </div>
              <div class="p-6">
                <div class="flex flex-col md:flex-row gap-6">
                  <img :src="selectedActivity.image" class="h-32 w-full md:w-48 rounded-xl object-cover bg-slate-100" alt="Activity Image" />
                  <div class="flex-1 grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Aktivite Adı</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedActivity.name }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Kategori</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedActivity.category }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Zorluk Seviyesi</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedActivity.difficulty }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Fiyat</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedActivity.price }}₺</p>
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                      <label class="text-xs font-medium text-slate-500">Açıklama</label>
                      <p class="text-sm text-slate-600 leading-relaxed">{{ selectedActivity.description }}</p>
                    </div>
                  </div>
                </div>

                <div class="mt-8">
                  <h3 class="mb-4 text-sm font-semibold text-slate-900">Gerekli Ekipmanlar</h3>
                  <div class="flex flex-wrap gap-2">
                    <span 
                      v-for="gear in selectedActivity.gear" 
                      :key="gear"
                      class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700"
                    >
                      {{ gear }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Participants -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Son Katılımcılar</h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                      CY
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Can Yılmaz</p>
                      <p class="text-xs text-slate-500">Bugün, 14:00</p>
                    </div>
                  </div>
                  <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Katıldı</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                      DA
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Deniz Aksoy</p>
                      <p class="text-xs text-slate-500">Yarın, 10:00</p>
                    </div>
                  </div>
                  <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">Kayıtlı</span>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <ActivityAI 
              :activity="selectedActivity" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-running text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Aktivite Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve yapay zeka analizini görmek için soldaki listeden bir aktivite seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import ActivityAI from '@/components/admin/activity/ActivityAI.vue';

const activities = ref([]);
const loading = ref(false);

const metrics = ref([
  { label: 'Aktif Aktivite', value: '0', trend: '0', trendUp: true, icon: 'fa-running', bgClass: 'bg-rose-100', textClass: 'text-rose-600' },
  { label: 'Günlük Katılım', value: '0', trend: '0%', trendUp: true, icon: 'fa-users', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Güvenlik Skoru', value: '0', trend: '0', trendUp: true, icon: 'fa-shield-alt', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
  { label: 'Ciro', value: '0 ₺', trend: '0%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
]);

const tabs = [
  { id: 'all', label: 'Tümü', count: 0 },
  { id: 'active', label: 'Aktif', count: 0 },
  { id: 'pending', label: 'Onay Bekleyen', count: 0 },
];

const statusClasses = {
  active: 'bg-emerald-100 text-emerald-700',
  pending: 'bg-amber-100 text-amber-700',
  closed: 'bg-red-100 text-red-700'
};

const statusLabels = {
  active: 'Aktif',
  pending: 'Onay Bekliyor',
  closed: 'Kapalı'
};

// State
const searchQuery = ref('');
const activeTab = ref('all');
const selectedActivity = ref(null);

// Computed
const filteredActivities = computed(() => {
  return activities.value.filter(a => {
    const matchesSearch = a.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          a.category.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || a.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

onMounted(async () => {
  loading.value = true;
  try {
    // Fetch Activities
    const response = await axios.get('/api/activities');
    activities.value = response.data.data || response.data;
    
    if (activities.value.length > 0) {
      selectedActivity.value = activities.value[0];
    }

    // Fetch Stats
    const statsResponse = await axios.get('/api/activities/stats');
    const stats = statsResponse.data;
    
    metrics.value = [
      { label: 'Aktif Aktivite', value: stats.active_activities.toString(), trend: '+4', trendUp: true, icon: 'fa-running', bgClass: 'bg-rose-100', textClass: 'text-rose-600' },
      { label: 'Günlük Katılım', value: stats.daily_participation.toString(), trend: '+12%', trendUp: true, icon: 'fa-users', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
      { label: 'Güvenlik Skoru', value: stats.safety_score.toString(), trend: '+0.1', trendUp: true, icon: 'fa-shield-alt', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
      { label: 'Ciro', value: stats.revenue + ' ₺', trend: '+5%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
    ];
    
    // Update tab counts
    tabs[0].count = activities.value.length;
    tabs[1].count = activities.value.filter(a => a.status === 'active').length;
    tabs[2].count = activities.value.filter(a => a.status === 'pending').length;

  } catch (error) {
    console.error('Failed to fetch activity data:', error);
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
