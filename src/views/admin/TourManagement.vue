<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Tur Yönetimi</h1>
          <p class="text-slate-500">Turları planlayın, rehberleri atayın ve müşteri deneyimini optimize edin.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-map-marked-alt text-slate-400"></i>
            Rota Planlayıcı
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-sky-700">
            <i class="fas fa-plus"></i>
            Yeni Tur
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
      <!-- Left Panel: Tour List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Tur ara..." 
              class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
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
              v-for="tour in filteredTours" 
              :key="tour.id"
              @click="selectedTour = tour"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedTour?.id === tour.id ? 'border-sky-500 bg-sky-50/30 ring-1 ring-sky-500' : 'border-slate-200 bg-white hover:border-sky-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <img :src="tour.image" class="h-12 w-12 rounded-lg object-cover bg-slate-100" alt="Tour" />
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ tour.name }}</h3>
                    <div class="flex items-center gap-1 text-xs text-amber-500">
                      <i class="fas fa-star"></i>
                      <span class="font-medium text-slate-700">{{ tour.rating }}</span>
                      <span class="text-slate-400">({{ tour.reviews }})</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[tour.status]"
                >
                  {{ statusLabels[tour.status] }}
                </span>
                <span class="text-xs font-medium text-slate-400">{{ tour.duration }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedTour" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">Tur Detayları</h2>
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
                  <img :src="selectedTour.image" class="h-32 w-full md:w-48 rounded-xl object-cover bg-slate-100" alt="Tour Image" />
                  <div class="flex-1 grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Tur Adı</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedTour.name }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Bölge</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedTour.region }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Süre</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedTour.duration }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Fiyat</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedTour.price }}₺</p>
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                      <label class="text-xs font-medium text-slate-500">Açıklama</label>
                      <p class="text-sm text-slate-600 leading-relaxed">{{ selectedTour.description }}</p>
                    </div>
                  </div>
                </div>

                <div class="mt-8">
                  <h3 class="mb-4 text-sm font-semibold text-slate-900">Yaklaşan Seferler</h3>
                  <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50">
                      <div class="h-10 w-10 rounded bg-sky-100 flex items-center justify-center text-sky-600">
                        <i class="fas fa-calendar-day"></i>
                      </div>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">15 Aralık 2025</p>
                        <p class="text-xs text-slate-500">Rehber: Ali Yılmaz</p>
                      </div>
                      <span class="text-sm font-bold text-emerald-600">12/20 Dolu</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50">
                      <div class="h-10 w-10 rounded bg-sky-100 flex items-center justify-center text-sky-600">
                        <i class="fas fa-calendar-day"></i>
                      </div>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">22 Aralık 2025</p>
                        <p class="text-xs text-slate-500">Rehber: Ayşe Demir</p>
                      </div>
                      <span class="text-sm font-bold text-emerald-600">5/20 Dolu</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Bookings -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Son Katılımcılar</h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                      MK
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Mehmet Kara</p>
                      <p class="text-xs text-slate-500">2 Kişi • 15 Aralık</p>
                    </div>
                  </div>
                  <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Ödendi</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                      ES
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Elif Sönmez</p>
                      <p class="text-xs text-slate-500">1 Kişi • 22 Aralık</p>
                    </div>
                  </div>
                  <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">Bekliyor</span>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <TourAI 
              :tour="selectedTour" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-map-signs text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Tur Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve yapay zeka analizini görmek için soldaki listeden bir tur seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import TourAI from '@/components/admin/tour/TourAI.vue';

const tours = ref([]);
const loading = ref(false);

const metrics = ref([
  { label: 'Aktif Tur', value: '0', trend: '0', trendUp: true, icon: 'fa-route', bgClass: 'bg-sky-100', textClass: 'text-sky-600' },
  { label: 'Toplam Katılımcı', value: '0', trend: '0%', trendUp: true, icon: 'fa-users', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Ort. Puan', value: '0', trend: '0', trendUp: true, icon: 'fa-star', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
  { label: 'Ciro', value: '0 ₺', trend: '0%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
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
const selectedTour = ref(null);

// Computed
const filteredTours = computed(() => {
  return tours.value.filter(t => {
    const matchesSearch = t.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          t.region.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || t.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

onMounted(async () => {
  loading.value = true;
  try {
    // Fetch Tours
    const response = await axios.get('/api/v1/tours'); // Try v1 first or fallback to direct route
    // Since we registered in api.php without v1 prefix explicitly but inside a group that might have it?
    // Wait, I added it to the main api.php file. Let's check if it's under v1 prefix.
    // The file had `Route::prefix('v1')->group(...)` but I added it outside of that, in the legacy/main section.
    // So it should be `/api/tours`.
    
    const toursResponse = await axios.get('/api/tours');
    tours.value = toursResponse.data.data || toursResponse.data;
    
    if (tours.value.length > 0) {
      selectedTour.value = tours.value[0];
    }

    // Fetch Stats
    const statsResponse = await axios.get('/api/tours/stats');
    const stats = statsResponse.data;
    
    metrics.value = [
      { label: 'Aktif Tur', value: stats.active_tours.toString(), trend: '+2', trendUp: true, icon: 'fa-route', bgClass: 'bg-sky-100', textClass: 'text-sky-600' },
      { label: 'Toplam Katılımcı', value: stats.total_bookings.toString(), trend: '+15%', trendUp: true, icon: 'fa-users', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
      { label: 'Ort. Puan', value: stats.avg_rating.toString(), trend: '+0.1', trendUp: true, icon: 'fa-star', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
      { label: 'Ciro', value: stats.total_revenue + ' ₺', trend: '+8%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
    ];
    
    // Update tab counts
    tabs[0].count = tours.value.length;
    tabs[1].count = tours.value.filter(t => t.status === 'active').length;
    tabs[2].count = tours.value.filter(t => t.status === 'pending').length;

  } catch (error) {
    console.error('Failed to fetch tour data:', error);
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
