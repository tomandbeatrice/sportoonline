<template>
  <div class="min-h-screen bg-slate-50/50 p-6">
    <!-- Header & Metrics -->
    <div class="mb-8">
      <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Blog Yönetimi</h1>
          <p class="text-slate-500">İçeriklerinizi yönetin, SEO performansını artırın.</p>
        </div>
        <div class="flex gap-3">
          <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <i class="fas fa-comments text-slate-400"></i>
            Yorumlar
          </button>
          <button class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            <i class="fas fa-plus"></i>
            Yeni Yazı
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
      <!-- Left Panel: Post List -->
      <div class="lg:col-span-4 xl:col-span-3">
        <div class="sticky top-6 space-y-4">
          <!-- Search & Filter -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Yazı ara..." 
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
              v-for="post in filteredPosts" 
              :key="post.id"
              @click="selectedPost = post"
              class="cursor-pointer rounded-xl border p-4 transition-all hover:shadow-md"
              :class="selectedPost?.id === post.id ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:border-indigo-200'"
            >
              <div class="mb-2 flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <img :src="post.image" class="h-12 w-12 rounded-lg object-cover" alt="Blog thumbnail">
                  <div>
                    <h3 class="text-sm font-semibold text-slate-900 line-clamp-1">{{ post.title }}</h3>
                    <div class="flex items-center gap-1 text-xs text-slate-500">
                      <span>{{ post.author }}</span>
                      <span class="mx-1">•</span>
                      <span>{{ post.date }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <span 
                  class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="statusClasses[post.status]"
                >
                  {{ statusLabels[post.status] }}
                </span>
                <div class="flex items-center gap-2 text-xs text-slate-400">
                  <span><i class="fas fa-eye mr-1"></i>{{ post.views }}</span>
                  <span><i class="fas fa-comment mr-1"></i>{{ post.comments }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="lg:col-span-8 xl:col-span-9">
        <div v-if="selectedPost" class="grid gap-6 xl:grid-cols-3">
          <!-- Main Details -->
          <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <!-- Cover Image -->
              <div class="relative h-48 w-full overflow-hidden rounded-t-xl">
                <img :src="postImage(selectedPost)" class="h-full w-full object-cover" alt="Cover">
                <div class="absolute bottom-4 left-4 flex gap-2">
                  <span class="rounded-lg bg-black/50 px-3 py-1 text-xs font-medium text-white backdrop-blur-sm">
                    {{ selectedPost.category }}
                  </span>
                </div>
              </div>

              <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-900">{{ selectedPost.title }}</h2>
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
                <div class="prose prose-sm max-w-none text-slate-600">
                  <p>{{ selectedPost.excerpt }}</p>
                  <p class="mt-4">...</p>
                </div>

                <div class="mt-8 grid grid-cols-3 gap-4 border-t border-slate-100 pt-6">
                  <div class="text-center">
                    <span class="block text-lg font-bold text-slate-900">{{ selectedPost.views }}</span>
                    <span class="text-xs text-slate-500">Görüntülenme</span>
                  </div>
                  <div class="text-center border-l border-slate-100">
                    <span class="block text-lg font-bold text-slate-900">{{ selectedPost.avgTime }}dk</span>
                    <span class="text-xs text-slate-500">Ort. Okuma</span>
                  </div>
                  <div class="text-center border-l border-slate-100">
                    <span class="block text-lg font-bold text-slate-900">%{{ selectedPost.bounceRate }}</span>
                    <span class="text-xs text-slate-500">Hemen Çıkma</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Comments -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
              <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Son Yorumlar</h2>
              </div>
              <div class="p-6 space-y-4">
                <div v-if="selectedPost.recentComments.length > 0">
                  <div v-for="comment in selectedPost.recentComments" :key="comment.id" class="flex gap-3 border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                    <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                      {{ getInitials(comment.user) }}
                    </div>
                    <div class="flex-1">
                      <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-900">{{ comment.user }}</p>
                        <span class="text-xs text-slate-400">{{ comment.date }}</span>
                      </div>
                      <p class="text-sm text-slate-600 mt-1">{{ comment.text }}</p>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-4 text-sm text-slate-500">
                  Henüz yorum yapılmamış.
                </div>
              </div>
            </div>
          </div>

          <!-- AI Sidebar -->
          <div class="xl:col-span-1">
            <BlogAI 
              :post="selectedPost" 
              @action="handleAIAction"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex h-[600px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center">
          <div class="mb-4 rounded-full bg-white p-4 shadow-sm">
            <i class="fas fa-newspaper text-3xl text-slate-300"></i>
          </div>
          <h3 class="text-lg font-semibold text-slate-900">Yazı Seçin</h3>
          <p class="max-w-xs text-sm text-slate-500">Detayları ve içerik asistanını görmek için soldaki listeden bir yazı seçin.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import BlogAI from '@/components/admin/blog/BlogAI.vue';
import axios from 'axios';

// State
const posts = ref([]);
const loading = ref(false);

// Fetch posts from API
const fetchPosts = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/admin/blog/posts');
    posts.value = data.posts || data.data || data || [];
  } catch (error) {
    console.error('Failed to fetch posts:', error);
    // Fallback demo data
    posts.value = [
      { id: 1, title: 'Doğru Koşu Ayakkabısı Seçimi', excerpt: 'Koşu performansınızı artırmak için...', author: 'Admin', category: 'Rehber', status: 'published', date: '12.05.2025', views: '12.5K', comments: 45, avgTime: '4:30', bounceRate: 42, image: 'https://via.placeholder.com/100', recentComments: [] }
    ];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchPosts();
});

const metrics = [
  { label: 'Toplam Okunma', value: '125K', trend: '+12%', trendUp: true, icon: 'fa-eye', bgClass: 'bg-indigo-100', textClass: 'text-indigo-600' },
  { label: 'Aktif Yazı', value: '42', trend: '+3', trendUp: true, icon: 'fa-file-alt', bgClass: 'bg-emerald-100', textClass: 'text-emerald-600' },
  { label: 'Yorumlar', value: '850', trend: '+45', trendUp: true, icon: 'fa-comments', bgClass: 'bg-blue-100', textClass: 'text-blue-600' },
  { label: 'Ort. Süre', value: '4:12', trend: '-15sn', trendUp: false, icon: 'fa-clock', bgClass: 'bg-amber-100', textClass: 'text-amber-600' },
];

const tabs = [
  { id: 'all', label: 'Tümü', count: 45 },
  { id: 'published', label: 'Yayında', count: 38 },
  { id: 'draft', label: 'Taslak', count: 5 },
  { id: 'archived', label: 'Arşiv', count: 2 },
];

const statusClasses = {
  published: 'bg-emerald-100 text-emerald-700',
  draft: 'bg-amber-100 text-amber-700',
  archived: 'bg-slate-100 text-slate-700'
};

const statusLabels = {
  published: 'Yayında',
  draft: 'Taslak',
  archived: 'Arşivlenmiş'
};

// State
const searchQuery = ref('');
const activeTab = ref('all');
const selectedPost = ref(posts.value[0]);

// Computed
const filteredPosts = computed(() => {
  return posts.value.filter(post => {
    const matchesSearch = post.title.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesTab = activeTab.value === 'all' || post.status === activeTab.value;
    return matchesSearch && matchesTab;
  });
});

// Methods
const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

const postImage = (post) => {
  return post.image || 'https://via.placeholder.com/400x200';
};

const handleAIAction = (action) => {
  console.log(`AI Action: ${action}`);
  // Implement action logic
};
</script>
