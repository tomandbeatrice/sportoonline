<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Yönetici & Rol Yönetimi</h1>
          <p class="text-slate-500">Admin kullanıcılarını, rolleri ve yetkileri yönetin.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-shield-alt text-slate-400"></i>
            Rolleri Düzenle
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            <i class="fas fa-user-plus"></i>
            Yeni Kullanıcı
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
            <span class="ml-2 text-xs font-medium text-slate-500">
              {{ metric.subtext }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Split View Content -->
    <div class="grid gap-6 lg:grid-cols-12">
      <!-- Left Panel: User List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Kullanıcı ara..." 
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
            </button>
          </div>

          <!-- List -->
          <div class="flex flex-col gap-3">
            <div 
              v-for="user in filteredUsers" 
              :key="user.id"
              @click="selectedUser = user"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedUser?.id === user.id ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:border-indigo-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center text-sm font-bold text-slate-600">
                    {{ getInitials(user.name) }}
                  </div>
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ user.name }}</h3>
                    <p class="text-xs text-slate-500">{{ user.email }}</p>
                  </div>
                </div>
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[user.status]"
                >
                  {{ statusLabels[user.status] }}
                </span>
              </div>
              <div class="mt-2 flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                  {{ user.role }}
                </span>
                <span class="text-[10px] text-slate-400 ml-auto">Son giriş: {{ user.lastLogin }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedUser" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">Kullanıcı Detayları</h2>
                <div class="flex gap-2">
                  <button class="rounded-lg border border-slate-200 p-2 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                    <i class="fas fa-key"></i>
                  </button>
                  <button class="rounded-lg border border-slate-200 p-2 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                    <i class="fas fa-edit"></i>
                  </button>
                </div>
              </div>
              
              <div class="p-6">
                <div class="grid gap-6 sm:grid-cols-2">
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Ad Soyad</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedUser.name }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">E-posta</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedUser.email }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Rol</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedUser.role }}</p>
                  </div>
                  <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500">Departman</label>
                    <p class="text-sm font-medium text-slate-900">{{ selectedUser.department }}</p>
                  </div>
                </div>

                <div class="mt-8">
                  <h3 class="mb-4 text-sm font-semibold text-slate-900">Yetkiler</h3>
                  <div class="flex flex-wrap gap-2">
                    <span v-for="perm in selectedUser.permissions" :key="perm" class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                      {{ perm }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Activity Log -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Son Aktiviteler</h2>
              </div>
              <div class="p-6 space-y-4">
                <div v-for="log in selectedUser.logs" :key="log.id" class="flex gap-3 border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                  <div class="mt-1 h-2 w-2 rounded-full bg-slate-300"></div>
                  <div class="flex-1">
                    <p class="text-sm text-slate-900">{{ log.action }}</p>
                    <p class="text-xs text-slate-500">{{ log.date }} • {{ log.ip }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <AdminUserAI 
              :user="selectedUser" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-users-cog text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Kullanıcı Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve güvenlik analizini görmek için soldaki listeden bir kullanıcı seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AdminUserAI from '@/components/admin/system/AdminUserAI.vue';
import axios from 'axios';

// State
const users = ref([]);
const loading = ref(false);

// Fetch users from API
const fetchUsers = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/admin/users');
    users.value = data.users || data.data || data || [];
  } catch (error) {
    console.error('Failed to fetch users:', error);
    // Fallback demo data
    users.value = [
      { id: 1, name: 'Admin User', email: 'admin@sportoonline.com', role: 'Super Admin', department: 'Yönetim', status: 'active', lastLogin: '10 dk önce', permissions: ['Tam Yetki'], logs: [] }
    ];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchUsers();
});

const metrics = [
  { label: 'Toplam Kullanıcı', value: '12', subtext: '4 farklı rol', icon: 'fa-users', bgClass: 'bg-indigo-100', textClass: 'text-indigo-600' },
  { label: 'Aktif Oturum', value: '4', subtext: 'Şu an çevrimiçi', icon: 'fa-circle', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Güvenlik Uyarısı', value: '1', subtext: 'İncelenmesi gereken', icon: 'fa-shield-alt', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
  { label: 'Son 24 Saat', value: '45', subtext: 'İşlem kaydı', icon: 'fa-history', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
];

const tabs = [
  { id: 'all', label: 'Tümü' },
  { id: 'active', label: 'Aktif' },
  { id: 'suspended', label: 'Askıda' },
  { id: 'admin', label: 'Yöneticiler' },
];

const statusClasses = {
  active: 'bg-emerald-100 text-emerald-700',
  suspended: 'bg-red-100 text-red-700',
  inactive: 'bg-slate-100 text-slate-700'
};

const statusLabels = {
  active: 'Aktif',
  suspended: 'Askıya Alındı',
  inactive: 'Pasif'
};

// State
const searchQuery = ref('');
const activeTab = ref('all');
const selectedUser = ref(users.value[0]);

// Computed
const filteredUsers = computed(() => {
  const userList = Array.isArray(users.value) ? users.value : [];
  return userList.filter(user => {
    const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          user.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    let matchesTab = true;
    if (activeTab.value === 'active') matchesTab = user.status === 'active';
    if (activeTab.value === 'suspended') matchesTab = user.status === 'suspended';
    if (activeTab.value === 'admin') matchesTab = user.role.includes('Admin');

    return matchesSearch && matchesTab;
  });
});

// Methods
const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

const handleAIAction = (action) => {
  console.log(`AI Action: ${action}`);
  // Implement action logic
};
</script>
