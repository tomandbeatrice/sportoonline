<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header -->
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Pazaryeri Genel Bakış</h1>
        <p class="text-slate-500">Satıcı performansı, komisyon gelirleri ve büyüme metrikleri.</p>
      </div>
      <div class="flex gap-3">
        <select class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 outline-none focus:border-indigo-500">
          <option>Son 7 Gün</option>
          <option>Son 30 Gün</option>
          <option>Bu Yıl</option>
        </select>
        <button class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
          <Download class="w-4 h-4" />
          Rapor Al
        </button>
      </div>
    </div>

    <!-- Key Metrics -->
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-slate-500">Toplam GMV</span>
          <div class="rounded-lg bg-indigo-100 p-2 text-indigo-600">
            <TrendingUp class="w-5 h-5" />
          </div>
        </div>
        <div class="mt-4">
          <span class="text-2xl font-bold text-slate-900">₺2.4M</span>
          <span class="ml-2 text-xs font-medium text-emerald-600 flex items-center inline-flex gap-1">
            <ArrowUp class="w-3 h-3" /> %12.5
          </span>
        </div>
        <p class="mt-1 text-xs text-slate-400">Geçen aya göre</p>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-slate-500">Komisyon Geliri</span>
          <div class="rounded-lg bg-emerald-100 p-2 text-emerald-600">
            <Coins class="w-5 h-5" />
          </div>
        </div>
        <div class="mt-4">
          <span class="text-2xl font-bold text-slate-900">₺360K</span>
          <span class="ml-2 text-xs font-medium text-emerald-600 flex items-center inline-flex gap-1">
            <ArrowUp class="w-3 h-3" /> %8.2
          </span>
        </div>
        <p class="mt-1 text-xs text-slate-400">Ort. Komisyon %15</p>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-slate-500">Aktif Satıcı</span>
          <div class="rounded-lg bg-blue-100 p-2 text-blue-600">
            <Store class="w-5 h-5" />
          </div>
        </div>
        <div class="mt-4">
          <span class="text-2xl font-bold text-slate-900">1,240</span>
          <span class="ml-2 text-xs font-medium text-emerald-600 flex items-center inline-flex gap-1">
            <ArrowUp class="w-3 h-3" /> +45
          </span>
        </div>
        <p class="mt-1 text-xs text-slate-400">Bu ay katılanlar</p>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-slate-500">Ort. Sepet Tutarı</span>
          <div class="rounded-lg bg-amber-100 p-2 text-amber-600">
            <ShoppingBasket class="w-5 h-5" />
          </div>
        </div>
        <div class="mt-4">
          <span class="text-2xl font-bold text-slate-900">₺850</span>
          <span class="ml-2 text-xs font-medium text-red-600 flex items-center inline-flex gap-1">
            <ArrowDown class="w-3 h-3" /> %2.1
          </span>
        </div>
        <p class="mt-1 text-xs text-slate-400">Geçen aya göre</p>
      </div>
    </div>

    <!-- Charts & Lists -->
    <div class="grid gap-6 lg:grid-cols-3">
      <!-- Sales Trend Chart (Mock) -->
      <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
        <h3 class="mb-6 text-lg font-bold text-slate-900">Satış Trendi (GMV)</h3>
        <div class="relative h-64 w-full">
          <!-- Simple CSS Bar Chart Mock -->
          <div class="flex h-full items-end justify-between gap-2">
            <div v-for="(bar, index) in salesData" :key="index" class="group relative w-full rounded-t-lg bg-indigo-50 transition-all hover:bg-indigo-100">
              <div 
                class="absolute bottom-0 w-full rounded-t-lg bg-indigo-500 transition-all group-hover:bg-indigo-600"
                :style="{ height: bar.height + '%' }"
              ></div>
              <div class="absolute -top-8 left-1/2 hidden -translate-x-1/2 rounded bg-slate-800 px-2 py-1 text-xs text-white group-hover:block">
                ₺{{ bar.value }}K
              </div>
              <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs text-slate-500">{{ bar.label }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Categories -->
      <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-6 text-lg font-bold text-slate-900">Kategori Performansı</h3>
        <div class="space-y-5">
          <div v-for="cat in topCategories" :key="cat.name">
            <div class="mb-1 flex justify-between text-sm">
              <span class="font-medium text-slate-700">{{ cat.name }}</span>
              <span class="text-slate-500">₺{{ cat.value }}K</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
              <div 
                class="h-full rounded-full bg-indigo-500"
                :style="{ width: cat.percentage + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Section -->
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
      <!-- Top Sellers -->
      <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
          <h3 class="font-bold text-slate-900">En İyi Satıcılar</h3>
          <router-link to="/admin/sellers" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Tümünü Gör</router-link>
        </div>
        <div class="divide-y divide-slate-100">
          <div v-for="seller in topSellers" :key="seller.id" class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-lg">
                {{ seller.logo }}
              </div>
              <div>
                <p class="text-sm font-semibold text-slate-900">{{ seller.name }}</p>
                <p class="text-xs text-slate-500">{{ seller.category }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm font-bold text-slate-900">₺{{ seller.sales }}K</p>
              <p class="text-xs text-emerald-600 flex items-center justify-end gap-1">
                <Star class="w-3 h-3 fill-current" /> {{ seller.rating }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Applications -->
      <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
          <h3 class="font-bold text-slate-900">Son Başvurular</h3>
          <router-link to="/admin/seller-applications" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Tümünü Gör</router-link>
        </div>
        <div class="divide-y divide-slate-100">
          <div v-for="app in recentApplications" :key="app.id" class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="h-2 w-2 rounded-full bg-blue-500"></div>
              <div>
                <p class="text-sm font-semibold text-slate-900">{{ app.company }}</p>
                <p class="text-xs text-slate-500">{{ app.date }}</p>
              </div>
            </div>
            <div class="flex gap-2">
              <button class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-100">
                Onayla
              </button>
              <button class="rounded-lg bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100">
                İncele
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { 
  Download, 
  TrendingUp, 
  ArrowUp, 
  ArrowDown, 
  Coins, 
  Store, 
  ShoppingBasket,
  Star
} from 'lucide-vue-next';

const salesData = ref([
  { label: 'Pzt', value: 120, height: 40 },
  { label: 'Sal', value: 150, height: 50 },
  { label: 'Çar', value: 180, height: 60 },
  { label: 'Per', value: 140, height: 45 },
  { label: 'Cum', value: 220, height: 75 },
  { label: 'Cmt', value: 280, height: 90 },
  { label: 'Paz', value: 250, height: 80 },
]);

const topCategories = ref([
  { name: 'Spor Giyim', value: 850, percentage: 85 },
  { name: 'Ayakkabı', value: 620, percentage: 65 },
  { name: 'Fitness Ekipmanları', value: 450, percentage: 45 },
  { name: 'Outdoor', value: 320, percentage: 35 },
  { name: 'Beslenme', value: 180, percentage: 20 },
]);

const topSellers = ref([
  { id: 1, name: 'Spor Dünyası', category: 'Giyim', sales: 450, rating: 4.8, logo: '🏪' },
  { id: 2, name: 'Fit Life', category: 'Ekipman', sales: 320, rating: 4.9, logo: '💪' },
  { id: 3, name: 'Koşu Pro', category: 'Ayakkabı', sales: 280, rating: 4.7, logo: '👟' },
  { id: 4, name: 'Outdoor Master', category: 'Kamp', sales: 150, rating: 4.6, logo: '⛺' },
]);

const recentApplications = ref([
  { id: 1, company: 'Yoga Store Ltd.', date: '2 saat önce' },
  { id: 2, company: 'Bisiklet Dünyası', date: '5 saat önce' },
  { id: 3, company: 'Protein Market', date: '1 gün önce' },
  { id: 4, company: 'Tenis Shop', date: '1 gün önce' },
]);
</script>
