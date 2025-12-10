<script setup lang="ts">
import { ref, computed } from 'vue'
import ThemeAIOptimizer from '@/components/admin/theme/ThemeAIOptimizer.vue'

// --- Types ---
interface ThemeSection {
  id: string
  title: string
  icon: string
  description: string
}

// --- State ---
const activeSectionId = ref('colors')
const theme = ref({
  colors: {
    primary: '#4F46E5',
    secondary: '#3B82F6',
    accent: '#F59E0B',
    background: '#F8FAFC',
    text: '#1E293B'
  },
  typography: {
    fontFamily: 'Inter, sans-serif',
    headingFont: 'Montserrat, sans-serif',
    baseSize: '16px'
  },
  borderRadius: '0.5rem',
  buttonStyle: 'flat' // flat, outline, soft
})

const sections: ThemeSection[] = [
  { id: 'colors', title: 'Renk Paleti', icon: '🎨', description: 'Marka renkleri ve tonları' },
  { id: 'typography', title: 'Tipografi', icon: 'Aa', description: 'Yazı tipleri ve boyutları' },
  { id: 'components', title: 'Bileşenler', icon: '🧩', description: 'Butonlar, kartlar ve inputlar' },
  { id: 'layout', title: 'Yerleşim', icon: '📐', description: 'Header, footer ve grid yapısı' }
]

// --- Computed ---
const activeSection = computed(() => sections.find(s => s.id === activeSectionId.value))

// --- Methods ---
const saveTheme = () => {
  alert('Tema ayarları kaydedildi!')
}

const resetTheme = () => {
  if(confirm('Varsayılan ayarlara dönmek istediğinize emin misiniz?')) {
    // Reset logic
  }
}
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50 overflow-hidden">
    <!-- Top Bar -->
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          🎨 Tema & Görünüm
          <span class="text-xs font-normal bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full border border-purple-200">Canlı Önizleme</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Sitenizin tasarım dilini ve marka kimliğini yönetin</p>
      </div>
      
      <div class="flex gap-3">
        <button @click="resetTheme" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-medium transition">
          Varsayılan
        </button>
        <button @click="saveTheme" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold transition shadow-sm shadow-indigo-200 flex items-center gap-2">
          <span>💾</span> Kaydet
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-1 overflow-hidden">
      
      <!-- Left Panel: Sections -->
      <div class="w-64 bg-white border-r border-slate-200 flex flex-col overflow-y-auto">
        <div class="p-4 space-y-1">
          <button 
            v-for="section in sections" 
            :key="section.id"
            @click="activeSectionId = section.id"
            class="w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 transition-all duration-200"
            :class="activeSectionId === section.id ? 'bg-indigo-50 text-indigo-700 font-bold ring-1 ring-indigo-200' : 'text-slate-600 hover:bg-slate-50'"
          >
            <span class="text-xl">{{ section.icon }}</span>
            <div>
              <div class="text-sm">{{ section.title }}</div>
              <div class="text-[10px] opacity-70 font-normal truncate max-w-[120px]">{{ section.description }}</div>
            </div>
          </button>
        </div>
      </div>

      <!-- Middle Panel: Editor -->
      <div class="flex-1 overflow-y-auto p-8 bg-slate-50">
        <div class="max-w-3xl mx-auto">
          
          <!-- Colors Editor -->
          <div v-if="activeSectionId === 'colors'" class="space-y-8">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm">🎨</span>
                Ana Renkler
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Primary -->
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Birincil Renk (Primary)</label>
                  <div class="flex gap-3">
                    <input type="color" v-model="theme.colors.primary" class="h-10 w-14 rounded cursor-pointer border border-slate-300 p-1">
                    <input type="text" v-model="theme.colors.primary" class="flex-1 border border-slate-300 rounded-lg px-3 text-sm font-mono text-slate-600 uppercase">
                  </div>
                  <p class="text-xs text-slate-400">Butonlar, linkler ve ana aksiyonlar için kullanılır.</p>
                </div>

                <!-- Secondary -->
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">İkincil Renk (Secondary)</label>
                  <div class="flex gap-3">
                    <input type="color" v-model="theme.colors.secondary" class="h-10 w-14 rounded cursor-pointer border border-slate-300 p-1">
                    <input type="text" v-model="theme.colors.secondary" class="flex-1 border border-slate-300 rounded-lg px-3 text-sm font-mono text-slate-600 uppercase">
                  </div>
                  <p class="text-xs text-slate-400">Yan bileşenler ve vurgular için kullanılır.</p>
                </div>

                <!-- Accent -->
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Vurgu Rengi (Accent)</label>
                  <div class="flex gap-3">
                    <input type="color" v-model="theme.colors.accent" class="h-10 w-14 rounded cursor-pointer border border-slate-300 p-1">
                    <input type="text" v-model="theme.colors.accent" class="flex-1 border border-slate-300 rounded-lg px-3 text-sm font-mono text-slate-600 uppercase">
                  </div>
                  <p class="text-xs text-slate-400">İndirim etiketleri ve uyarılar için.</p>
                </div>

                <!-- Background -->
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Arka Plan (Background)</label>
                  <div class="flex gap-3">
                    <input type="color" v-model="theme.colors.background" class="h-10 w-14 rounded cursor-pointer border border-slate-300 p-1">
                    <input type="text" v-model="theme.colors.background" class="flex-1 border border-slate-300 rounded-lg px-3 text-sm font-mono text-slate-600 uppercase">
                  </div>
                </div>
              </div>
            </div>

            <!-- Live Preview Block -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h2 class="text-lg font-bold text-slate-800 mb-4">Canlı Önizleme</h2>
              <div class="border border-slate-200 rounded-xl overflow-hidden" :style="{ backgroundColor: theme.colors.background }">
                <!-- Mock Header -->
                <div class="h-14 flex items-center px-6 justify-between border-b border-slate-200 bg-white">
                  <div class="font-black text-xl" :style="{ color: theme.colors.primary }">LOGO</div>
                  <div class="flex gap-4 text-sm font-medium" :style="{ color: theme.colors.text }">
                    <span>Ana Sayfa</span>
                    <span>Ürünler</span>
                    <span>Hakkımızda</span>
                  </div>
                  <button class="px-4 py-1.5 rounded-lg text-white text-sm font-bold" :style="{ backgroundColor: theme.colors.primary }">
                    Giriş Yap
                  </button>
                </div>
                <!-- Mock Hero -->
                <div class="p-10 text-center">
                  <h1 class="text-3xl font-bold mb-4" :style="{ color: theme.colors.text }">Yeni Sezon İndirimleri</h1>
                  <p class="mb-6 opacity-70" :style="{ color: theme.colors.text }">En trend ürünleri keşfedin ve tarzınızı yansıtın.</p>
                  <div class="flex justify-center gap-3">
                    <button class="px-6 py-2.5 rounded-lg text-white font-bold shadow-lg" :style="{ backgroundColor: theme.colors.primary }">
                      Alışverişe Başla
                    </button>
                    <button class="px-6 py-2.5 rounded-lg font-bold border" :style="{ borderColor: theme.colors.secondary, color: theme.colors.secondary }">
                      Daha Fazla
                    </button>
                  </div>
                  <div class="mt-8 inline-block px-3 py-1 rounded text-xs font-bold text-white" :style="{ backgroundColor: theme.colors.accent }">
                    %50 İNDİRİM
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Typography Editor (Placeholder) -->
          <div v-if="activeSectionId === 'typography'" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm text-center py-20">
            <div class="text-4xl mb-4">Aa</div>
            <h3 class="text-lg font-bold text-slate-800">Tipografi Ayarları</h3>
            <p class="text-slate-500">Yazı tipleri ve boyutlandırma ayarları yakında eklenecek.</p>
          </div>

           <!-- Components Editor (Placeholder) -->
           <div v-if="activeSectionId === 'components'" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm text-center py-20">
            <div class="text-4xl mb-4">🧩</div>
            <h3 class="text-lg font-bold text-slate-800">Bileşen Ayarları</h3>
            <p class="text-slate-500">Buton stilleri ve kart yapıları yakında eklenecek.</p>
          </div>

           <!-- Layout Editor (Placeholder) -->
           <div v-if="activeSectionId === 'layout'" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm text-center py-20">
            <div class="text-4xl mb-4">📐</div>
            <h3 class="text-lg font-bold text-slate-800">Yerleşim Ayarları</h3>
            <p class="text-slate-500">Grid ve spacing ayarları yakında eklenecek.</p>
          </div>

        </div>
      </div>

      <!-- Right Panel: AI Optimizer -->
      <div class="w-80 bg-white border-l border-slate-200 p-6 overflow-y-auto">
        <h3 class="font-bold text-slate-800 mb-4">AI Asistanı</h3>
        <ThemeAIOptimizer :colors="theme.colors" />
      </div>

    </div>
  </div>
</template>
