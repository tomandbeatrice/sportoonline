<script setup lang="ts">
import { ref } from 'vue'

const steps = ['Hedef Seçimi', 'AI Analizi', 'Onay']
const currentStep = ref(0)
const isAnalyzing = ref(false)

const goals = [
  {
    id: 'revenue',
    icon: '💰',
    title: 'Gelir Artırma',
    description: 'Kısa vadeli ciro artışı için agresif indirimler.',
    color: 'bg-emerald-50 border-emerald-200 text-emerald-700'
  },
  {
    id: 'clearance',
    icon: '📦',
    title: 'Stok Eritme',
    description: 'Hareketsiz stokları nakite çevirmek için.',
    color: 'bg-orange-50 border-orange-200 text-orange-700'
  },
  {
    id: 'acquisition',
    icon: '👥',
    title: 'Yeni Müşteri',
    description: 'İlk sipariş teşviki ile kullanıcı tabanını büyütme.',
    color: 'bg-indigo-50 border-indigo-200 text-indigo-700'
  },
  {
    id: 'retention',
    icon: '🔄',
    title: 'Sadakat',
    description: 'Eski müşterileri tekrar alışverişe döndürme.',
    color: 'bg-purple-50 border-purple-200 text-purple-700'
  }
]

const selectedGoal = ref<string | null>(null)

const aiSuggestion = ref<any>(null)

const selectGoal = (id: string) => {
  selectedGoal.value = id
  currentStep.value = 1
  runAnalysis()
}

const runAnalysis = () => {
  isAnalyzing.value = true
  setTimeout(() => {
    isAnalyzing.value = false
    generateSuggestion()
  }, 2000)
}

const generateSuggestion = () => {
  const suggestions: Record<string, any> = {
    revenue: {
      title: 'Hafta Sonu Flaş İndirimi',
      products: 'En çok satan 50 ürün',
      discount: '%20',
      duration: '48 Saat',
      audience: 'Tüm Kullanıcılar',
      predictedRevenue: '₺150.000',
      predictedOrders: '450+'
    },
    clearance: {
      title: 'Sezon Sonu Temizliği',
      products: '90+ gündür satılmayan ürünler',
      discount: '%40 - %60',
      duration: '1 Hafta',
      audience: 'Fiyat duyarlı kullanıcılar',
      predictedRevenue: '₺45.000',
      predictedOrders: '200+'
    },
    acquisition: {
      title: 'Hoş Geldin Kampanyası',
      products: 'Tüm Kategoriler',
      discount: 'İlk siparişe %15',
      duration: 'Süresiz',
      audience: 'Ziyaretçi olup almayanlar',
      predictedRevenue: '₺25.000 / ay',
      predictedOrders: '100+ / ay'
    },
    retention: {
      title: 'Seni Özledik',
      products: 'Kişiselleştirilmiş Öneriler',
      discount: '500 TL üzeri 100 TL',
      duration: '3 Gün',
      audience: 'Son 60 gündür pasif',
      predictedRevenue: '₺80.000',
      predictedOrders: '150+'
    }
  }
  aiSuggestion.value = suggestions[selectedGoal.value || 'revenue']
}

const reset = () => {
  currentStep.value = 0
  selectedGoal.value = null
  aiSuggestion.value = null
}

const createCampaign = () => {
  currentStep.value = 2
  // Emit event or call API
}
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center text-xl backdrop-blur-sm">
          🤖
        </div>
        <div>
          <h2 class="text-xl font-bold">AI Kampanya Sihirbazı</h2>
          <p class="text-indigo-100 text-sm">Hedefinizi seçin, yapay zeka en uygun kampanyayı oluştursun.</p>
        </div>
      </div>
      
      <!-- Stepper -->
      <div class="flex items-center gap-2 mt-6">
        <div v-for="(step, index) in steps" :key="index" class="flex items-center">
          <div 
            class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-colors"
            :class="currentStep >= index ? 'bg-white text-indigo-600' : 'bg-indigo-800 text-indigo-400'"
          >
            {{ index + 1 }}
          </div>
          <span 
            class="ml-2 text-sm font-medium"
            :class="currentStep >= index ? 'text-white' : 'text-indigo-400'"
          >
            {{ step }}
          </span>
          <div v-if="index < steps.length - 1" class="w-12 h-0.5 mx-2 bg-indigo-800">
            <div 
              class="h-full bg-white transition-all duration-500"
              :style="{ width: currentStep > index ? '100%' : '0%' }"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="p-6 min-h-[400px]">
      <!-- Step 1: Goal Selection -->
      <div v-if="currentStep === 0" class="grid md:grid-cols-2 gap-4">
        <button
          v-for="goal in goals"
          :key="goal.id"
          @click="selectGoal(goal.id)"
          class="p-4 rounded-xl border-2 text-left transition hover:scale-[1.02]"
          :class="goal.color"
        >
          <div class="text-3xl mb-3">{{ goal.icon }}</div>
          <h3 class="font-bold text-lg mb-1">{{ goal.title }}</h3>
          <p class="text-sm opacity-80">{{ goal.description }}</p>
        </button>
      </div>

      <!-- Step 2: Analysis & Suggestion -->
      <div v-if="currentStep === 1" class="flex flex-col h-full">
        <div v-if="isAnalyzing" class="flex-1 flex flex-col items-center justify-center text-center py-12">
          <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mb-4"></div>
          <h3 class="text-lg font-bold text-slate-900">Veriler Analiz Ediliyor...</h3>
          <p class="text-slate-500 mt-2">Geçmiş satışlar, stok durumu ve kullanıcı davranışları inceleniyor.</p>
        </div>

        <div v-else-if="aiSuggestion" class="animate-fade-in">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-900">Önerilen Kampanya Stratejisi</h3>
            <button @click="reset" class="text-sm text-slate-500 hover:text-slate-700">← Farklı Hedef Seç</button>
          </div>

          <div class="grid md:grid-cols-2 gap-8">
            <!-- Campaign Card -->
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
              <div class="flex items-center gap-3 mb-4">
                <span class="text-4xl">✨</span>
                <div>
                  <h4 class="font-bold text-xl text-slate-900">{{ aiSuggestion.title }}</h4>
                  <span class="inline-block px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded mt-1">AI ÖNERİSİ</span>
                </div>
              </div>
              
              <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-slate-200">
                  <span class="text-slate-500">Hedef Kitle</span>
                  <span class="font-medium text-slate-900">{{ aiSuggestion.audience }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-200">
                  <span class="text-slate-500">Kapsam</span>
                  <span class="font-medium text-slate-900">{{ aiSuggestion.products }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-200">
                  <span class="text-slate-500">İndirim Oranı</span>
                  <span class="font-bold text-green-600">{{ aiSuggestion.discount }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-200">
                  <span class="text-slate-500">Süre</span>
                  <span class="font-medium text-slate-900">{{ aiSuggestion.duration }}</span>
                </div>
              </div>
            </div>

            <!-- Prediction -->
            <div class="flex flex-col justify-center">
              <h4 class="font-bold text-slate-900 mb-4">Tahmini Sonuçlar 📊</h4>
              <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                  <div class="text-sm text-emerald-600 mb-1">Beklenen Ciro</div>
                  <div class="text-2xl font-bold text-emerald-700">{{ aiSuggestion.predictedRevenue }}</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                  <div class="text-sm text-blue-600 mb-1">Tahmini Sipariş</div>
                  <div class="text-2xl font-bold text-blue-700">{{ aiSuggestion.predictedOrders }}</div>
                </div>
              </div>
              
              <button 
                @click="createCampaign"
                class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200"
              >
                Kampanyayı Oluştur 🚀
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Step 3: Success -->
      <div v-if="currentStep === 2" class="text-center py-12 animate-fade-in">
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">
          ✓
        </div>
        <h3 class="text-2xl font-bold text-slate-900 mb-2">Kampanya Hazır!</h3>
        <p class="text-slate-500 mb-8">Kampanya taslağı oluşturuldu ve onaya gönderildi.</p>
        <button 
          @click="reset"
          class="px-6 py-3 bg-slate-100 text-slate-700 rounded-xl font-medium hover:bg-slate-200 transition"
        >
          Yeni Kampanya Oluştur
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
