<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Restoran Yönetimi</h1>
          <p class="text-slate-500">Menüleri düzenleyin, siparişleri takip edin ve yapay zeka ile kaliteyi artırın.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-utensils text-slate-400"></i>
            Menü Yönetimi
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-orange-700">
            <i class="fas fa-plus"></i>
            Yeni Restoran
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
      <!-- Left Panel: Restaurant List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Restoran ara..." 
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
              v-for="restaurant in filteredRestaurants" 
              :key="restaurant.id"
              @click="selectedRestaurant = restaurant"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedRestaurant?.id === restaurant.id ? 'border-orange-500 bg-orange-50/30 ring-1 ring-orange-500' : 'border-slate-200 bg-white hover:border-orange-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <img :src="restaurant.image" class="h-12 w-12 rounded-lg object-cover bg-slate-100" alt="Logo" />
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ restaurant.name }}</h3>
                    <div class="flex items-center gap-1 text-xs text-amber-500">
                      <i class="fas fa-star"></i>
                      <span class="font-medium text-slate-700">{{ restaurant.rating }}</span>
                      <span class="text-slate-400">({{ restaurant.reviews }})</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[restaurant.status]"
                >
                  {{ statusLabels[restaurant.status] }}
                </span>
                <span class="text-xs font-medium text-slate-400">{{ restaurant.cuisine }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedRestaurant" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">Restoran Detayları</h2>
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
                  <img :src="selectedRestaurant.image" class="h-32 w-full md:w-48 rounded-xl object-cover bg-slate-100" alt="Restaurant Image" />
                  <div class="flex-1 grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Restoran Adı</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedRestaurant.name }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Mutfak Türü</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedRestaurant.cuisine }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Konum</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedRestaurant.location }}</p>
                    </div>
                    <div class="space-y-1">
                      <label class="text-xs font-medium text-slate-500">Ortalama Tutar</label>
                      <p class="text-sm font-medium text-slate-900">{{ selectedRestaurant.priceRange }}</p>
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                      <label class="text-xs font-medium text-slate-500">Açıklama</label>
                      <p class="text-sm text-slate-600 leading-relaxed">{{ selectedRestaurant.description }}</p>
                    </div>
                  </div>
                </div>

                <div class="mt-8">
                  <h3 class="mb-4 text-sm font-semibold text-slate-900">Popüler Menü Öğeleri</h3>
                  <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50">
                      <div class="h-10 w-10 rounded bg-orange-100 flex items-center justify-center text-orange-600">
                        <i class="fas fa-hamburger"></i>
                      </div>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">Spesiyal Burger</p>
                        <p class="text-xs text-slate-500">245 Sipariş / Hafta</p>
                      </div>
                      <span class="text-sm font-bold text-slate-900">350₺</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50">
                      <div class="h-10 w-10 rounded bg-red-100 flex items-center justify-center text-red-600">
                        <i class="fas fa-pizza-slice"></i>
                      </div>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900">İtalyan Pizza</p>
                        <p class="text-xs text-slate-500">180 Sipariş / Hafta</p>
                      </div>
                      <span class="text-sm font-bold text-slate-900">420₺</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Reviews -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Son Değerlendirmeler</h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="flex gap-4 border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs">MK</div>
                  <div>
                    <div class="flex items-center gap-2 mb-1">
                      <span class="text-sm font-semibold text-slate-900">Murat K.</span>
                      <div class="flex text-xs text-amber-400">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                      </div>
                    </div>
                    <p class="text-sm text-slate-600">Harika bir deneyimdi, yemekler çok lezzetliydi.</p>
                  </div>
                </div>
                <div class="flex gap-4 border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs">AS</div>
                  <div>
                    <div class="flex items-center gap-2 mb-1">
                      <span class="text-sm font-semibold text-slate-900">Ayşe S.</span>
                      <div class="flex text-xs text-amber-400">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                      </div>
                    </div>
                    <p class="text-sm text-slate-600">Servis biraz yavaştı ama lezzet yerindeydi.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <RestaurantAI 
              :restaurant="selectedRestaurant" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-utensils text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Restoran Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve yapay zeka analizini görmek için soldaki listeden bir restoran seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import RestaurantAI from '@/components/admin/restaurant/RestaurantAI.vue';

// Mock Data
const restaurants = ref([
  {
    id: 1,
    name: 'Lezzet Durağı',
    cuisine: 'Türk Mutfağı',
    location: 'Kadıköy, İstanbul',
    rating: 4.8,
    reviews: 1240,
    priceRange: '₺₺',
    status: 'active',
    image: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
    description: 'Geleneksel Türk yemeklerini modern sunumlarla buluşturan, aile sıcaklığında bir mekan.'
  },
  {
    id: 2,
    name: 'Burger King',
    cuisine: 'Fast Food',
    location: 'Beşiktaş, İstanbul',
    rating: 4.2,
    reviews: 3500,
    priceRange: '₺',
    status: 'active',
    image: 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
    description: 'Dünyaca ünlü burger zinciri, hızlı servis ve değişmeyen lezzet.'
  },
  {
    id: 3,
    name: 'Sushi Co',
    cuisine: 'Uzak Doğu',
    location: 'Nişantaşı, İstanbul',
    rating: 4.9,
    reviews: 850,
    priceRange: '₺₺₺',
    status: 'active',
    image: 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
    description: 'En taze deniz ürünleriyle hazırlanan sushi ve sashimi çeşitleri.'
  },
  {
    id: 4,
    name: 'Pizza Locale',
    cuisine: 'İtalyan',
    location: 'Alsancak, İzmir',
    rating: 3.5,
    reviews: 420,
    priceRange: '₺₺',
    status: 'pending',
    image: 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
    description: 'Odun ateşinde pişen ince hamurlu Napoli pizzaları.'
  }
]);

const metrics = [
  { label: 'Aktif Restoran', value: '42', trend: '+3', trendUp: true, icon: 'fa-store', bgClass: 'bg-orange-100', textClass: 'text-orange-600' },
  { label: 'Günlük Sipariş', value: '1.2K', trend: '+15%', trendUp: true, icon: 'fa-shopping-bag', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
  { label: 'Ort. Puan', value: '4.6', trend: '+0.1', trendUp: true, icon: 'fa-star', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
  { label: 'Ciro', value: '450K ₺', trend: '+8%', trendUp: true, icon: 'fa-chart-line', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
];

const tabs = [
  { id: 'all', label: 'Tümü', count: 45 },
  { id: 'active', label: 'Aktif', count: 42 },
  { id: 'pending', label: 'Onay Bekleyen', count: 3 },
];

const statusClasses = {
  active: 'bg-emerald-100 text-emerald-700',
  pending: 'bg-amber-100 text-amber-700',
  closed: 'bg-red-100 text-red-700'
};

const statusLabels = {
  active: 'Açık',
  pending: 'Onay Bekliyor',
  closed: 'Kapalı'
};

// State
const searchQuery = ref('');
const activeTab = ref('all');
const selectedRestaurant = ref(restaurants.value[0]);

// Computed
const filteredRestaurants = computed(() => {
  return restaurants.value.filter(r => {
    const matchesSearch = r.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          r.cuisine.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || r.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

// Methods
const handleAIAction = (action) => {
  console.log(`AI Action: ${action}`);
  // Implement action logic
};
</script>
