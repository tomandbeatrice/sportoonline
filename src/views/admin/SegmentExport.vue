<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Segment Dışa Aktarım</h1>
          <p class="text-slate-500">Müşteri segmentlerini analiz edin ve özel raporlar oluşturun.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-book text-slate-400"></i>
            Kılavuz
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            <i class="fas fa-bolt"></i>
            Hızlı Export
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
      <!-- Left Panel: Saved Segments (Templates) -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900">Hazır Şablonlar</h3>
            <button class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Tümü</button>
          </div>

          <!-- Search -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Şablon ara..." 
              class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
            >
          </div>

          <!-- List -->
          <div class="flex flex-col gap-3">
            <div 
              v-for="segment in filteredSegments" 
              :key="segment.id"
              @click="loadTemplate(segment)"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedTemplateId === segment.id ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:border-indigo-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                    <i class="fas" :class="segment.icon"></i>
                  </div>
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ segment.title }}</h3>
                    <p class="text-xs text-slate-500">{{ segment.audience }}</p>
                  </div>
                </div>
              </div>
              <p class="text-xs text-slate-500 line-clamp-2">{{ segment.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Export Form & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div class="grid gap-6 xl:grid-cols-3">
          <!-- Main Form -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Export Yapılandırması</h2>
              </div>
              <div class="p-6 space-y-6">
                <!-- Date Range -->
                <div class="grid gap-4 sm:grid-cols-2">
                  <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Başlangıç Tarihi</label>
                    <input v-model="form.startDate" type="date" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                  </div>
                  <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Bitiş Tarihi</label>
                    <input v-model="form.endDate" type="date" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                  </div>
                </div>

                <!-- Segment Select -->
                <div class="space-y-1.5">
                  <label class="text-sm font-medium text-slate-700">Hedef Segment</label>
                  <select v-model="form.segment" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="" disabled>Segment seçin</option>
                    <option v-for="opt in segmentOptions" :key="opt.id" :value="opt.id">{{ opt.label }}</option>
                  </select>
                </div>

                <!-- Format -->
                <div class="space-y-3">
                  <label class="text-sm font-medium text-slate-700">Çıktı Formatı</label>
                  <div class="flex gap-3">
                    <button 
                      v-for="fmt in formats" 
                      :key="fmt.id"
                      @click="form.format = fmt.id"
                      class="flex flex-1 items-center justify-center gap-2 rounded-lg border py-2 text-sm font-medium transition-all"
                      :class="form.format === fmt.id ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                    >
                      <i class="fas" :class="fmt.icon"></i>
                      {{ fmt.label }}
                    </button>
                  </div>
                </div>

                <!-- Fields -->
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-slate-700">Dahil Edilecek Alanlar</label>
                    <button class="text-xs text-indigo-600 hover:underline" @click="selectAllFields">Tümünü Seç</button>
                  </div>
                  <div class="grid gap-3 sm:grid-cols-2">
                    <label 
                      v-for="field in availableFields" 
                      :key="field.id" 
                      class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50"
                      :class="form.fields.includes(field.id) ? 'bg-indigo-50/30 border-indigo-200' : ''"
                    >
                      <input 
                        type="checkbox" 
                        :value="field.id" 
                        v-model="form.fields"
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" 
                      />
                      <span class="text-sm text-slate-700">{{ field.label }}</span>
                    </label>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                  <button class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100" @click="resetForm">
                    Temizle
                  </button>
                  <button class="flex items-center gap-2 rounded-lg bg-slate-900 px-6 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800">
                    <i class="fas fa-file-export"></i>
                    Export Oluştur
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <SegmentExportAI 
              :config="form" 
              @action="handleAIAction"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import SegmentExportAI from '@/components/admin/segment-export/SegmentExportAI.vue';

// Mock Data
const savedSegments = [
  {
    id: 'seg-1',
    title: 'Sadık Müşteriler',
    audience: 'B2C',
    description: 'Son 6 ayda en az 3 sipariş veren ve iade oranı %5\'in altında olan müşteriler.',
    icon: 'fa-heart',
    config: {
      segment: 'loyal_customers',
      format: 'excel',
      fields: ['name', 'email', 'phone', 'total_spend', 'last_order_date']
    }
  },
  {
    id: 'seg-2',
    title: 'Kaybedilme Riski',
    audience: 'Tümü',
    description: 'Son 90 gündür giriş yapmayan ancak geçmişte yüksek harcama yapmış kullanıcılar.',
    icon: 'fa-user-clock',
    config: {
      segment: 'churn_risk',
      format: 'csv',
      fields: ['name', 'email', 'last_login', 'last_order_date']
    }
  },
  {
    id: 'seg-3',
    title: 'Yeni Kurumsal Üyeler',
    audience: 'B2B',
    description: 'Son 30 günde kayıt olan ve vergi levhası onaylanmış şirketler.',
    icon: 'fa-building',
    config: {
      segment: 'new_b2b',
      format: 'excel',
      fields: ['company_name', 'tax_no', 'email', 'phone', 'city']
    }
  }
];

const metrics = [
  { label: 'Aktif Segment', value: '24', trend: '+2', trendUp: true, icon: 'fa-layer-group', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
  { label: 'Günlük Export', value: '156', trend: '+12%', trendUp: true, icon: 'fa-file-export', bgClass: 'bg-indigo-100', textClass: 'text-indigo-600' },
  { label: 'Ort. Süre', value: '1.2dk', trend: '-0.3', trendUp: true, icon: 'fa-stopwatch', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Veri Hacmi', value: '4.5GB', trend: '+0.8', trendUp: false, icon: 'fa-database', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
];

const segmentOptions = [
  { id: 'all_users', label: 'Tüm Kullanıcılar' },
  { id: 'loyal_customers', label: 'Sadık Müşteriler (VIP)' },
  { id: 'churn_risk', label: 'Kaybedilme Riski Olanlar' },
  { id: 'new_b2b', label: 'Yeni Kurumsal Üyeler' },
  { id: 'high_returns', label: 'Yüksek İade Oranı' },
];

const formats = [
  { id: 'excel', label: 'Excel (.xlsx)', icon: 'fa-file-excel' },
  { id: 'csv', label: 'CSV', icon: 'fa-file-csv' },
  { id: 'json', label: 'JSON', icon: 'fa-file-code' },
];

const availableFields = [
  { id: 'name', label: 'Ad Soyad / Ünvan' },
  { id: 'email', label: 'E-posta Adresi' },
  { id: 'phone', label: 'Telefon Numarası' },
  { id: 'tckn', label: 'TCKN / VKN' },
  { id: 'city', label: 'Şehir' },
  { id: 'total_spend', label: 'Toplam Harcama' },
  { id: 'last_order_date', label: 'Son Sipariş Tarihi' },
  { id: 'last_login', label: 'Son Giriş' },
  { id: 'company_name', label: 'Şirket Adı' },
  { id: 'tax_no', label: 'Vergi No' },
];

// State
const searchQuery = ref('');
const selectedTemplateId = ref(null);
const form = reactive({
  startDate: '',
  endDate: '',
  segment: '',
  format: 'excel',
  fields: []
});

// Computed
const filteredSegments = computed(() => {
  return savedSegments.filter(seg => 
    seg.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    seg.description.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// Methods
const loadTemplate = (template) => {
  selectedTemplateId.value = template.id;
  form.segment = template.config.segment;
  form.format = template.config.format;
  form.fields = [...template.config.fields];
  // Dates are usually dynamic, so we might leave them or set defaults
};

const selectAllFields = () => {
  form.fields = availableFields.map(f => f.id);
};

const resetForm = () => {
  selectedTemplateId.value = null;
  form.startDate = '';
  form.endDate = '';
  form.segment = '';
  form.format = 'excel';
  form.fields = [];
};

const handleAIAction = (action) => {
  console.log(`AI Action: ${action}`);
  if (action === 'mask_pii') {
    // Remove sensitive fields
    form.fields = form.fields.filter(f => !['email', 'phone', 'tckn'].includes(f));
  } else if (action === 'save_template') {
    alert('Şablon kaydetme modalı açılacak...');
  }
};
</script>
