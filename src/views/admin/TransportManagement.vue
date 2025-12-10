<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Ulaşım Yönetimi</h1>
          <p class="text-slate-500">Transferleri izleyin, araçları yönetin ve yapay zeka ile rotaları optimize edin.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-map text-slate-400"></i>
            Harita Görünümü
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-green-700">
            <i class="fas fa-plus"></i>
            Yeni Transfer
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
      <!-- Left Panel: Transfer List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Transfer no veya yolcu ara..." 
              class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
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
              v-for="transfer in filteredTransfers" 
              :key="transfer.id"
              @click="selectedTransfer = transfer"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedTransfer?.id === transfer.id ? 'border-green-500 bg-green-50/30 ring-1 ring-green-500' : 'border-slate-200 bg-white hover:border-green-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-lg text-slate-500">
                    <i class="fas" :class="getVehicleIcon(transfer.vehicleType)"></i>
                  </div>
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">#{{ transfer.id }}</h3>
                    <p class="text-xs text-slate-500">{{ transfer.passenger }}</p>
                  </div>
                </div>
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[transfer.status]"
                >
                  {{ statusLabels[transfer.status] }}
                </span>
              </div>
              <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                <i class="fas fa-map-marker-alt text-slate-400"></i>
                <span class="truncate">{{ transfer.from }} <i class="fas fa-arrow-right mx-1 text-slate-300"></i> {{ transfer.to }}</span>
              </div>
              <div class="mt-2 flex items-center justify-between text-xs text-slate-400">
                <span>{{ transfer.date }}</span>
                <span>{{ transfer.price }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedTransfer" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">Transfer Detayları</h2>
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
                <!-- Route Map Placeholder -->
                <div class="mb-6 h-48 w-full rounded-lg bg-slate-100 flex items-center justify-center border border-slate-200">
                  <div class="text-center text-slate-400">
                    <i class="fas fa-map-marked-alt text-3xl mb-2"></i>
                    <p class="text-sm">Harita Görünümü</p>
                  </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Yolcu Adı</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedTransfer.passenger }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">İletişim</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedTransfer.phone }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Alış Noktası</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedTransfer.from }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Varış Noktası</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedTransfer.to }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Araç & Sürücü</label>
                    <div class="flex items-center gap-2">
                      <p class="text-sm font-medium text-slate-900">{{ selectedTransfer.vehicle }}</p>
                      <span class="text-xs text-slate-400">• {{ selectedTransfer.driver }}</span>
                    </div>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Tarih & Saat</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedTransfer.date }} • {{ selectedTransfer.time }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Timeline -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Durum Geçmişi</h2>
              </div>
              <div class="p-6">
                <div class="relative space-y-6 border-l-2 border-slate-100 pl-6">
                  <div class="relative" v-if="selectedTransfer.status === 'completed'">
                    <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-emerald-500 shadow-sm"></div>
                    <p class="text-sm font-medium text-slate-900">Tamamlandı</p>
                    <p class="text-xs text-slate-500">14:45 • Yolcu indirildi</p>
                  </div>
                  <div class="relative" v-if="['active', 'completed'].includes(selectedTransfer.status)">
                    <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-blue-500 shadow-sm"></div>
                    <p class="text-sm font-medium text-slate-900">Yolda</p>
                    <p class="text-xs text-slate-500">14:00 • Yolcu alındı</p>
                  </div>
                  <div class="relative">
                    <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-slate-300"></div>
                    <p class="text-sm font-medium text-slate-900">Oluşturuldu</p>
                    <p class="text-xs text-slate-500">{{ selectedTransfer.date }} 09:30 • Sistem</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <TransportAI 
              :transfer="selectedTransfer" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-car-side text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Transfer Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve yapay zeka analizini görmek için soldaki listeden bir transfer seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import TransportAI from '@/components/admin/transport/TransportAI.vue';

// Mock Data
const transfers = ref([
  {
    id: 'TR-1024',
    passenger: 'Ahmet Yılmaz',
    phone: '0532 111 22 33',
    from: 'İstanbul Havalimanı',
    to: 'Taksim Meydanı',
    vehicleType: 'van',
    vehicle: 'Mercedes Vito (34 ABC 123)',
    driver: 'Mehmet Şoför',
    date: '12.05.2024',
    time: '14:30',
    price: '1200 ₺',
    status: 'active'
  },
  {
    id: 'TR-1025',
    passenger: 'John Doe',
    phone: '+1 555 0123',
    from: 'Sabiha Gökçen HL',
    to: 'Kadıköy',
    vehicleType: 'sedan',
    vehicle: 'Fiat Egea (34 XYZ 789)',
    driver: 'Ali Veli',
    date: '12.05.2024',
    time: '16:00',
    price: '800 ₺',
    status: 'pending'
  },
  {
    id: 'TR-1023',
    passenger: 'Ayşe Demir',
    phone: '0555 444 55 66',
    from: 'Beşiktaş',
    to: 'İstanbul Havalimanı',
    vehicleType: 'luxury',
    vehicle: 'Mercedes S-Class (34 LUX 001)',
    driver: 'Hasan Lüks',
    date: '12.05.2024',
    time: '10:00',
    price: '2500 ₺',
    status: 'completed'
  },
  {
    id: 'TR-1022',
    passenger: 'Grup Turizm',
    phone: '0212 222 33 44',
    from: 'Sultanahmet',
    to: 'Kapalıçarşı',
    vehicleType: 'bus',
    vehicle: 'Otokar Sultan (34 TUR 555)',
    driver: 'İsmail Tur',
    date: '11.05.2024',
    time: '09:00',
    price: '3000 ₺',
    status: 'completed'
  }
]);

const metrics = [
  { label: 'Aktif Transfer', value: '12', trend: '+2', trendUp: true, icon: 'fa-car', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
  { label: 'Tamamlanan', value: '156', trend: '+15', trendUp: true, icon: 'fa-check-circle', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Bekleyen', value: '5', trend: '-1', trendUp: true, icon: 'fa-clock', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
  { label: 'Günlük Gelir', value: '15.4K ₺', trend: '+12%', trendUp: true, icon: 'fa-wallet', bgClass: 'bg-purple-100', textClass: 'text-purple-600' },
];

const tabs = [
  { id: 'all', label: 'Tümü', count: 173 },
  { id: 'active', label: 'Aktif', count: 12 },
  { id: 'pending', label: 'Bekleyen', count: 5 },
  { id: 'completed', label: 'Tamamlanan', count: 156 },
];

const statusClasses = {
  active: 'bg-blue-100 text-blue-700',
  pending: 'bg-amber-100 text-amber-700',
  completed: 'bg-emerald-100 text-emerald-700',
  cancelled: 'bg-red-100 text-red-700'
};

const statusLabels = {
  active: 'Yolda',
  pending: 'Bekliyor',
  completed: 'Tamamlandı',
  cancelled: 'İptal'
};

// State
const searchQuery = ref('');
const activeTab = ref('all');
const selectedTransfer = ref(transfers.value[0]);

// Computed
const filteredTransfers = computed(() => {
  return transfers.value.filter(t => {
    const matchesSearch = t.id.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          t.passenger.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || t.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

// Methods
const getVehicleIcon = (type) => {
  switch (type) {
    case 'van': return 'fa-shuttle-van';
    case 'bus': return 'fa-bus';
    case 'luxury': return 'fa-car-alt';
    default: return 'fa-car';
  }
};

const handleAIAction = (action) => {
  console.log(`AI Action: ${action}`);
  // Implement action logic
};
</script>
