<template>
  <div class="seller-dashboard max-w-7xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-8">Satıcı Paneli</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Toplam Ürün</div>
        <div class="text-3xl font-bold text-blue-600">{{ stats.total_products }}</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Toplam Sipariş</div>
        <div class="text-3xl font-bold text-green-600">{{ stats.total_orders }}</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Toplam Gelir</div>
        <div class="text-3xl font-bold text-purple-600">{{ formatPrice(stats.total_revenue) }} ₺</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-2">Ortalama Puan</div>
        <div class="text-3xl font-bold text-yellow-600">{{ formattedAvgRating }}</div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
      <div class="border-b">
        <div class="flex">
          <button 
            @click="activeTab = 'products'"
            :class="['px-6 py-4 font-semibold', activeTab === 'products' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600']"
          >
            Ürünlerim
          </button>
          <button 
            @click="activeTab = 'orders'"
            :class="['px-6 py-4 font-semibold', activeTab === 'orders' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600']"
          >
            Siparişler
          </button>
          <button 
            @click="activeTab = 'financials'"
            :class="['px-6 py-4 font-semibold', activeTab === 'financials' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600']"
          >
            Finans
          </button>
          <button 
            @click="activeTab = 'add-product'"
            :class="['px-6 py-4 font-semibold', activeTab === 'add-product' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600']"
          >
            + Yeni Ürün
          </button>
        </div>
      </div>

      <div class="p-6">
        <!-- Products Tab -->
        <div v-if="activeTab === 'products'">
          <div v-if="products.length === 0" class="text-center py-12 text-gray-500">
            Henüz ürün eklemediniz
          </div>
          <div v-else class="space-y-4">
            <div 
              v-for="product in products" 
              :key="product.id"
              class="flex gap-4 p-4 border rounded hover:bg-gray-50"
            >
              <div class="w-24 h-24 bg-gray-200 rounded flex-shrink-0">
                <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover rounded">
              </div>
              <div class="flex-1">
                <h3 class="font-semibold text-lg">{{ product.name }}</h3>
                <p class="text-gray-600 text-sm">{{ product.description }}</p>
                <div class="mt-2">
                  <span class="text-blue-600 font-bold">{{ formatPrice(product.price) }} ₺</span>
                  <span class="ml-4 text-sm text-gray-500">Stok: {{ product.stock }}</span>
                </div>
              </div>
              <div class="flex flex-col gap-2">
                <button @click="editProduct(product.id)" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                  Düzenle
                </button>
                <button @click="deleteProduct(product.id)" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                  Sil
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Orders Tab -->
        <div v-if="activeTab === 'orders'">
          <div v-if="orders.length === 0" class="text-center py-12 text-gray-500">
            Henüz sipariş yok
          </div>
          <div v-else class="space-y-4">
            <div 
              v-for="order in orders" 
              :key="order.id"
              class="p-4 border rounded"
            >
              <div class="flex justify-between items-start mb-4">
                <div>
                  <div class="font-semibold">Sipariş #{{ order.id }}</div>
                  <div class="text-sm text-gray-600">{{ new Date(order.created_at).toLocaleDateString('tr-TR') }}</div>
                  <div class="text-sm text-gray-500">Müşteri: {{ order.user.name }}</div>
                </div>
                <div class="text-right">
                  <div class="text-lg font-bold text-blue-600">{{ formatPrice(order.total) }} ₺</div>
                   <span :class="getStatusClass(order.status)" class="px-3 py-1 rounded-full text-xs font-medium mt-1 inline-block">
                    {{ order.status_tr }}
                  </span>
                </div>
              </div>
              
              <!-- Order Items -->
              <div class="border-t mt-4 pt-4 space-y-3">
                <h4 class="font-semibold text-sm text-gray-700">Sipariş İçeriği:</h4>
                <div v-for="item in order.order_items" :key="item.id" class="flex justify-between items-center">
                  <div>
                    <p class="font-medium">{{ item.product.name }} (x{{ item.quantity }})</p>
                    <p class="text-sm text-gray-600">{{ formatPrice(item.price) }} ₺</p>
                  </div>
                  <div class="flex items-center gap-2">
                     <span :class="getStatusClass(item.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ item.status_tr }}
                    </span>
                    <select 
                      @change="updateOrderItemStatus(item, $event.target.value)"
                      :value="item.status"
                      class="p-2 border rounded text-sm"
                    >
                      <option value="pending">Beklemede</option>
                      <option value="processing">İşleniyor</option>
                      <option value="shipped">Kargolandı</option>
                      <option value="delivered">Teslim Edildi</option>
                      <option value="cancelled">İptal Edildi</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Financials Tab -->
        <div v-if="activeTab === 'financials'">
          <FinancialReport />
        </div>

        <!-- Add Product Tab -->
        <div v-if="activeTab === 'add-product'">
          <form @submit.prevent="addProduct" class="max-w-2xl space-y-4">
            <div>
              <label class="block font-semibold mb-2">Ürün Adı *</label>
              <input v-model="newProduct.name" type="text" required class="w-full p-3 border rounded" />
            </div>
            <div>
              <label class="block font-semibold mb-2">Açıklama *</label>
              <textarea v-model="newProduct.description" required rows="4" class="w-full p-3 border rounded"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block font-semibold mb-2">Fiyat *</label>
                <input v-model.number="newProduct.price" type="number" step="0.01" required class="w-full p-3 border rounded" placeholder="0,00" />
              </div>
              <div>
                <label class="block font-semibold mb-2">Stok *</label>
                <input v-model.number="newProduct.stock" type="number" required class="w-full p-3 border rounded" placeholder="0" />
              </div>
            </div>
            <div>
              <label class="block font-semibold mb-2">Kategori *</label>
              <select v-model="newProduct.category_id" required class="w-full p-3 border rounded">
                <option value="">Seçiniz</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>
            <button type="submit" :disabled="addingProduct" class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold disabled:bg-gray-400">
              {{ addingProduct ? 'Ekleniyor...' : 'Ürün Ekle' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import FinancialReport from './FinancialReport.vue'

// ... (existing imports)

const router = useRouter()

const activeTab = ref('products')
const stats = ref({
  total_products: 0,
  total_orders: 0,
  total_revenue: 0,
  avg_rating: null
})

const products = ref<any[]>([])
const orders = ref<any[]>([])
const categories = ref<any[]>([])
const addingProduct = ref(false)

const newProduct = ref({
  name: '',
  description: '',
  price: null,
  stock: null,
  category_id: ''
})

// API Calls
async function loadDashboard() {
  try {
    const [statsRes, productsRes, ordersRes, catsRes] = await Promise.all([
      axios.get('/api/seller/stats'),
      axios.get('/api/seller/products'),
      axios.get('/api/seller/orders'),
      axios.get('/api/kategoriler')
    ])
    
    stats.value = statsRes.data
    products.value = productsRes.data
    orders.value = ordersRes.data
    categories.value = catsRes.data
  } catch (error) {
    console.error('Dashboard verileri yüklenirken bir hata oluştu:', error)
    // Optionally, show a user-friendly error message
  }
}

async function addProduct() {
  if (!newProduct.value.name || !newProduct.value.description || !newProduct.value.price || !newProduct.value.stock || !newProduct.value.category_id) {
    alert('Lütfen tüm zorunlu alanları doldurun.');
    return;
  }
  addingProduct.value = true
  try {
    await axios.post('/api/products', newProduct.value)
    alert('Ürün başarıyla eklendi!')
    // Reset form
    newProduct.value = { name: '', description: '', price: null, stock: null, category_id: '' }
    activeTab.value = 'products'
    await loadDashboard() // Refresh data
  } catch (error) {
    console.error('Ürün eklenirken hata:', error)
    alert('Ürün eklenirken bir hata oluştu. Lütfen tekrar deneyin.')
  } finally {
    addingProduct.value = false
  }
}

function editProduct(id: number) {
  router.push(`/seller/product/${id}/edit`)
}

async function deleteProduct(id: number) {
  if (!confirm('Bu ürünü silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) return
  
  try {
    await axios.delete(`/api/products/${id}`)
    alert('Ürün başarıyla silindi.')
    await loadDashboard() // Refresh data
  } catch (error) {
    console.error('Ürün silinirken hata:', error)
    alert('Ürün silinirken bir hata oluştu.')
  }
}

async function updateOrderItemStatus(item: any, newStatus: string) {
  try {
    const response = await axios.patch(`/api/seller/order-items/${item.id}/status`, {
      status: newStatus,
    });
    
    // Update the local state to reflect the change immediately
    item.status = response.data.order_item.status;
    item.status_tr = response.data.order_item.status_tr; // Assuming the API returns a translated status

    alert('Sipariş durumu başarıyla güncellendi.');
    
    // Optionally, refresh all order data to ensure consistency
    // await loadDashboard(); 
  } catch (error) {
    console.error('Sipariş durumu güncellenirken hata:', error);
    alert('Sipariş durumu güncellenirken bir hata oluştu. Yetkiniz olmayabilir veya geçersiz bir durum seçtiniz.');
    // Revert the select box to the original value if the API call fails
    // This requires a bit more complex state management, e.g., storing the original status before the change.
  }
}


// UI Helpers
function getStatusClass(status: string) {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

function formatPrice(price: number | null | undefined) {
  if (price === null || price === undefined) return '0,00';
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price)
}

const formattedAvgRating = computed(() => {
  return stats.value.avg_rating ? Number(stats.value.avg_rating).toFixed(1) : '-';
});

// Lifecycle Hook
onMounted(() => {
  loadDashboard()
})
</script>

<style scoped>
.seller-dashboard { padding: 2rem; }
.stats { display: flex; gap: 1rem; margin-bottom: 1rem; }
.stat-box { background: #f5f5f5; padding: 1rem; border-radius: 6px; min-width: 120px; }
.campaign-form { margin-bottom: 1rem; display: flex; gap: 0.5rem; }
.chart { background: #e0e7ff; height: 120px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.empty { color: #888; font-style: italic; }
button { margin-left: 0.5rem; }
</style>