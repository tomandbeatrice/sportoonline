<template>
  <div class="min-h-screen bg-slate-50 pb-20 font-sans">
    <!-- Loading State -->
    <div v-if="loading" class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <div class="lg:grid lg:grid-cols-2 lg:gap-x-12">
        <!-- Image Skeleton -->
        <div class="aspect-square overflow-hidden rounded-3xl bg-white shadow-sm border border-slate-100">
          <Skeleton class="h-full w-full" />
        </div>

        <!-- Info Skeleton -->
        <div class="mt-10 sm:mt-16 lg:mt-0">
          <Skeleton class="h-10 w-3/4 mb-4" />
          <Skeleton class="h-6 w-1/2 mb-6" />
          <Skeleton class="h-12 w-40 mb-6" />
          <Skeleton class="h-24 w-full mb-6" />
          <div class="flex gap-4">
            <Skeleton class="h-12 flex-1" />
            <Skeleton class="h-12 w-32" />
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="product" class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="mb-8 flex items-center text-sm text-slate-500">
        <router-link to="/" class="hover:text-blue-600 transition-colors">Ana Sayfa</router-link>
        <svg class="mx-3 h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <router-link
          :to="`/?category=${product.category_id}`"
          class="hover:text-blue-600 transition-colors"
        >
          {{ product.category?.name || 'Kategori' }}
        </router-link>
        <svg class="mx-3 h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="font-medium text-slate-900 truncate max-w-[200px]">{{ product.name }}</span>
      </nav>

      <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 lg:items-start">
        <!-- Image Gallery -->
        <div class="relative">
          <SmartImage 
            :src="product.image_url" 
            :alt="product.name" 
            container-class="aspect-square rounded-3xl border border-slate-100 shadow-sm"
            image-class="hover:scale-105 transition-transform duration-500"
          />
          <!-- Badges -->
          <div class="absolute top-4 left-4 flex flex-col gap-2">
            <span v-if="product.stock < 5 && product.stock > 0" class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-lg">
              Son {{ product.stock }} Ürün
            </span>
            <span v-if="product.discount_rate" class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-full shadow-lg">
              %{{ product.discount_rate }} İndirim
            </span>
          </div>
        </div>

        <!-- Product Info -->
        <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
          <div class="mb-6">
            <h1 class="text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">{{ product.name }}</h1>
            <div class="mt-4 flex items-center">
              <div class="flex items-center">
                <div class="flex items-center">
                  <BadgeIcon v-for="i in 5" :key="i" name="star" :cls="i <= 4 ? 'w-4 h-4 text-yellow-400' : 'w-4 h-4 text-slate-200'" />
                </div>
                <p class="ml-3 text-sm text-slate-500">{{ reviews.length }} değerlendirme</p>
              </div>
              <div class="ml-4 border-l border-slate-200 pl-4">
                <span class="text-sm font-medium text-green-600" v-if="product.stock > 0">Stokta Var</span>
                <span class="text-sm font-medium text-red-600" v-else>Tükendi</span>
              </div>
            </div>
          </div>

          <div class="mt-6">
            <h2 class="sr-only">Ürün Bilgileri</h2>
            <p class="text-4xl font-bold text-slate-900">{{ formatCurrencyWithConversion(parseFloat(product.price), 'TRY') }}</p>
          </div>

          <div class="mt-8">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-medium text-slate-900">Miktar</h3>
            </div>

            <div class="mt-2 flex items-center gap-4">
              <div class="flex items-center rounded-xl border border-slate-200 bg-white">
                <button @click="quantity > 1 && quantity--" class="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-l-xl">-</button>
                <input
                  v-model.number="quantity"
                  type="number"
                  min="1"
                  :max="product.stock"
                  class="w-16 border-0 bg-transparent py-2 text-center text-slate-900 focus:ring-0"
                />
                <button @click="quantity < product.stock && quantity++" class="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-r-xl">+</button>
              </div>
              <span class="text-sm text-slate-500">{{ product.stock }} adet mevcut</span>
            </div>
          </div>

          <div class="mt-8 flex gap-4">
            <button
              @click="addToCart"
              :disabled="product.stock === 0"
              class="flex-1 flex items-center justify-center rounded-2xl bg-blue-600 px-8 py-4 text-base font-bold text-white shadow-lg shadow-blue-200 transition-all hover:bg-blue-700 hover:shadow-blue-300 hover:-translate-y-1 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
            >
              <svg class="mr-2 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              Sepete Ekle
            </button>
            <button
              @click="toggleFavorite"
              class="flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-4 text-slate-400 shadow-sm transition-all hover:border-red-200 hover:text-red-500 hover:shadow-md"
              :class="{ 'text-red-500 border-red-200 bg-red-50': isFavorite }"
            >
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
              </svg>
            </button>
          </div>

          <!-- Seller Card -->
          <div class="mt-10 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold text-xl">
                  {{ product.vendor_id ? 'S' : 'A' }}
                </div>
                <div>
                  <h3 class="font-bold text-slate-900">Satıcı: {{ product.vendor_id || 'SportoOnline' }}</h3>
                  <div class="flex items-center text-xs text-slate-500">
                    <span class="flex items-center text-yellow-500 mr-2">
                      <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                      4.8
                    </span>
                    <span>• Başarılı Satıcı</span>
                  </div>
                </div>
              </div>
              <button @click="contactSeller" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">
                Mesaj At
              </button>
            </div>
          </div>

          <div class="mt-10 border-t border-slate-200 pt-10">
            <h3 class="text-lg font-bold text-slate-900">Ürün Açıklaması</h3>
            <div class="mt-4 prose prose-sm text-slate-600">
              <p>{{ product.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Similar Products (AI Recommendation) -->
      <section class="mt-24">
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-2xl font-bold text-slate-900">Bunu Beğenenler Bunları da İnceledi</h2>
          <span class="text-xs font-semibold bg-gradient-to-r from-fuchsia-600 to-purple-600 bg-clip-text text-transparent flex items-center gap-1">
            <svg class="w-4 h-4 text-fuchsia-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            AI Önerisi
          </span>
        </div>
        
        <!-- Loading State -->
        <div v-if="loadingSimilar" class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
          <div v-for="i in 4" :key="i" class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100">
            <Skeleton class="aspect-square w-full rounded-xl mb-4" />
            <Skeleton class="h-4 w-3/4 mb-2" />
            <Skeleton class="h-4 w-1/2" />
          </div>
        </div>
        
        <!-- Real AI Products -->
        <div v-else-if="similarProducts.length > 0" class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
          <router-link 
            v-for="similar in similarProducts" 
            :key="similar.id" 
            :to="`/products/${similar.id}`"
            class="group relative rounded-2xl bg-white p-4 shadow-sm transition-all hover:shadow-md border border-slate-100 cursor-pointer"
          >
            <div class="aspect-square w-full overflow-hidden rounded-xl bg-slate-100 mb-4">
              <img 
                v-if="similar.image_url" 
                :src="similar.image_url" 
                :alt="similar.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
              </div>
            </div>
            <h3 class="text-sm font-medium text-slate-900 line-clamp-2">{{ similar.name }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ similar.category?.name || 'Spor' }}</p>
            <p class="mt-2 font-bold text-blue-600">{{ similar.price }} TL</p>
            <span v-if="similar.match_score" class="absolute top-2 right-2 px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-700 rounded-full">
              %{{ Math.round(similar.match_score * 100) }} Eşleşme
            </span>
          </router-link>
        </div>
        
        <!-- Fallback Empty State -->
        <div v-else class="text-center py-12 bg-slate-50 rounded-2xl">
          <p class="text-slate-500">Henüz benzer ürün önerisi bulunmuyor.</p>
        </div>
      </section>
      
      <!-- Frequently Bought Together (AI) -->
      <section v-if="boughtTogetherProducts.length > 0" class="mt-16">
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-2xl font-bold text-slate-900">Birlikte Sıkça Alınanlar</h2>
          <span class="text-xs font-semibold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent flex items-center gap-1">
            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            Müşteri Tercihi
          </span>
        </div>
        
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex flex-wrap items-center justify-center gap-4">
            <!-- Current Product -->
            <div class="text-center">
              <SmartImage 
                v-if="product.image_url" 
                :src="product.image_url" 
                :alt="product.name" 
                container-class="w-24 h-24 rounded-xl mx-auto"
              />
              <p class="mt-2 text-sm font-medium text-slate-900 max-w-[120px] truncate">{{ product.name }}</p>
              <p class="text-sm font-bold text-blue-600">{{ product.price }} TL</p>
            </div>
            
            <!-- Plus Signs and Other Products -->
            <template v-for="(item, index) in boughtTogetherProducts.slice(0, 3)" :key="item.id">
              <span class="text-2xl text-slate-300 font-light">+</span>
              <router-link :to="`/products/${item.id}`" class="text-center group cursor-pointer">
                <SmartImage 
                  v-if="item.image_url" 
                  :src="item.image_url" 
                  :alt="item.name" 
                  container-class="w-24 h-24 rounded-xl mx-auto group-hover:ring-2 ring-blue-500 transition-all"
                />
                <p class="mt-2 text-sm font-medium text-slate-900 max-w-[120px] truncate group-hover:text-blue-600">{{ item.name }}</p>
                <p class="text-sm font-bold text-blue-600">{{ item.price }} TL</p>
              </router-link>
            </template>
            
            <!-- Total & Add All Button -->
            <div class="ml-8 pl-8 border-l border-slate-200">
              <p class="text-sm text-slate-500">Toplam Fiyat</p>
              <p class="text-2xl font-bold text-slate-900">
                {{ (parseFloat(product.price) + boughtTogetherProducts.slice(0, 3).reduce((sum, p) => sum + parseFloat(p.price), 0)).toFixed(2) }} TL
              </p>
              <button 
                @click="addBundleToCart"
                class="mt-3 px-6 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-colors"
              >
                Hepsini Sepete Ekle
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Reviews Section -->
      <section class="mt-24">
        <h2 class="text-2xl font-bold text-slate-900 mb-8">Müşteri Değerlendirmeleri</h2>
        
        <div class="grid gap-8 lg:grid-cols-12">
          <!-- Review Summary -->
          <div class="lg:col-span-4">
            <div class="rounded-2xl bg-slate-50 p-6">
              <div class="flex items-end gap-2">
                <span class="text-5xl font-black text-slate-900">4.8</span>
                <span class="mb-2 text-lg text-slate-500">/ 5</span>
              </div>
              <div class="mt-4 flex items-center">
                <BadgeIcon v-for="i in 5" :key="'s'+i" name="star" :cls="'w-5 h-5 '+(i<=5? 'text-yellow-400':'text-slate-200')" />
              </div>
              <p class="mt-2 text-sm text-slate-500">{{ reviews.length }} yoruma göre</p>
              
              <div class="mt-6 space-y-2">
                <div class="flex items-center gap-2 text-sm">
                  <span class="w-3 text-slate-600">5</span>
                  <div class="h-2 flex-1 rounded-full bg-slate-200 overflow-hidden">
                    <div class="h-full w-[80%] bg-yellow-400"></div>
                  </div>
                </div>
                <div class="flex items-center gap-2 text-sm">
                  <span class="w-3 text-slate-600">4</span>
                  <div class="h-2 flex-1 rounded-full bg-slate-200 overflow-hidden">
                    <div class="h-full w-[15%] bg-yellow-400"></div>
                  </div>
                </div>
                <!-- ... other bars ... -->
              </div>
            </div>
          </div>

          <!-- Review List & Form -->
          <div class="lg:col-span-8">
             <!-- Review Form -->
            <div v-if="isAuthenticated" class="mb-10 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
              <h3 class="font-bold text-slate-900 mb-4">Değerlendirme Yap</h3>
              <form @submit.prevent="submitReview">
                <div class="mb-4">
                  <label class="block text-sm font-medium text-slate-700 mb-2">Puanınız</label>
                  <div class="flex gap-1">
                    <button
                      v-for="i in 5"
                      :key="i"
                      type="button"
                      @click="newReview.rating = i"
                      class="transition-transform hover:scale-110"
                      :aria-pressed="i <= newReview.rating"
                      :title="`${i} yıldız`"
                    >
                      <IconStar :cls="i <= newReview.rating ? 'w-6 h-6 text-yellow-400' : 'w-6 h-6 text-slate-200'" :filled="i <= newReview.rating" />
                    </button>
                  </div>
                </div>
                <div class="mb-4">
                  <label class="block text-sm font-medium text-slate-700 mb-2">Yorumunuz</label>
                  <textarea
                    v-model="newReview.comment"
                    rows="3"
                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Ürün hakkında deneyimlerinizi paylaşın..."
                  ></textarea>
                </div>
                <button
                  type="submit"
                  class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white hover:bg-slate-800"
                >
                  Yorumu Gönder
                </button>
              </form>
            </div>

            <!-- Reviews -->
            <div class="space-y-6">
              <div v-if="reviews.length === 0" class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 py-12 text-center">
                <div class="text-4xl mb-2"><BadgeIcon name="chat" cls="w-12 h-12 text-slate-400" /></div>
                <p class="text-slate-500">Henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
              </div>
              
              <div
                v-for="review in reviews"
                :key="review.id"
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5"
              >
                <div class="flex items-start justify-between">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                      {{ (review.user?.name || 'A')[0].toUpperCase() }}
                    </div>
                    <div>
                      <p class="font-bold text-slate-900">{{ review.user?.name || 'Anonim Kullanıcı' }}</p>
                      <div class="flex text-xs">
                        <BadgeIcon v-for="i in review.rating" :key="'r'+i" name="star" cls="w-4 h-4 text-yellow-400" />
                        <BadgeIcon v-for="i in (5 - review.rating)" :key="'e'+i" name="star" cls="w-4 h-4 text-slate-200" />
                      </div>
                    </div>
                  </div>
                  <span class="text-xs text-slate-400">{{ formatDate(review.created_at) }}</span>
                </div>
                <p class="mt-4 text-slate-600 leading-relaxed">{{ review.comment }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/services/api'
import { useProductStore } from '../../stores/productStore'
import { useCartStore } from '../../stores/cartStore'
import { useOrderStore } from '../../stores/orderStore'
import { storeToRefs } from 'pinia'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
import IconStar from '@/components/icons/IconStar.vue'
import { useTracking } from '@/composables/useTracking'
import SmartImage from '@/components/ui/SmartImage.vue'
import { useSEO, generateProductSchema } from '@/composables/useSEO'
import { formatCurrencyWithConversion } from '@/utils/currencyConverter'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const productStore = useProductStore()
const { products: storeProducts } = storeToRefs(productStore)
const cartStore = useCartStore()
const orderStore = useOrderStore()

// AI Tracking System
const { trackProductView, trackCartEvent } = useTracking()
const { updateSEO } = useSEO()

const product = ref<any>(null)
const reviews = ref<any[]>([])
const loading = ref(true)
const quantity = ref(1)
const isFavorite = ref(false)

// AI Recommendations
const similarProducts = ref<any[]>([])
const boughtTogetherProducts = ref<any[]>([])
const loadingSimilar = ref(false)
const loadingBoughtTogether = ref(false)

const newReview = ref({
  rating: 5,
  comment: ''
})

watch(reviews, () => {
  if (product.value) {
    updateSEO({
      jsonLd: generateProductSchema(product.value, reviews.value)
    })
  }
})

const isAuthenticated = computed(() => {
  return !!localStorage.getItem('token')
})

const fetchProduct = async () => {
  loading.value = true
  try {
    const id = Number(route.params.id)
    const found = storeProducts.value.find(p => p.id === id)
    
    if (found) {
      product.value = found
      
      // Update SEO
      updateSEO({
        title: `${found.name} - SportoOnline`,
        description: found.description || `${found.name} en uygun fiyatlarla SportoOnline'da.`,
        image: found.image_url,
        jsonLd: generateProductSchema(found, reviews.value)
      })
    } else {
      // Fallback to API if not in store (or handle 404)
      // For now, just mock it or redirect
      console.warn('Product not found in store, trying API...')
      // const { data } = await api.get(`/products/${route.params.id}`)
      // product.value = data
      toast.error('Ürün bulunamadı!')
      router.push('/')
    }
  } catch (error) {
    console.error('Ürün yüklenemedi:', error)
    toast.error('Ürün bulunamadı!')
    router.push('/')
  } finally {
    loading.value = false
  }
}

const fetchReviews = async () => {
  try {
    const { data } = await api.get(`/products/${product.value?.id}/reviews`)
    reviews.value = data.reviews || data.data || data || []
  } catch (error) {
    console.error('Yorumlar yüklenemedi:', error)
    // Fallback demo data
    reviews.value = [
      { id: 1, user: { name: 'Mehmet K.' }, rating: 5, comment: 'Harika bir ürün, çok memnun kaldım.', created_at: '2025-11-10' },
      { id: 2, user: { name: 'Ayşe Y.' }, rating: 4, comment: 'Kargo biraz geç geldi ama ürün kaliteli.', created_at: '2025-11-12' }
    ]
  }
}

// AI: Benzer Ürünleri Getir
const fetchSimilarProducts = async () => {
  if (!product.value?.id) return
  loadingSimilar.value = true
  try {
    const { data } = await api.get(`/ai/products/${product.value.id}/similar`)
    similarProducts.value = data.similar_products || []
  } catch (error) {
    console.error('Benzer ürünler yüklenemedi:', error)
    // Fallback to random products from store
    similarProducts.value = storeProducts.value
      .filter(p => p.id !== product.value.id && p.category_id === product.value.category_id)
      .slice(0, 4)
  } finally {
    loadingSimilar.value = false
  }
}

// AI: Birlikte Alınan Ürünleri Getir
const fetchBoughtTogether = async () => {
  if (!product.value?.id) return
  loadingBoughtTogether.value = true
  try {
    const { data } = await api.get(`/ai/products/${product.value.id}/bought-together`)
    boughtTogetherProducts.value = data.bought_together || []
  } catch (error) {
    console.error('Birlikte alınan ürünler yüklenemedi:', error)
    // Fallback
    boughtTogetherProducts.value = storeProducts.value
      .filter(p => p.id !== product.value.id)
      .slice(0, 3)
  } finally {
    loadingBoughtTogether.value = false
  }
}

const addToCart = async () => {
  cartStore.addToCart(product.value, quantity.value)
  // AI: Sepete ekleme olayını takip et
  trackCartEvent(product.value.id, 'add', quantity.value, parseFloat(product.value.price))
}

// AI: Birlikte alınan ürünleri sepete ekle
const addBundleToCart = () => {
  // Ana ürünü ekle
  cartStore.addToCart(product.value, 1)
  trackCartEvent(product.value.id, 'add', 1, parseFloat(product.value.price))
  
  // Birlikte alınan ürünleri ekle
  boughtTogetherProducts.value.slice(0, 3).forEach(item => {
    cartStore.addToCart(item, 1)
    trackCartEvent(item.id, 'add', 1, parseFloat(item.price))
  })
  
  toast.success(`${boughtTogetherProducts.value.slice(0, 3).length + 1} ürün sepete eklendi!`)
}

const toggleFavorite = async () => {
  orderStore.toggleFavorite(product.value)
  isFavorite.value = orderStore.isFavorite(product.value.id)
}

const submitReview = async () => {
  if (!newReview.value.comment.trim()) {
    toast.warning('Lütfen bir yorum yazın!')
    return
  }

  try {
    await api.post('/reviews', {
      product_id: product.value.id,
      rating: newReview.value.rating,
      comment: newReview.value.comment
    })
    toast.success('Yorumunuz gönderildi!')
    newReview.value = { rating: 5, comment: '' }
    fetchReviews()
  } catch (error) {
    console.error('Yorum gönderilemedi:', error)
    toast.error('Bir hata oluştu!')
  }
}

const contactSeller = () => {
  if (!isAuthenticated.value) {
    toast.warning('Mesaj göndermek için giriş yapmalısınız!')
    router.push('/login')
    return
  }
  router.push(`/messages?seller=${product.value.vendor_id}`)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(() => {
  fetchProduct()
  fetchReviews()
})

// Ürün yüklendiğinde AI verilerini getir ve görüntülemeyi takip et
watch(product, (newProduct) => {
  if (newProduct?.id) {
    // AI: Ürün görüntüleme olayını takip et
    trackProductView(newProduct.id, 'product_detail')
    // AI önerilerini getir
    fetchSimilarProducts()
    fetchBoughtTogether()
  }
})
</script>
