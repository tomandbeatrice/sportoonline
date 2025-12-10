<template>
  <div class="p-6">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Blog Kategorileri</h1>
        <p class="text-sm text-slate-500 mt-1">Blog yazıları için kategori yönetimi</p>
      </div>
      <button 
        @click="showCreateModal = true"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors"
      >
        + Yeni Kategori
      </button>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kategori</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Slug</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Yazı Sayısı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="category in categories" :key="category.id" class="hover:bg-slate-50">
            <td class="px-6 py-4">
              <div class="font-medium text-slate-900">{{ category.name }}</div>
              <div class="text-sm text-slate-500">{{ category.description }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ category.slug }}</td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ category.post_count }} yazı</td>
            <td class="px-6 py-4">
              <span 
                class="px-2 py-1 text-xs font-semibold rounded-full"
                :class="category.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'"
              >
                {{ category.is_active ? 'Aktif' : 'Pasif' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Düzenle</button>
              <button class="text-red-600 hover:text-red-800 text-sm font-medium">Sil</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const showCreateModal = ref(false)

const categories = ref([
  { id: 1, name: 'Teknoloji', slug: 'teknoloji', description: 'Teknoloji haberleri', post_count: 24, is_active: true },
  { id: 2, name: 'Moda', slug: 'moda', description: 'Moda ve stil', post_count: 18, is_active: true },
  { id: 3, name: 'Seyahat', slug: 'seyahat', description: 'Seyahat rehberi', post_count: 15, is_active: true },
  { id: 4, name: 'Yemek', slug: 'yemek', description: 'Yemek tarifleri', post_count: 32, is_active: true },
])
</script>
