<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Dışa Aktarım Merkezi</h1>
          <p class="text-slate-500">Raporlarınızı yönetin, indirin ve yapay zeka ile optimize edin.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-history text-slate-400"></i>
            Geçmiş
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            <i class="fas fa-plus"></i>
            Yeni Export
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
      <!-- Left Panel: Export List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Dosya adı ara..." 
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
              v-for="file in filteredFiles" 
              :key="file.id"
              @click="selectedFile = file"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedFile?.id === file.id ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:border-indigo-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-lg text-slate-500">
                    <i class="fas" :class="getFileIcon(file.format)"></i>
                  </div>
                  <div class="overflow-hidden">
                    <h3 class="truncate text-sm font-semibold text-slate-900">{{ file.name }}</h3>
                    <p class="text-xs text-slate-500">{{ file.date }}</p>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[file.status]"
                >
                  {{ statusLabels[file.status] }}
                </span>
                <span class="text-xs font-medium text-slate-400">{{ file.size }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedFile" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">Dosya Detayları</h2>
                <button 
                  v-if="selectedFile.status === 'completed'"
                  class="flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-700"
                >
                  <i class="fas fa-download"></i>
                  İndir
                </button>
              </div>
              <div class="p-6">
                <div class="grid gap-6 sm:grid-cols-2">
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Dosya Adı</label>
                    <p class="text-sm font-medium text-slate-900 break-all">{{ selectedFile.name }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Format</label>
                    <p class="text-sm font-medium text-slate-900 uppercase">{{ selectedFile.format }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Segment</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedFile.segment }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Oluşturulma Tarihi</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedFile.date }}</p>
                  </div>
                  <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Açıklama</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedFile.description }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Logs / History -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">İşlem Günlükleri</h2>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div class="flex gap-3">
                    <div class="mt-1 text-xs font-mono text-slate-400">14:30:05</div>
                    <div class="text-sm text-slate-600">İşlem kuyruğa alındı.</div>
                  </div>
                  <div class="flex gap-3">
                    <div class="mt-1 text-xs font-mono text-slate-400">14:30:10</div>
                    <div class="text-sm text-slate-600">Veri tabanı sorgusu başlatıldı (Rows: 15,420).</div>
                  </div>
                  <div class="flex gap-3" v-if="selectedFile.status === 'completed'">
                    <div class="mt-1 text-xs font-mono text-slate-400">14:30:45</div>
                    <div class="text-sm text-emerald-600 font-medium">Dosya başarıyla oluşturuldu.</div>
                  </div>
                  <div class="flex gap-3" v-if="selectedFile.status === 'failed'">
                    <div class="mt-1 text-xs font-mono text-slate-400">14:31:00</div>
                    <div class="text-sm text-red-600 font-medium">Hata: Bağlantı zaman aşımı.</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <ExportAI 
              :exportFile="selectedFile" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-file-export text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Dosya Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve yapay zeka analizini görmek için soldaki listeden bir dosya seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import ExportAI from '@/components/admin/export/ExportAI.vue';

// Mock Data
const files = ref([
  {
    id: 1,
    name: 'Satis_Raporu_2024_Q1.xlsx',
    description: '2024 ilk çeyrek satış verileri ve detaylı analiz.',
    format: 'xlsx',
    segment: 'Finans',
    size: '12.4 MB',
    date: '12.05.2024 14:30',
    status: 'completed'
  },
  {
    id: 2,
    name: 'Musteri_Listesi_Aktif.csv',
    description: 'Son 30 günde giriş yapan aktif müşteriler.',
    format: 'csv',
    segment: 'Pazarlama',
    size: '4.2 MB',
    date: '12.05.2024 10:15',
    status: 'completed'
  },
  {
    id: 3,
    name: 'Stok_Durumu_Mayis.pdf',
    description: 'Kritik stok seviyesindeki ürünler.',
    format: 'pdf',
    segment: 'Depo',
    size: '1.8 MB',
    date: '11.05.2024 16:45',
    status: 'completed'
  },
  {
    id: 4,
    name: 'Kampanya_Donusumleri.xlsx',
    description: 'Bahar kampanyası dönüşüm oranları.',
    format: 'xlsx',
    segment: 'Pazarlama',
    size: '-',
    date: '12.05.2024 15:00',
    status: 'processing'
  },
  {
    id: 5,
    name: 'Log_Kayitlari_Hata.txt',
    description: 'Son 24 saatteki sistem hataları.',
    format: 'txt',
    segment: 'Sistem',
    size: '-',
    date: '11.05.2024 09:00',
    status: 'failed'
  }
]);

const metrics = [
  { label: 'Toplam Dosya', value: '1,245', trend: '+12', trendUp: true, icon: 'fa-file', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
  { label: 'İndirilen', value: '856', trend: '+45', trendUp: true, icon: 'fa-download', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Hatalı', value: '12', trend: '-2', trendUp: true, icon: 'fa-exclamation-triangle', bgClass: 'bg-red-100', textClass: 'text-red-600' },
  { label: 'Ort. Boyut', value: '5.2 MB', trend: '+0.5', trendUp: false, icon: 'fa-database', bgClass: 'bg-indigo-100', textClass: 'text-indigo-600' },
];

const tabs = [
  { id: 'all', label: 'Tümü', count: 1245 },
  { id: 'completed', label: 'Tamamlanan', count: 1200 },
  { id: 'processing', label: 'İşleniyor', count: 5 },
  { id: 'failed', label: 'Hatalı', count: 40 },
];

const statusClasses = {
  completed: 'bg-emerald-100 text-emerald-700',
  processing: 'bg-blue-100 text-blue-700',
  failed: 'bg-red-100 text-red-700'
};

const statusLabels = {
  completed: 'Tamamlandı',
  processing: 'İşleniyor',
  failed: 'Hata'
};

// State
const searchQuery = ref('');
const activeTab = ref('all');
const selectedFile = ref(files.value[0]);

// Computed
const filteredFiles = computed(() => {
  return files.value.filter(file => {
    const matchesSearch = file.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || file.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

// Methods
const getFileIcon = (format) => {
  switch (format) {
    case 'xlsx': return 'fa-file-excel text-emerald-600';
    case 'csv': return 'fa-file-csv text-blue-600';
    case 'pdf': return 'fa-file-pdf text-red-600';
    case 'txt': return 'fa-file-alt text-slate-600';
    default: return 'fa-file text-slate-400';
  }
};

const handleAIAction = (action) => {
  console.log(`AI Action: ${action}`);
  // Implement action logic
};
</script>
