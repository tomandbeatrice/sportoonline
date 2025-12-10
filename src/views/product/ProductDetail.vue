<template>
  <div class="product-detail min-h-screen bg-slate-50 py-8" v-if="product">
    <div class="max-w-7xl mx-auto px-4">
      <div class="bg-white rounded-2xl shadow-sm p-8">
        <div class="grid lg:grid-cols-2 gap-8">
          <div>
            <img :src="product.image" alt="Ürün görseli" class="w-full rounded-xl" />
          </div>
          <div>
            <h2 class="text-3xl font-bold text-slate-900 mb-4">{{ product.name }}</h2>
            <p class="text-slate-600 mb-6">{{ product.description }}</p>
            <div class="mb-4">
              <span class="text-sm text-slate-500">Stok:</span>
              <span class="ml-2 font-semibold text-slate-900">{{ product.stock }}</span>
            </div>
            <div class="mb-6">
              <span class="text-3xl font-bold text-indigo-600">{{ formatCurrency(product.price) }}</span>
            </div>
            <button 
              @click="addToCart" 
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors"
            >
              Sepete Ekle
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="min-h-screen bg-slate-50 flex items-center justify-center">
    <p class="text-slate-500">Ürün bulunamadı.</p>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'

const route = useRoute()
const cartStore = useCartStore()
const toast = useToast()
const product = ref<any>(null)

onMounted(async () => {
  try {
    const response = await fetch(`/api/products/${route.params.id}`)
    if (response.ok) {
      product.value = await response.json()
    } else {
      // Fallback demo data
      product.value = {
        id: Number(route.params.id),
        name: 'Ürün ' + route.params.id,
        image: 'https://via.placeholder.com/400',
        description: 'Yüksek kaliteli ürün.',
        stock: 10,
        price: 299,
        type: 'product'
      }
    }
  } catch (error) {
    console.error('Ürün yüklenemedi:', error)
    product.value = null
  }
})

function addToCart() {
  if (product.value) {
    cartStore.addToCart(product.value, 1)
  }
}

const formatCurrency = (amount: number) => `₺${amount.toFixed(2)}`
</script>

<style scoped>
.product-detail { padding: 2rem; }
.product-image { width: 200px; height: auto; margin-bottom: 1rem; }
</style>