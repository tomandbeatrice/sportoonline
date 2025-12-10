<template>
  <div class="turbo-page min-h-screen bg-gradient-to-br from-violet-900 via-purple-900 to-indigo-900">
    <!-- Header -->
    <div class="bg-black/20 backdrop-blur-sm border-b border-white/10 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <router-link to="/" class="p-2 hover:bg-white/10 rounded-lg transition">
              <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </router-link>
            <div>
              <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                🚀 TURBO MOD
              </h1>
              <p class="text-purple-300 text-sm">Aylık Yarışma - Kazan & Ödül Al</p>
            </div>
          </div>
          
          <!-- User Position Badge -->
          <div v-if="myPosition" class="hidden md:flex items-center gap-3 bg-white/10 backdrop-blur rounded-xl px-4 py-2">
            <div class="text-right">
              <p class="text-purple-300 text-xs">Sıralaman</p>
              <p class="text-white font-bold">#{{ myPosition.rank }}</p>
            </div>
            <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center font-bold text-white">
              {{ myPosition.rank }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
      <TurboMode />
    </div>

    <!-- Rules Section -->
    <div class="max-w-7xl mx-auto px-4 pb-12">
      <div class="bg-white/10 backdrop-blur rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
          📋 Yarışma Kuralları
        </h2>
        
        <div class="grid md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div class="flex gap-3">
              <div class="w-8 h-8 bg-purple-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">1</span>
              </div>
              <div>
                <h3 class="text-white font-medium">Katılım</h3>
                <p class="text-purple-300 text-sm">Alışveriş yaptığınız anda otomatik olarak yarışmaya katılırsınız.</p>
              </div>
            </div>
            
            <div class="flex gap-3">
              <div class="w-8 h-8 bg-purple-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">2</span>
              </div>
              <div>
                <h3 class="text-white font-medium">Sıralama</h3>
                <p class="text-purple-300 text-sm">Toplam alışveriş tutarınız sıralamanızı belirler. İptal ve iade edilen siparişler sayılmaz.</p>
              </div>
            </div>
            
            <div class="flex gap-3">
              <div class="w-8 h-8 bg-purple-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">3</span>
              </div>
              <div>
                <h3 class="text-white font-medium">Dönem</h3>
                <p class="text-purple-300 text-sm">Her ay yeni bir yarışma başlar. Ay sonunda sıralamalar sıfırlanır.</p>
              </div>
            </div>
          </div>
          
          <div class="space-y-4">
            <div class="flex gap-3">
              <div class="w-8 h-8 bg-yellow-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">🥇</span>
              </div>
              <div>
                <h3 class="text-white font-medium">1. Sıra Ödülü</h3>
                <p class="text-yellow-300 text-sm">₺1,000 + 5,000 Puan</p>
              </div>
            </div>
            
            <div class="flex gap-3">
              <div class="w-8 h-8 bg-slate-400/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">🥈</span>
              </div>
              <div>
                <h3 class="text-white font-medium">2. Sıra Ödülü</h3>
                <p class="text-slate-300 text-sm">₺500 + 3,000 Puan</p>
              </div>
            </div>
            
            <div class="flex gap-3">
              <div class="w-8 h-8 bg-amber-600/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">🥉</span>
              </div>
              <div>
                <h3 class="text-white font-medium">3. Sıra Ödülü</h3>
                <p class="text-amber-400 text-sm">₺250 + 2,000 Puan</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-6 p-4 bg-green-500/20 rounded-xl border border-green-500/30">
          <p class="text-green-300 text-sm flex items-center gap-2">
            <span>💡</span>
            <span><strong>Satıcılar için:</strong> En çok satan satıcılar ek olarak %5-10 komisyon indirimi kuponu kazanır!</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import TurboMode from '@/components/home/TurboMode.vue'

const myPosition = ref<any>(null)

const fetchMyPosition = async () => {
  try {
    const response = await fetch('/api/turbo/my-position', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })
    if (response.ok) {
      myPosition.value = await response.json()
    }
  } catch (error) {
    console.log('Kullanıcı pozisyonu alınamadı')
  }
}

onMounted(() => {
  fetchMyPosition()
})
</script>

<style scoped>
.turbo-page {
  min-height: 100vh;
}
</style>
