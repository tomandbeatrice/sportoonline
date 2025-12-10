<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
          <router-link to="/" class="text-2xl font-bold text-slate-900">SportoOnline</router-link>
          <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">SATICI</span>
        </div>
        <div class="flex items-center gap-3">
          <router-link to="/seller/dashboard" class="text-sm text-slate-600 hover:text-slate-900">← Dashboard</router-link>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto">
      <ProductManagement />
    </div>
  </div>
</template>

<script setup lang="ts">
import ProductManagement from '@/components/seller/ProductManagement.vue'
</script>