<script setup lang="ts">
import { ref, onMounted } from 'vue'

const insights = ref([
  {
    id: 1,
    type: 'recommendation',
    title: 'Bu Ayakkabıyı Sevebilirsin',
    description: 'Son aldığın Nike şort ile harika uyum sağlar.',
    product: {
      name: 'Nike Air Zoom Pegasus',
      price: 3299,
      image: 'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/524f5436-3002-4288-830e-1a6c8fb8b727/air-zoom-pegasus-40-road-running-shoes-Qadg6P.png',
      discount: null
    },
    action: 'İncele'
  },
  {
    id: 2,
    type: 'price_drop',
    title: 'Fiyat Düştü! 📉',
    description: 'Favorilerindeki Adidas çanta %20 indirime girdi.',
    product: {
      name: 'Adidas Duffel Bag',
      price: 899,
      oldPrice: 1125,
      image: 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/4e894c2b76dd4c8e9013ac4300192545_9366/Linear_Duffel_Bag_Small_Black_GN2034_01_standard.jpg',
      discount: 20
    },
    action: 'Sepete Ekle'
  },
  {
    id: 3,
    type: 'repurchase',
    title: 'Stok Yenileme Zamanı?',
    description: 'Protein tozun bitmek üzere olabilir. Şimdi sipariş ver, yarın kapında.',
    product: {
      name: 'Big Joy Whey Protein',
      price: 1499,
      image: 'https://productimages.hepsiburada.net/s/40/375/10655666929714.jpg',
      discount: null
    },
    action: 'Tekrar Al'
  }
])

const isLoading = ref(true)

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false
  }, 1000)
})
</script>

<template>
  <div class="space-y-6">
    <div v-if="isLoading" class="animate-pulse space-y-4">
      <div class="h-32 bg-slate-200 rounded-xl"></div>
      <div class="h-32 bg-slate-200 rounded-xl"></div>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Insight Card -->
      <div 
        v-for="insight in insights" 
        :key="insight.id"
        class="bg-white border border-slate-100 rounded-2xl p-5 hover:shadow-lg transition group relative overflow-hidden"
      >
        <!-- Background Gradient for specific types -->
        <div 
          v-if="insight.type === 'price_drop'"
          class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-bl-full -mr-10 -mt-10 z-0"
        ></div>

        <div class="relative z-10">
          <div class="flex items-start justify-between mb-4">
            <div>
              <span 
                class="text-xs font-bold px-2 py-1 rounded-full mb-2 inline-block"
                :class="{
                  'bg-indigo-100 text-indigo-700': insight.type === 'recommendation',
                  'bg-green-100 text-green-700': insight.type === 'price_drop',
                  'bg-orange-100 text-orange-700': insight.type === 'repurchase'
                }"
              >
                {{ 
                  insight.type === 'recommendation' ? '✨ Öneri' : 
                  insight.type === 'price_drop' ? '🔥 Fırsat' : '🔄 Hatırlatma' 
                }}
              </span>
              <h3 class="font-bold text-slate-900">{{ insight.title }}</h3>
            </div>
            <button class="text-slate-300 hover:text-slate-500">✕</button>
          </div>

          <p class="text-sm text-slate-600 mb-4">{{ insight.description }}</p>

          <!-- Product Preview -->
          <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-xl mb-4">
            <img :src="insight.product.image" class="w-16 h-16 object-cover rounded-lg bg-white" />
            <div>
              <div class="font-medium text-slate-900 text-sm line-clamp-1">{{ insight.product.name }}</div>
              <div class="flex items-center gap-2">
                <span class="font-bold text-slate-900">{{ insight.product.price }} TL</span>
                <span v-if="insight.product.oldPrice" class="text-xs text-slate-400 line-through">{{ insight.product.oldPrice }} TL</span>
              </div>
            </div>
          </div>

          <button class="w-full py-2.5 bg-slate-900 text-white rounded-xl text-sm font-medium hover:bg-slate-800 transition flex items-center justify-center gap-2">
            {{ insight.action }}
            <span class="group-hover:translate-x-1 transition-transform">→</span>
          </button>
        </div>
      </div>

      <!-- Daily Tip Card -->
      <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 text-white flex flex-col justify-between">
        <div>
          <div class="text-indigo-200 text-sm font-medium mb-2">Günün İpucu 💡</div>
          <h3 class="font-bold text-xl mb-2">Daha Fazla Puan Kazan</h3>
          <p class="text-indigo-100 text-sm opacity-90">
            Profilindeki spor ilgi alanlarını tamamlayarak anında 500 Puan kazanabilirsin.
          </p>
        </div>
        <button class="mt-6 bg-white/20 hover:bg-white/30 backdrop-blur py-2 rounded-lg text-sm font-semibold transition">
          Profili Tamamla
        </button>
      </div>
    </div>
  </div>
</template>
