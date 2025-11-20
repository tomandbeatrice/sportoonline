<template>
  <div class="relative w-full max-w-md">
    <div class="relative">
      <input
        v-model="query"
        @input="onInput"
        @focus="showResults = true"
        @blur="handleBlur"
        type="text"
        placeholder="Ürün, kategori veya marka ara..."
        class="w-full rounded-2xl border border-gray-200 pl-10 pr-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all"
      />
      <span class="absolute left-3 top-2.5 text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </span>
      <span v-if="loading" class="absolute right-3 top-2.5">
        <div class="animate-spin h-4 w-4 border-2 border-blue-500 rounded-full border-t-transparent"></div>
      </span>
    </div>

    <div v-if="showResults && (products.length > 0 || categories.length > 0)" 
         class="absolute z-50 mt-2 w-full rounded-xl border border-gray-100 bg-white shadow-lg overflow-hidden">
      
      <div v-if="categories.length > 0" class="p-2">
        <p class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase">Kategoriler</p>
        <div v-for="cat in categories" :key="cat.id" 
             @click="selectCategory(cat)"
             class="cursor-pointer rounded-lg px-3 py-2 text-sm hover:bg-gray-50 text-gray-700">
          {{ cat.name }}
        </div>
      </div>

      <div v-if="products.length > 0" class="border-t border-gray-50 p-2">
        <p class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase">Ürünler</p>
        <div v-for="prod in products" :key="prod.id" 
             @click="selectProduct(prod)"
             class="flex items-center gap-3 cursor-pointer rounded-lg px-2 py-2 hover:bg-gray-50">
          <img v-if="prod.image_url" :src="prod.image_url" class="h-8 w-8 rounded object-cover bg-gray-100" />
          <div v-else class="h-8 w-8 rounded bg-gray-100 flex items-center justify-center text-xs text-gray-400">IMG</div>
          <div class="flex-1 min-w-0">
            <p class="truncate text-sm font-medium text-gray-900">{{ prod.name }}</p>
            <p class="text-xs text-blue-600 font-semibold">{{ formatCurrency(prod.price) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useDebounce } from '@/composables/useDebounce'

const router = useRouter()
const query = ref('')
const products = ref<any[]>([])
const categories = ref<any[]>([])
const loading = ref(false)
const showResults = ref(false)

const emit = defineEmits(['search'])

async function searchProducts(val: string) {
  if (val.length < 2) {
    products.value = []
    categories.value = []
    return
  }

  loading.value = true
  try {
    const { data } = await axios.get('/api/products/search/autocomplete', {
      params: { query: val }
    })
    products.value = data.products
    categories.value = data.categories
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const { debounced: debouncedSearch, isPending } = useDebounce(searchProducts, 300)

function onInput() {
  debouncedSearch(query.value)
}

function handleBlur() {
  // Delay hiding to allow click events to register
  setTimeout(() => {
    showResults.value = false
  }, 200)
}

function selectCategory(category: any) {
  query.value = category.name
  emit('search', { type: 'category', value: category.id })
  showResults.value = false
}

function selectProduct(product: any) {
  router.push(`/products/${product.id}`)
  showResults.value = false
}

function formatCurrency(value: number) {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}
</script>
