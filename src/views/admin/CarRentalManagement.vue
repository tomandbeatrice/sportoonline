<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Araç Kiralama Yönetimi</h1>
          <p class="text-slate-500">Filo yönetimi, rezervasyon takibi ve yapay zeka destekli bakım planlaması.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-wrench text-slate-400"></i>
            Bakım Takvimi
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-orange-700">
            <i class="fas fa-plus"></i>
            Yeni Araç
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
      <!-- Left Panel: Car List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Araç ara..." 
              class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
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
              v-for="car in filteredCars" 
              :key="car.id"
              @click="selectedCar = car"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedCar?.id === car.id ? 'border-orange-500 bg-orange-50/30 ring-1 ring-orange-500' : 'border-slate-200 bg-white hover:border-orange-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <img :src="car.image" class="h-12 w-12 rounded-lg object-cover bg-slate-100" alt="Car" />
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ car.model }}</h3>
                    <div class="flex items-center gap-1 text-xs text-slate-500">
                      <i class="fas fa-gas-pump"></i>
                      <span>{{ car.fuelType }}</span>
                      <span class="mx-1">•</span>
                      <span>{{ car.transmission }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[car.status]"
                >
                  {{ statusLabels[car.status] }}
                </span>
                <span class="text-xs font-medium text-slate-400">{{ car.plate }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedCar" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">Araç Detayları</h2>
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
                  <img :src="selectedCar.image" class="h-32 w-full md:w-48 rounded-xl object-cover bg-slate-100" alt="Car Image" />
                  <div class="flex-1 grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Model</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedCar.model }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Plaka</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedCar.plate }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Yıl</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedCar.year }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Günlük Fiyat</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedCar.price }}₺</p>
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                      <label class="text-xs font-medium text-slate-500">Özellikler</label>
                      <div class="flex flex-wrap gap-2 mt-1">
                        <span v-for="feature in selectedCar.features" :key="feature" class="rounded bg-slate-100 px-2 py-1 text-xs text-slate-600">
                          {{ feature }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-8">
                  <h3 class="mb-4 text-sm font-semibold text-slate-900">Kilometre Geçmişi</h3>
                  <div class="h-2 w-full rounded-full bg-slate-100">
                    <div class="h-2 rounded-full bg-orange-500" style="width: 65%"></div>
                  </div>
                  <div class="mt-2 flex justify-between text-xs text-slate-500">
                    <span>Son Bakım: 45.000 km</span>
                    <span>Güncel: 52.450 km</span>
                    <span>Sonraki Bakım: 60.000 km</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Rentals -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Son Kiralamalar</h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                      AK
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Ali Kaya</p>
                      <p class="text-xs text-slate-500">10-15 Aralık • 5 Gün</p>
                    </div>
                  </div>
                  <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Tamamlandı</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                      BM
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Burak Mert</p>
                      <p class="text-xs text-slate-500">20-22 Aralık • 2 Gün</p>
                    </div>
                  </div>
                  <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">Aktif</span>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <CarRentalAI 
              :car="selectedCar" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-car text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Araç Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve yapay zeka analizini görmek için soldaki listeden bir araç seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import CarRentalAI from '@/components/admin/car-rental/CarRentalAI.vue';

const cars = ref([]);
const loading = ref(false);

const metrics = ref([
  { label: 'Toplam Araç', value: '0', trend: '0', trendUp: true, icon: 'fa-car', bgClass: 'bg-orange-100', textClass: 'text-orange-600' },
  { label: 'Kiradaki Araç', value: '0', trend: '0%', trendUp: true, icon: 'fa-key', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Bakımdaki Araç', value: '0', trend: '0', trendUp: true, icon: 'fa-tools', bgClass: 'bg-red-100', textClass: 'text-red-600' },
  { label: 'Aylık Ciro', value: '0 ₺', trend: '0%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
]);

const tabs = [
  { id: 'all', label: 'Tümü', count: 0 },
  { id: 'available', label: 'Müsait', count: 0 },
  { id: 'rented', label: 'Kirada', count: 0 },
  { id: 'maintenance', label: 'Bakımda', count: 0 },
];

const statusClasses = {
  available: 'bg-emerald-100 text-emerald-700',
  rented: 'bg-blue-100 text-blue-700',
  maintenance: 'bg-red-100 text-red-700'
};

const statusLabels = {
  available: 'Müsait',
  rented: 'Kirada',
  maintenance: 'Bakımda'
};

// State
const searchQuery = ref('');
const activeTab = ref('all');
const selectedCar = ref(null);

// Computed
const filteredCars = computed(() => {
  return cars.value.filter(c => {
    const matchesSearch = c.model.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          c.plate.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || c.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

onMounted(async () => {
  loading.value = true;
  try {
    // Fetch Cars
    const response = await axios.get('/api/cars');
    cars.value = response.data.data || response.data;
    
    if (cars.value.length > 0) {
      selectedCar.value = cars.value[0];
    }

    // Fetch Stats
    const statsResponse = await axios.get('/api/cars/stats');
    const stats = statsResponse.data;
    
    metrics.value = [
      { label: 'Toplam Araç', value: stats.total_cars.toString(), trend: '+3', trendUp: true, icon: 'fa-car', bgClass: 'bg-orange-100', textClass: 'text-orange-600' },
      { label: 'Kiradaki Araç', value: stats.rented_cars.toString(), trend: '+12%', trendUp: true, icon: 'fa-key', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
      { label: 'Bakımdaki Araç', value: stats.maintenance_cars.toString(), trend: '-1', trendUp: true, icon: 'fa-tools', bgClass: 'bg-red-100', textClass: 'text-red-600' },
      { label: 'Aylık Ciro', value: stats.monthly_revenue + ' ₺', trend: '+8%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
    ];
    
    // Update tab counts
    tabs[0].count = cars.value.length;
    tabs[1].count = cars.value.filter(c => c.status === 'available').length;
    tabs[2].count = cars.value.filter(c => c.status === 'rented').length;
    tabs[3].count = cars.value.filter(c => c.status === 'maintenance').length;

  } catch (error) {
    console.error('Failed to fetch car data:', error);
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
