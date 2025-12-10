<script setup lang="ts">
import { ref, onMounted } from 'vue'

const alerts = ref([
  {
    id: 1,
    type: 'warning',
    title: 'Anormal İade Artışı',
    description: '"Nike Air Max" kategorisinde son 2 saatte %15 iade artışı tespit edildi.',
    confidence: 89,
    action: 'İncele'
  },
  {
    id: 2,
    type: 'opportunity',
    title: 'Gelir Fırsatı',
    description: 'Hafta sonu kampanyası için "Outdoor" kategorisi yüksek potansiyel gösteriyor.',
    confidence: 94,
    action: 'Kampanya Oluştur'
  },
  {
    id: 3,
    type: 'security',
    title: 'Şüpheli Giriş Denemesi',
    description: '3 farklı satıcı hesabında aynı IP bloğundan başarısız giriş denemeleri.',
    confidence: 98,
    action: 'IP Engelle'
  }
])

const systemHealth = ref({
  apiLatency: 45, // ms
  errorRate: 0.02, // %
  activeWorkers: 12,
  predictionAccuracy: 92 // %
})

const isScanning = ref(true)

onMounted(() => {
  // Simulate scanning effect
  setInterval(() => {
    systemHealth.value.apiLatency = 40 + Math.floor(Math.random() * 15)
  }, 2000)
})
</script>

<template>
  <div class="grid lg:grid-cols-3 gap-6">
    <!-- AI Monitor Panel -->
    <div class="lg:col-span-2 bg-slate-900 text-white rounded-2xl p-6 relative overflow-hidden">
      <!-- Background Grid Animation -->
      <div class="absolute inset-0 opacity-10" 
           style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 20px 20px;">
      </div>

      <div class="relative z-10">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-500/20 rounded-lg flex items-center justify-center">
              <span class="text-2xl">🧠</span>
            </div>
            <div>
              <h3 class="font-bold text-lg">AI Komuta Merkezi</h3>
              <p class="text-indigo-300 text-xs">Sistem genelinde anlık analiz</p>
            </div>
          </div>
          <div class="flex items-center gap-2 px-3 py-1 bg-indigo-500/20 rounded-full border border-indigo-500/30">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            <span class="text-xs font-mono text-indigo-200">CANLI TARAMA</span>
          </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mb-6">
          <div class="bg-white/5 rounded-xl p-4 border border-white/10">
            <div class="text-xs text-slate-400 mb-1">API Gecikmesi</div>
            <div class="text-2xl font-mono font-bold text-emerald-400">{{ systemHealth.apiLatency }}ms</div>
            <div class="w-full bg-white/10 h-1 mt-2 rounded-full overflow-hidden">
              <div class="bg-emerald-500 h-full transition-all duration-500" :style="`width: ${systemHealth.apiLatency}%`"></div>
            </div>
          </div>
          <div class="bg-white/5 rounded-xl p-4 border border-white/10">
            <div class="text-xs text-slate-400 mb-1">Hata Oranı</div>
            <div class="text-2xl font-mono font-bold text-blue-400">%{{ systemHealth.errorRate }}</div>
            <div class="w-full bg-white/10 h-1 mt-2 rounded-full overflow-hidden">
              <div class="bg-blue-500 h-full" style="width: 2%"></div>
            </div>
          </div>
          <div class="bg-white/5 rounded-xl p-4 border border-white/10">
            <div class="text-xs text-slate-400 mb-1">Tahmin Doğruluğu</div>
            <div class="text-2xl font-mono font-bold text-purple-400">%{{ systemHealth.predictionAccuracy }}</div>
            <div class="w-full bg-white/10 h-1 mt-2 rounded-full overflow-hidden">
              <div class="bg-purple-500 h-full" style="width: 92%"></div>
            </div>
          </div>
        </div>

        <!-- Alerts List -->
        <div class="space-y-3">
          <div 
            v-for="alert in alerts" 
            :key="alert.id"
            class="flex items-center gap-4 p-3 rounded-xl border transition-colors cursor-pointer hover:bg-white/5"
            :class="{
              'bg-orange-500/10 border-orange-500/30': alert.type === 'warning',
              'bg-emerald-500/10 border-emerald-500/30': alert.type === 'opportunity',
              'bg-red-500/10 border-red-500/30': alert.type === 'security'
            }"
          >
            <div class="text-xl">
              {{ alert.type === 'warning' ? '⚠️' : alert.type === 'opportunity' ? '💡' : '🛡️' }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <h4 class="font-bold text-sm truncate" :class="{
                  'text-orange-300': alert.type === 'warning',
                  'text-emerald-300': alert.type === 'opportunity',
                  'text-red-300': alert.type === 'security'
                }">{{ alert.title }}</h4>
                <span class="text-xs font-mono opacity-60">%{{ alert.confidence }} Güven</span>
              </div>
              <p class="text-xs text-slate-300 truncate">{{ alert.description }}</p>
            </div>
            <button class="px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-medium transition">
              {{ alert.action }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Predictive Chart (Simplified) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 flex flex-col">
      <div class="mb-6">
        <h3 class="font-bold text-slate-900">Gelecek Tahmini 🔮</h3>
        <p class="text-sm text-slate-500">Önümüzdeki 7 gün için satış öngörüsü</p>
      </div>
      
      <div class="flex-1 flex items-end gap-2 h-40 mb-4">
        <!-- Mock Bars -->
        <div class="w-full bg-indigo-100 rounded-t-lg relative group" style="height: 40%">
          <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition">Pzt</div>
        </div>
        <div class="w-full bg-indigo-200 rounded-t-lg relative group" style="height: 55%"></div>
        <div class="w-full bg-indigo-300 rounded-t-lg relative group" style="height: 45%"></div>
        <div class="w-full bg-indigo-400 rounded-t-lg relative group" style="height: 70%"></div>
        <div class="w-full bg-indigo-500 rounded-t-lg relative group" style="height: 85%"></div>
        <div class="w-full bg-indigo-600 rounded-t-lg relative group" style="height: 60%"></div>
        <div class="w-full bg-indigo-500/50 border-2 border-dashed border-indigo-500 rounded-t-lg relative group" style="height: 90%">
          <div class="absolute top-2 left-1/2 -translate-x-1/2 text-[10px] font-bold text-indigo-700">Tahmin</div>
        </div>
      </div>

      <div class="bg-indigo-50 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-1">
          <span class="text-indigo-600 font-bold">↗ %18 Büyüme</span>
          <span class="text-xs text-slate-500">bekleniyor</span>
        </div>
        <p class="text-xs text-slate-600">
          AI modelleri, önümüzdeki hafta sonu için rekor satış hacmi öngörüyor. Stokları kontrol edin.
        </p>
      </div>
    </div>
  </div>
</template>
