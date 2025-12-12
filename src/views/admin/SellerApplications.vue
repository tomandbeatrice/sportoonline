<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Satıcı Başvuruları</h1>
          <p class="text-slate-500">Yeni satıcı adaylarını inceleyin ve yapay zeka desteğiyle değerlendirin.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-download text-slate-400"></i>
            Dışa Aktar
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            <i class="fas fa-cog"></i>
            Başvuru Ayarları
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
      <!-- Left Panel: Application List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Mağaza veya e-posta ara..." 
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
              v-for="app in filteredApplications" 
              :key="app.id"
              @click="selectedApplication = app"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedApplication?.id === app.id ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:border-indigo-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-2">
                  <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                    {{ app.store_name.substring(0, 2).toUpperCase() }}
                  </div>
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ app.store_name }}</h3>
                    <p class="text-xs text-slate-500">{{ app.created_at_formatted }}</p>
                  </div>
                </div>
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[app.status]"
                >
                  {{ statusLabels[app.status] }}
                </span>
              </div>
              <div class="flex items-center justify-between text-xs text-slate-500">
                <span>{{ app.user.name }}</span>
                <i class="fas fa-chevron-right text-slate-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedApplication" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Başvuru Detayları</h2>
              </div>
              <div class="p-6">
                <div class="grid gap-6 sm:grid-cols-2">
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Mağaza Adı</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedApplication.store_name }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Yetkili Kişi</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedApplication.user.name }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">E-posta</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedApplication.user.email }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Telefon</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedApplication.user.phone || '+90 555 123 45 67' }}</p>
                  </div>
                  <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-medium text-slate-500">Şirket Adresi</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedApplication.address || 'Örnek Mahallesi, Teknoloji Caddesi No:1, İstanbul' }}</p>
                  </div>
                </div>

                <div class="mt-8">
                  <h3 class="mb-4 text-sm font-semibold text-slate-900">Yüklenen Belgeler</h3>
                  <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50">
                      <div class="rounded bg-red-50 p-2 text-red-500">
                        <i class="fas fa-file-pdf"></i>
                      </div>
                      <div class="flex-1 overflow-hidden">
                        <p class="truncate text-sm font-medium text-slate-900">Vergi_Levhasi.pdf</p>
                        <p class="text-xs text-slate-500">2.4 MB • 12.05.2024</p>
                      </div>
                      <button class="text-slate-400 hover:text-indigo-600">
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50">
                      <div class="rounded bg-blue-50 p-2 text-blue-500">
                        <i class="fas fa-file-image"></i>
                      </div>
                      <div class="flex-1 overflow-hidden">
                        <p class="truncate text-sm font-medium text-slate-900">Imza_Sirkuleri.jpg</p>
                        <p class="text-xs text-slate-500">1.1 MB • 12.05.2024</p>
                      </div>
                      <button class="text-slate-400 hover:text-indigo-600">
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Timeline / History -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">İşlem Geçmişi</h2>
              </div>
              <div class="p-6">
                <div class="relative space-y-6 border-l-2 border-slate-100 pl-6">
                  <div class="relative">
                    <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-indigo-500 shadow-sm"></div>
                    <p class="text-sm font-medium text-slate-900">Başvuru İnceleniyor</p>
                    <p class="text-xs text-slate-500">Bugün, 14:30 • Sistem tarafından atandı</p>
                  </div>
                  <div class="relative">
                    <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-slate-300"></div>
                    <p class="text-sm font-medium text-slate-900">Başvuru Alındı</p>
                    <p class="text-xs text-slate-500">{{ selectedApplication.created_at_formatted }} • Web Form</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <SellerApplicationAI 
              :application="selectedApplication" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-store text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Başvuru Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve yapay zeka analizini görmek için soldaki listeden bir başvuru seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import SellerApplicationAI from '@/components/admin/seller-apps/SellerApplicationAI.vue';

const applications = ref([]);
const loading = ref(false);

const metrics = ref([
  { label: 'Bekleyen', value: '0', trend: '0', trendUp: true, icon: 'fa-clock', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
  { label: 'Onaylanan', value: '0', trend: '0', trendUp: true, icon: 'fa-check-circle', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Reddedilen', value: '0', trend: '0', trendUp: false, icon: 'fa-ban', bgClass: 'bg-red-100', textClass: 'text-red-600' },
  { label: 'Ort. Süre', value: '-', trend: '-', trendUp: true, icon: 'fa-stopwatch', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
]);

const tabs = ref([
  { id: 'all', label: 'Tümü', count: 0 },
  { id: 'pending', label: 'Bekleyen', count: 0 },
  { id: 'approved', label: 'Onaylı', count: 0 },
  { id: 'rejected', label: 'Red', count: 0 },
]);

const statusClasses = {
  pending: 'bg-amber-100 text-amber-700',
  approved: 'bg-emerald-100 text-emerald-700',
  rejected: 'bg-red-100 text-red-700'
};

const statusLabels = {
  pending: 'Bekliyor',
  approved: 'Onaylandı',
  rejected: 'Reddedildi'
};

// State
const searchQuery = ref('');
const activeTab = ref('pending');
const selectedApplication = ref(null);

// Computed
const filteredApplications = computed(() => {
  const apps = Array.isArray(applications.value) ? applications.value : [];
  return apps.filter(app => {
    const matchesSearch = (app.store_name?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) || 
                          (app.user?.email?.toLowerCase() || '').includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || app.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

onMounted(async () => {
    await fetchApplications();
    await fetchStats();
});

async function fetchApplications() {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/seller-applications');
        applications.value = response.data.data || response.data;
        
        // Update tab counts
        const apps = Array.isArray(applications.value) ? applications.value : [];
        tabs.value[0].count = apps.length;
        tabs.value[1].count = apps.filter(a => a.status === 'pending').length;
        tabs.value[2].count = apps.filter(a => a.status === 'approved').length;
        tabs.value[3].count = apps.filter(a => a.status === 'rejected').length;

        if (apps.length > 0 && !selectedApplication.value) {
            selectedApplication.value = apps[0];
        }
    } catch (error) {
        console.error('Failed to fetch seller applications:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchStats() {
    try {
        const response = await axios.get('/api/admin/seller-applications/stats');
        // Assuming API returns stats matching our metrics structure
        // For now, we'll just update counts from the applications list if API doesn't return full stats
        const apps = Array.isArray(applications.value) ? applications.value : [];
        metrics.value[0].value = apps.filter(a => a.status === 'pending').length.toString();
        metrics.value[1].value = apps.filter(a => a.status === 'approved').length.toString();
        metrics.value[2].value = apps.filter(a => a.status === 'rejected').length.toString();
    } catch (error) {
        console.error('Failed to fetch stats:', error);
    }
}

// Methods
const handleAIAction = async (action) => {
  if (!selectedApplication.value) return;
  
  try {
      if (action === 'approve') {
        await axios.post(`/api/admin/seller-applications/${selectedApplication.value.id}/approve`);
        selectedApplication.value.status = 'approved';
        // Refresh list to update counts
        await fetchApplications();
      } else if (action === 'reject') {
        await axios.post(`/api/admin/seller-applications/${selectedApplication.value.id}/reject`);
        selectedApplication.value.status = 'rejected';
        await fetchApplications();
      }
  } catch (error) {
      console.error('Action failed:', error);
  }
};
</script>
