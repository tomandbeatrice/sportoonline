<template>
  <div class="products-page max-w-7xl mx-auto p-8">
    <!-- Başlık ve Filtreler -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-6">Ürünler</h1>
      
      <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
        <!-- Kategori Filtresi -->
        <div class="w-full md:w-auto">
          <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
          <select
            v-model="selectedCategory"
            class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Tüm Kategoriler</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <!-- Arama -->
        <div class="w-full md:w-auto">
          <label class="block text-sm font-medium text-gray-700 mb-2">Ara</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Ürün ara..."
            class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Sıralama -->
        <div class="w-full md:w-auto">
          <label class="block text-sm font-medium text-gray-700 mb-2">Sırala</label>
          <select
            v-model="sortBy"
            class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          >
            <option value="newest">En Yeni</option>
            <option value="price-asc">Fiyat: Düşük-Yüksek</option>
            <option value="price-desc">Fiyat: Yüksek-Düşük</option>
            <option value="name">İsim</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="text-center py-20">
      <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Ürünler yükleniyor...</p>
    </div>

    <!-- Ürün Listesi -->
    <div v-else-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow cursor-pointer"
        @click="goToProduct(product.id)"
      >
        <div class="relative pb-[100%]">
          <img
            :src="product.image || '/placeholder.png'"
            :alt="product.name"
            class="absolute inset-0 w-full h-full object-cover"
          />
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-lg mb-2 line-clamp-2 h-14">{{ product.name }}</h3>
          <p class="text-sm text-gray-600 mb-3 line-clamp-2 h-10">{{ product.description }}</p>
          
          <!-- Satıcı Bilgisi -->
          <p v-if="product.seller" class="text-xs text-gray-500 mb-2">
            🏪 {{ product.seller.name }}
          </p>
          
          <div class="flex items-center justify-between">
            <span class="text-2xl font-bold text-blue-600">{{ formatPrice(product.price) }} ₺</span>
            <button
              @click.stop="addToCart(product)"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold text-sm"
            >
              🛒 Sepete Ekle
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Ürün Bulunamadı -->
    <div v-else class="text-center py-20">
      <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
      </svg>
      <p class="text-xl text-gray-600">Ürün bulunamadı</p>
      <p class="text-gray-500 mt-2">Farklı bir kategori veya arama terimi deneyin</p>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

interface Category {
  id: number
  name: string
}

interface Product {
  id: number
  name: string
  description: string
  price: number
  image: string
  category_id: number
  seller?: {
    name: string
  }
}

const router = useRouter()
const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const selectedCategory = ref<number | ''>('')
const searchQuery = ref('')
const sortBy = ref('newest')
const loading = ref(true)

const filteredProducts = computed(() => {
  let filtered = products.value

  // Kategori filtresi
  if (selectedCategory.value) {
    filtered = filtered.filter(p => p.category_id === selectedCategory.value)
  }

  // Arama filtresi
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(p =>
      p.name.toLowerCase().includes(query) ||
      p.description?.toLowerCase().includes(query)
    )
  }

  // Sıralama
  const sorted = [...filtered]
  switch (sortBy.value) {
    case 'price-asc':
      sorted.sort((a, b) => a.price - b.price)
      break
    case 'price-desc':
      sorted.sort((a, b) => b.price - a.price)
      break
    case 'name':
      sorted.sort((a, b) => a.name.localeCompare(b.name, 'tr'))
      break
    case 'newest':
    default:
      sorted.reverse()
      break
  }

  return sorted
})

function formatPrice(price: number) {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price)
}

function goToProduct(productId: number) {
  router.push(`/products/${productId}`)
}

async function addToCart(product: Product) {
  try {
    await axios.post('/api/cart', {
      product_id: product.id,
      quantity: 1
    })
    alert(`${product.name} sepete eklendi!`)
  } catch (error: any) {
    if (error.response?.status === 401) {
      alert('Sepete eklemek için giriş yapmalısınız')
      router.push('/login')
    } else {
      console.error('Sepete eklenirken hata:', error)
      alert('Sepete eklenirken bir hata oluştu')
    }
  }
}

async function loadProducts() {
  loading.value = true
  try {
    const [productsRes, categoriesRes] = await Promise.all([
      axios.get('/api/products'),
      axios.get('/api/categories')
    ])
    products.value = productsRes.data
    categories.value = categoriesRes.data
  } catch (error) {
    console.error('Ürünler yüklenirken hata:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadProducts()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

const addToCart = (product: Product) => {
  // Vuex veya Pinia ile sepet yönetimi entegre edilebilir
  console.log('Sepete eklendi:', product.name)
}

onMounted(async () => {
  const [productRes, categoryRes] = await Promise.all([
    axios.get('/api/products'),
    axios.get('/api/categories')
  ])
  products.value = productRes.data
  categories.value = categoryRes.data
})
</script>

<style scoped>
.product-list {
  padding: 2rem;
}
.products {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}
.product-card {
  border: 1px solid #ccc;
  padding: 1rem;
  width: 200px;
}
.product-card img {
  width: 100%;
  height: auto;
}
</style>