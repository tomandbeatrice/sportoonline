<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       📝 BLOG & İÇERİK - Lifestyle Hub
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="blog-posts py-4 mb-8">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-slate-900">İlham Veren İçerikler</h2>
      <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Bloga Git →</a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="post in blogPosts" :key="post.id" class="flex gap-4 bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-all cursor-pointer">
        <div class="w-24 h-24 bg-slate-50 rounded-lg flex items-center justify-center text-4xl flex-shrink-0">
          {{ post.image }}
        </div>
        <div class="flex flex-col justify-center">
          <span class="text-xs font-bold text-indigo-600 mb-1">{{ post.category }}</span>
          <h3 class="font-semibold text-slate-900 leading-tight mb-2">{{ post.title }}</h3>
          <span class="text-xs text-slate-500">⏱️ {{ post.readTime }} okuma</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// State
const blogPosts = ref([
  { id: 1, title: 'En İyi Koşu Ayakkabısı Nasıl Seçilir?', category: 'Spor', image: '👟', readTime: '5 dk' },
  { id: 2, title: 'Sağlıklı Beslenme İpuçları', category: 'Beslenme', image: '🥗', readTime: '3 dk' },
  { id: 3, title: 'Evde Egzersiz Rehberi', category: 'Fitness', image: '🧘‍♀️', readTime: '7 dk' }
])
</script>
