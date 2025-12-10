<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Otel Yönetimi</h1>
          <p class="text-slate-500">Otelleri, odaları ve rezervasyonları yönetin, yapay zeka ile doluluğu artırın.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-calendar-check text-slate-400"></i>
            Rezervasyonlar
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            <i class="fas fa-plus"></i>
            Yeni Otel
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
      <!-- Left Panel: Hotel List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Otel ara..." 
              class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
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
              v-for="hotel in filteredHotels" 
              :key="hotel.id"
              @click="selectedHotel = hotel"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedHotel?.id === hotel.id ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:border-indigo-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <img :src="hotel.image" class="h-12 w-12 rounded-lg object-cover bg-slate-100" alt="Hotel" />
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ hotel.name }}</h3>
                    <div class="flex items-center gap-1 text-xs text-amber-500">
                      <i class="fas fa-star"></i>
                      <span class="font-medium text-slate-700">{{ hotel.rating }}</span>
                      <span class="text-slate-400">({{ hotel.reviews }})</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[hotel.status]"
                >
                  {{ statusLabels[hotel.status] }}
                </span>
                <span class="text-xs font-medium text-slate-400">{{ hotel.location }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedHotel" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">Otel Detayları</h2>
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
                  <img :src="selectedHotel.image" class="h-32 w-full md:w-48 rounded-xl object-cover bg-slate-100" alt="Hotel Image" />
                  <div class="flex-1 grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Otel Adı</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedHotel.name }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Konum</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedHotel.location }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Oda Sayısı</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedHotel.rooms }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Fiyat Aralığı</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedHotel.priceRange }}</p>
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                      <label class="text-xs font-medium text-slate-500">Açıklama</label>
                      <p class="text-sm text-slate-600 leading-relaxed">{{ selectedHotel.description }}</p>
                    </div>
                  </div>
                </div>

                <div class="mt-8">
                  <h3 class="mb-4 text-sm font-semibold text-slate-900">Oda Tipleri</h3>
                  <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50">
                      <div class="h-10 w-10 rounded bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-bed"></i>
                      </div>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">Standart Oda</p>
                        <p class="text-xs text-slate-500">25 m² • Şehir Manzaralı</p>
                      </div>
                      <span class="text-sm font-bold text-slate-900">1.200₺</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50">
                      <div class="h-10 w-10 rounded bg-violet-100 flex items-center justify-center text-violet-600">
                        <i class="fas fa-crown"></i>
                      </div>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">Deluxe Suite</p>
                        <p class="text-xs text-slate-500">45 m² • Deniz Manzaralı</p>
                      </div>
                      <span class="text-sm font-bold text-slate-900">2.500₺</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Bookings -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Son Rezervasyonlar</h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                      AH
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Ahmet Hakan</p>
                      <p class="text-xs text-slate-500">12-15 Aralık • 3 Gece</p>
                    </div>
                  </div>
                  <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Onaylandı</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs">
                      ZK
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Zeynep Kaya</p>
                      <p class="text-xs text-slate-500">20-22 Aralık • 2 Gece</p>
                    </div>
                  </div>
                  <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">Bekliyor</span>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <HotelAI 
              :hotel="selectedHotel" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-hotel text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Otel Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve yapay zeka analizini görmek için soldaki listeden bir otel seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import HotelAI from '@/components/admin/hotel/HotelAI.vue';

// Mock Data
const hotels = ref([
  {
    id: 1,
    name: 'Grand Plaza Hotel',
    location: 'Şişli, İstanbul',
    rating: 4.7,
    reviews: 850,
    rooms: 120,
    priceRange: '₺₺₺',
    status: 'active',
    image: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
    description: 'Şehrin kalbinde lüks konaklama deneyimi sunan, spa ve fitness merkezi bulunan 5 yıldızlı otel.'
  },
  {
    id: 2,
    name: 'Seaside Resort',
    location: 'Bodrum, Muğla',
    rating: 4.9,
    reviews: 1240,
    rooms: 250,
    priceRange: '₺₺₺₺',
    status: 'active',
    image: 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
    description: 'Ege\'nin maviliklerine açılan, özel plajı ve her şey dahil konseptiyle eşsiz bir tatil köyü.'
  },
  {
    id: 3,
    name: 'City Boutique Hotel',
    location: 'Alsancak, İzmir',
    rating: 4.5,
    reviews: 320,
    rooms: 45,
    priceRange: '₺₺',
    status: 'active',
    image: 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
    description: 'Tarihi dokusu ve modern tasarımıyla dikkat çeken, merkezi konumda butik otel.'
  },
  {
    id: 4,
    name: 'Mountain View Lodge',
    location: 'Uludağ, Bursa',
    rating: 4.2,
    reviews: 180,
    rooms: 60,
    priceRange: '₺₺₺',
    status: 'pending',
    image: 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
    description: 'Kış sporları tutkunları için ideal, pistlere yakın konumda dağ oteli.'
  }
]);

const metrics = [
  { label: 'Aktif Otel', value: '24', trend: '+2', trendUp: true, icon: 'fa-building', bgClass: 'bg-indigo-100', textClass: 'text-indigo-600' },
  { label: 'Doluluk Oranı', value: '%78', trend: '+5%', trendUp: true, icon: 'fa-bed', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Ort. Puan', value: '4.5', trend: '+0.2', trendUp: true, icon: 'fa-star', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
  { label: 'Aylık Gelir', value: '850K ₺', trend: '+12%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
];

const tabs = [
  { id: 'all', label: 'Tümü', count: 28 },
  { id: 'active', label: 'Aktif', count: 24 },
  { id: 'pending', label: 'Onay Bekleyen', count: 4 },
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
const selectedHotel = ref(hotels.value[0]);

// Computed
const filteredHotels = computed(() => {
  return hotels.value.filter(h => {
    const matchesSearch = h.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          h.location.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || h.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

// Methods
const handleAIAction = (action) => {
  console.log(`AI Action: ${action}`);
  // Implement action logic
};
</script>
