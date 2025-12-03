<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-8 text-center">
      <div class="text-8xl mb-6">🔍</div>
      <h1 class="text-4xl font-bold text-slate-800 mb-3">404</h1>
      <h2 class="text-xl font-semibold text-slate-700 mb-2">Sayfa Bulunamadı</h2>
      <p class="text-slate-500 mb-8">Aradığınız sayfa mevcut değil veya taşınmış olabilir.</p>
      
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <router-link 
          to="/" 
          class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium"
        >
          🏠 Ana Sayfa
        </router-link>
        <button 
          @click="goBack"
          class="px-6 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition font-medium"
        >
          ← Geri Dön
        </button>
      </div>
      
      <div class="mt-8 pt-6 border-t border-slate-100">
        <p class="text-sm text-slate-400 mb-3">Popüler Sayfalar:</p>
        <div class="flex flex-wrap justify-center gap-2">
          <router-link to="/food" class="px-3 py-1 bg-orange-50 text-orange-600 rounded-lg text-sm hover:bg-orange-100">🍔 Yemek</router-link>
          <router-link to="/hotels" class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-sm hover:bg-blue-100">🏨 Otel</router-link>
          <router-link to="/rides" class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-sm hover:bg-green-100">🚗 Ulaşım</router-link>
          <router-link to="/services" class="px-3 py-1 bg-purple-50 text-purple-600 rounded-lg text-sm hover:bg-purple-100">🔧 Hizmet</router-link>
          <router-link to="/products" class="px-3 py-1 bg-slate-50 text-slate-600 rounded-lg text-sm hover:bg-slate-100">🛒 Ürünler</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

const goBack = () => {
  if (window.history.length > 1) {
    router.back()
  } else {
    router.push('/')
  }
}
</script>