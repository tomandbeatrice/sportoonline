<template>
  <div class="product-management h-[calc(100vh-100px)] flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Ürün Yönetimi</h1>
        <p class="text-slate-500">Yapay zeka destekli ürün ve fiyat optimizasyonu</p>
      </div>
      <div class="flex gap-3">
        <button class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition">
          🔄 Yenile
        </button>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
          + Yeni Ürün
        </button>
      </div>
    </div>

    <div class="flex gap-6 flex-1 min-h-0">
      <!-- Main Content (List) -->
      <div 
        class="flex flex-col transition-all duration-300 ease-in-out"
        :class="selectedProduct ? 'w-3/5' : 'w-full'"
      >
        <!-- Filters -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-4 grid grid-cols-4 gap-4 shrink-0">
          <div class="col-span-1">
            <input 
              type="text" 
              placeholder="Ürün ara..."
              class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
            />
          </div>
          <div class="col-span-3 flex gap-2">
            <select class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Kategori: Tümü</option>
              <option value="electronics">Elektronik</option>
              <option value="clothing">Giyim</option>
            </select>
            <select class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Durum: Tümü</option>
              <option value="active">Yayında</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex-1 flex flex-col">
          <div class="overflow-y-auto flex-1">
            <table class="w-full text-left text-sm">
              <thead class="bg-slate-50 border-b border-slate-200 sticky top-0 z-10">
                <tr>
                  <th class="px-6 py-4 font-semibold text-slate-700">Ürün</th>
                  <th class="px-6 py-4 font-semibold text-slate-700" :class="{ 'hidden': selectedProduct }">Kategori</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">Fiyat</th>
                  <th class="px-6 py-4 font-semibold text-slate-700" :class="{ 'hidden': selectedProduct }">Stok</th>
                  <th class="px-6 py-4 font-semibold text-slate-700 text-right">İşlemler</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr 
                  v-for="product in products" 
                  :key="product.id" 
                  class="hover:bg-slate-50 transition cursor-pointer"
                  :class="{ 'bg-indigo-50 hover:bg-indigo-50': selectedProduct?.id === product.id }"
                  @click="selectProduct(product)"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-xl shadow-sm">
                        {{ product.image }}
                      </div>
                      <div>
                        <p class="font-medium text-slate-900">{{ product.name }}</p>
                        <p class="text-xs text-slate-500">{{ product.sku }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-slate-600" :class="{ 'hidden': selectedProduct }">{{ product.category }}</td>
                  <td class="px-6 py-4 font-medium text-slate-900">{{ formatCurrency(product.price) }}</td>
                  <td class="px-6 py-4" :class="{ 'hidden': selectedProduct }">
                    <span :class="product.stock > 10 ? 'text-emerald-600' : 'text-orange-600'">
                      {{ product.stock }} adet
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <button 
                      class="px-3 py-1.5 rounded-lg text-xs font-medium transition flex items-center gap-1 ml-auto"
                      :class="selectedProduct?.id === product.id ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100'"
                    >
                      <span>⚡</span> {{ selectedProduct?.id === product.id ? 'Düzenleniyor' : 'AI Edit' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Side Panel (AI Tools) -->
      <div 
        v-if="selectedProduct" 
        class="w-2/5 flex flex-col gap-4 overflow-y-auto pb-4 animate-slide-in"
      >
        <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm sticky top-0 z-20">
          <h2 class="font-bold text-slate-900">Akıllı Düzenleme</h2>
          <button @click="selectedProduct = null" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <ProductAIEnricher 
          :product-name="selectedProduct.name"
          :category="selectedProduct.category"
        />

        <PriceIntelligence 
          :current-price="selectedProduct.price"
        />

        <!-- Quick Actions -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
          <h3 class="font-bold text-slate-900 mb-3">Hızlı İşlemler</h3>
          <div class="grid grid-cols-2 gap-3">
            <button class="p-3 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50 transition text-left group">
              <div class="text-xl mb-1">🏷️</div>
              <div class="text-sm font-medium text-slate-700 group-hover:text-indigo-700">Etiket Oluştur</div>
            </button>
            <button class="p-3 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50 transition text-left group">
              <div class="text-xl mb-1">📢</div>
              <div class="text-sm font-medium text-slate-700 group-hover:text-indigo-700">Reklam Çık</div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ProductAIEnricher from '@/components/admin/product/ProductAIEnricher.vue'
import PriceIntelligence from '@/components/admin/product/PriceIntelligence.vue'

interface Product {
  id: number
  name: string
  sku: string
  category: string
  price: number
  stock: number
  status: string
  image: string
}

const selectedProduct = ref<Product | null>(null)

const products = ref<Product[]>([
  { id: 1, name: 'Kablosuz Kulaklık Pro', sku: 'SKU-1001', category: 'Elektronik', price: 1299.90, stock: 45, status: 'active', image: '🎧' },
  { id: 2, name: 'Akıllı Saat Series 5', sku: 'SKU-1002', category: 'Elektronik', price: 3499.00, stock: 12, status: 'active', image: '⌚' },
  { id: 3, name: 'Spor Ayakkabı', sku: 'SKU-2001', category: 'Giyim', price: 899.50, stock: 8, status: 'active', image: '👟' },
  { id: 4, name: 'Yazlık Tişört', sku: 'SKU-2002', category: 'Giyim', price: 249.90, stock: 0, status: 'out_of_stock', image: '👕' },
  { id: 5, name: 'Kahve Makinesi', sku: 'SKU-3001', category: 'Ev & Yaşam', price: 4500.00, stock: 20, status: 'draft', image: '☕' },
])

const selectProduct = (product: Product) => {
  selectedProduct.value = selectedProduct.value?.id === product.id ? null : product
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    active: 'Yayında',
    draft: 'Taslak',
    out_of_stock: 'Stok Yok'
  }
  return labels[status] || status
}
</script>

<style scoped>
.animate-slide-in {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateX(20px); }
  to { opacity: 1; transform: translateX(0); }
}
</style>
