<template>
  <section v-if="turboCompetition" class="turbo-section relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0djItSDI0di0yaDEyem0wLTR2MkgyNHYtMmgxMnptMC00djJIMjR2LTJoMTJ6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-30"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <div class="flex flex-col lg:flex-row lg:items-center gap-6">
        <!-- Sol: Turbo Bilgi -->
        <div class="flex-1">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
              <span class="text-3xl">🚀</span>
            </div>
            <div>
              <h2 class="text-2xl font-bold text-white">TURBO MOD</h2>
              <p class="text-purple-200 text-sm">Aylık Yarışma - {{ turboCompetition.name }}</p>
            </div>
          </div>
          
          <!-- Geri Sayım -->
          <div class="flex items-center gap-3 mb-4">
            <div class="flex gap-2">
              <div class="bg-white/10 backdrop-blur rounded-lg px-3 py-2 text-center min-w-[60px]">
                <span class="text-2xl font-bold text-white">{{ turboCountdown.days }}</span>
                <p class="text-xs text-purple-200">Gün</p>
              </div>
              <div class="bg-white/10 backdrop-blur rounded-lg px-3 py-2 text-center min-w-[60px]">
                <span class="text-2xl font-bold text-white">{{ turboCountdown.hours }}</span>
                <p class="text-xs text-purple-200">Saat</p>
              </div>
              <div class="bg-white/10 backdrop-blur rounded-lg px-3 py-2 text-center min-w-[60px]">
                <span class="text-2xl font-bold text-white">{{ turboCountdown.minutes }}</span>
                <p class="text-xs text-purple-200">Dakika</p>
              </div>
            </div>
          </div>
          
          <p class="text-purple-100 text-sm mb-4">
            En çok alışveriş yapan müşteriler ve en çok satan satıcılar ödüller kazanıyor!
          </p>
          
          <router-link 
            to="/turbo"
            class="inline-flex items-center gap-2 bg-white text-purple-700 px-6 py-2.5 rounded-xl font-semibold hover:bg-purple-50 transition-all shadow-lg"
          >
            <span>Yarışmaya Katıl</span>
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </router-link>
        </div>
        
        <!-- Sağ: Liderlik Tablosu -->
        <div class="lg:w-[400px]">
          <div class="bg-white/10 backdrop-blur rounded-2xl p-4">
            <h3 class="text-white font-semibold mb-3 flex items-center gap-2">
              <span>🏆</span> Liderlik Tablosu
            </h3>
            
            <!-- Podyum -->
            <div class="flex items-end justify-center gap-2 mb-4">
              <!-- 2. Sıra -->
              <div v-if="turboLeaders[1]" class="text-center">
                <div class="w-12 h-12 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-1">
                  <span class="text-lg">🥈</span>
                </div>
                <div class="bg-gradient-to-t from-slate-400/30 to-slate-300/30 rounded-t-lg px-3 py-6">
                  <p class="text-white text-xs font-medium truncate max-w-[70px]">{{ turboLeaders[1]?.name }}</p>
                  <p class="text-purple-200 text-xs">{{ formatTurboCurrency(turboLeaders[1]?.total) }}</p>
                </div>
              </div>
              
              <!-- 1. Sıra -->
              <div v-if="turboLeaders[0]" class="text-center -mt-4">
                <div class="w-14 h-14 mx-auto bg-yellow-400/30 rounded-full flex items-center justify-center mb-1 ring-2 ring-yellow-400">
                  <span class="text-2xl">🥇</span>
                </div>
                <div class="bg-gradient-to-t from-yellow-400/30 to-yellow-300/30 rounded-t-lg px-4 py-8">
                  <p class="text-white text-sm font-bold truncate max-w-[80px]">{{ turboLeaders[0]?.name }}</p>
                  <p class="text-yellow-200 text-sm font-semibold">{{ formatTurboCurrency(turboLeaders[0]?.total) }}</p>
                </div>
              </div>
              
              <!-- 3. Sıra -->
              <div v-if="turboLeaders[2]" class="text-center">
                <div class="w-12 h-12 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-1">
                  <span class="text-lg">🥉</span>
                </div>
                <div class="bg-gradient-to-t from-amber-600/30 to-amber-500/30 rounded-t-lg px-3 py-4">
                  <p class="text-white text-xs font-medium truncate max-w-[70px]">{{ turboLeaders[2]?.name }}</p>
                  <p class="text-purple-200 text-xs">{{ formatTurboCurrency(turboLeaders[2]?.total) }}</p>
                </div>
              </div>
            </div>
            
            <!-- Ödüller -->
            <div class="grid grid-cols-3 gap-2 text-center">
              <div class="bg-white/5 rounded-lg p-2">
                <p class="text-yellow-400 font-bold">₺1,000</p>
                <p class="text-purple-200 text-xs">1. Ödül</p>
              </div>
              <div class="bg-white/5 rounded-lg p-2">
                <p class="text-slate-300 font-bold">₺500</p>
                <p class="text-purple-200 text-xs">2. Ödül</p>
              </div>
              <div class="bg-white/5 rounded-lg p-2">
                <p class="text-amber-500 font-bold">₺250</p>
                <p class="text-purple-200 text-xs">3. Ödül</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const turboCompetition = ref<any>(null)
const turboLeaders = ref<any[]>([])
const turboCountdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 })
let turboCountdownInterval: ReturnType<typeof setInterval> | null = null

const formatTurboCurrency = (amount: number) => {
  if (!amount) return '₺0'
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(amount)
}

const loadTurboCompetition = async () => {
  try {
    const response = await fetch('/api/turbo/current')
    if (response.ok) {
      const data = await response.json()
      turboCompetition.value = data.competition
      if (data.competition?.ends_at) {
        startTurboCountdown(data.competition.ends_at)
      }
    }
    
    // Liderlik tablosunu yükle
    const leaderResponse = await fetch('/api/turbo/leaderboard/customers')
    if (leaderResponse.ok) {
      const leaderData = await leaderResponse.json()
      turboLeaders.value = leaderData.data?.slice(0, 3) || []
    }
  } catch (error) {
    console.error('Turbo yarışması yüklenemedi:', error)
  }
}

const startTurboCountdown = (endDate: string) => {
  const updateTurboCountdown = () => {
    const now = new Date().getTime()
    const end = new Date(endDate).getTime()
    const diff = end - now
    
    if (diff <= 0) {
      turboCountdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
      if (turboCountdownInterval) clearInterval(turboCountdownInterval)
      return
    }
    
    turboCountdown.value = {
      days: Math.floor(diff / (1000 * 60 * 60 * 24)),
      hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
      minutes: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)),
      seconds: Math.floor((diff % (1000 * 60)) / 1000)
    }
  }
  
  updateTurboCountdown()
  turboCountdownInterval = setInterval(updateTurboCountdown, 1000)
}

onMounted(() => {
  loadTurboCompetition()
})

onUnmounted(() => {
  if (turboCountdownInterval) clearInterval(turboCountdownInterval)
})
</script>
