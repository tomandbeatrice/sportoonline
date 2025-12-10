<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  productName?: string
  category?: string
}>()

const isGenerating = ref(false)
const activeTab = ref('description') // description, seo, images

const generatedContent = ref({
  description: '',
  features: [] as string[],
  seoTitle: '',
  seoDescription: '',
  keywords: [] as string[]
})

const generateContent = () => {
  isGenerating.value = true
  
  // Simulate AI delay
  setTimeout(() => {
    isGenerating.value = false
    
    if (activeTab.value === 'description') {
      generatedContent.value.description = `Bu ${props.category || 'ürün'}, üstün kalitesi ve şık tasarımıyla hayatınızı kolaylaştırmak için tasarlandı. Dayanıklı malzemelerden üretilen ${props.productName || 'bu model'}, uzun ömürlü kullanım sunar. Ergonomik yapısı sayesinde konforlu bir deneyim yaşatırken, modern çizgileriyle tarzınızı yansıtır.`
      generatedContent.value.features = [
        'Yüksek kaliteli malzeme yapısı',
        'Ergonomik ve şık tasarım',
        'Uzun ömürlü kullanım garantisi',
        'Kolay temizlenebilir yüzey',
        'Çevre dostu üretim teknolojisi'
      ]
    } else if (activeTab.value === 'seo') {
      generatedContent.value.seoTitle = `${props.productName} | En Uygun Fiyat & Hızlı Kargo - SportoOnline`
      generatedContent.value.seoDescription = `${props.productName} modelleri en iyi fiyatlarla SportoOnline'da. Hemen inceleyin, indirimli fiyatları kaçırmayın! Ücretsiz kargo ve iade garantisi.`
      generatedContent.value.keywords = ['kaliteli', 'uygun fiyat', 'indirimli', 'yeni sezon', 'hızlı kargo']
    }
  }, 1500)
}

const applyContent = (type: string) => {
  // Emit event to parent to update product form
  console.log('Applying content:', type)
}
</script>

<template>
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-violet-600 to-fuchsia-600 p-4 text-white flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-xl">✨</span>
        <h3 class="font-bold">AI İçerik Sihirbazı</h3>
      </div>
      <span class="text-xs bg-white/20 px-2 py-1 rounded">Beta</span>
    </div>

    <div class="p-4">
      <!-- Tabs -->
      <div class="flex gap-2 mb-4 border-b border-slate-100 pb-2">
        <button 
          @click="activeTab = 'description'"
          class="px-3 py-1.5 text-sm font-medium rounded-lg transition"
          :class="activeTab === 'description' ? 'bg-violet-50 text-violet-700' : 'text-slate-500 hover:bg-slate-50'"
        >
          📝 Açıklama
        </button>
        <button 
          @click="activeTab = 'seo'"
          class="px-3 py-1.5 text-sm font-medium rounded-lg transition"
          :class="activeTab === 'seo' ? 'bg-violet-50 text-violet-700' : 'text-slate-500 hover:bg-slate-50'"
        >
          🔍 SEO
        </button>
        <button 
          @click="activeTab = 'images'"
          class="px-3 py-1.5 text-sm font-medium rounded-lg transition"
          :class="activeTab === 'images' ? 'bg-violet-50 text-violet-700' : 'text-slate-500 hover:bg-slate-50'"
        >
          🖼️ Görsel Analiz
        </button>
      </div>

      <!-- Content Area -->
      <div class="min-h-[200px]">
        <div v-if="isGenerating" class="flex flex-col items-center justify-center h-48 text-slate-400">
          <div class="w-8 h-8 border-2 border-violet-200 border-t-violet-600 rounded-full animate-spin mb-2"></div>
          <span class="text-sm">Yapay zeka düşünüyor...</span>
        </div>

        <div v-else>
          <!-- Description Tab -->
          <div v-if="activeTab === 'description'" class="space-y-4">
            <div v-if="!generatedContent.description" class="text-center py-8">
              <p class="text-slate-500 text-sm mb-4">Ürün adı ve kategorisine göre otomatik açıklama oluşturun.</p>
              <button 
                @click="generateContent"
                class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 transition shadow-lg shadow-violet-200"
              >
                ✨ Otomatik Oluştur
              </button>
            </div>
            <div v-else class="animate-fade-in">
              <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 mb-3">
                <p class="text-sm text-slate-700 leading-relaxed">{{ generatedContent.description }}</p>
              </div>
              <div class="space-y-2">
                <h4 class="text-xs font-bold text-slate-500 uppercase">Öne Çıkan Özellikler</h4>
                <ul class="space-y-1">
                  <li v-for="(feature, idx) in generatedContent.features" :key="idx" class="flex items-center gap-2 text-sm text-slate-600">
                    <span class="text-green-500">✓</span> {{ feature }}
                  </li>
                </ul>
              </div>
              <div class="mt-4 flex justify-end">
                <button @click="applyContent('description')" class="text-sm text-violet-600 font-medium hover:underline">
                  Bu İçeriği Kullan →
                </button>
              </div>
            </div>
          </div>

          <!-- SEO Tab -->
          <div v-if="activeTab === 'seo'" class="space-y-4">
            <div v-if="!generatedContent.seoTitle" class="text-center py-8">
              <p class="text-slate-500 text-sm mb-4">Google aramalarında üst sıralara çıkmak için SEO önerileri alın.</p>
              <button 
                @click="generateContent"
                class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 transition shadow-lg shadow-violet-200"
              >
                🚀 SEO Analizi Yap
              </button>
            </div>
            <div v-else class="animate-fade-in space-y-3">
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Meta Başlık (Title)</label>
                <div class="p-2 bg-slate-50 border border-slate-200 rounded text-sm text-blue-600 hover:underline cursor-pointer">
                  {{ generatedContent.seoTitle }}
                </div>
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Meta Açıklama (Description)</label>
                <div class="p-2 bg-slate-50 border border-slate-200 rounded text-sm text-slate-600">
                  {{ generatedContent.seoDescription }}
                </div>
              </div>
              <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Anahtar Kelimeler</label>
                <div class="flex flex-wrap gap-2">
                  <span v-for="kw in generatedContent.keywords" :key="kw" class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded-full border border-slate-200">
                    #{{ kw }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Images Tab -->
          <div v-if="activeTab === 'images'" class="text-center py-8">
            <div class="w-16 h-16 bg-slate-50 rounded-xl border-2 border-dashed border-slate-300 flex items-center justify-center mx-auto mb-3">
              📷
            </div>
            <p class="text-sm text-slate-500">Görsel kalite analizi için bir ürün seçin.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
