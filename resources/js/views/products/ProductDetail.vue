<template>
  <div class="product-detail-page max-w-7xl mx-auto p-8">
    <div v-if="loading" class="text-center py-20">
      <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Ürün yükleniyor...</p>
    </div>

    <div v-else-if="product" class="space-y-8">
      <!-- Ürün Ana Bilgileri -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ürün Görseli -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
          <img
            :src="product.image || '/placeholder.png'"
            :alt="product.name"
            class="w-full h-[500px] object-cover"
          />
        </div>

        <!-- Ürün Bilgileri ve Sepet -->
        <div class="space-y-6">
          <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-2 text-sm text-gray-500">{{ product.category }}</div>
            <h1 class="text-3xl font-bold mb-4">{{ product.name }}</h1>
            
            <div class="flex items-baseline space-x-3 mb-6">
              <span class="text-4xl font-bold text-blue-600">{{ formatPrice(product.price) }} ₺</span>
            </div>

            <div class="mb-6 pb-6 border-b">
              <p class="text-gray-700">{{ product.description }}</p>
            </div>

            <!-- Miktar Seçimi -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Miktar</label>
              <div class="flex items-center space-x-3">
                <button
                  @click="quantity = Math.max(1, quantity - 1)"
                  class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100"
                >
                  -
                </button>
                <input
                  v-model.number="quantity"
                  type="number"
                  min="1"
                  class="w-20 px-4 py-2 border border-gray-300 rounded-lg text-center"
                />
                <button
                  @click="quantity++"
                  class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100"
                >
                  +
                </button>
              </div>
            </div>

            <!-- Sepete Ekle -->
            <button
              @click="addToCart"
              class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all"
            >
              🛒 Sepete Ekle
            </button>

            <!-- Ürün İstatistikleri -->
            <div class="mt-6 pt-6 border-t grid grid-cols-2 gap-4 text-center">
              <div>
                <div class="text-2xl font-bold text-gray-800">{{ product.order_count }}</div>
                <div class="text-sm text-gray-600">Satış</div>
              </div>
              <div>
                <div class="text-2xl font-bold text-gray-800">{{ product.comments_count }}</div>
                <div class="text-sm text-gray-600">Yorum</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Satıcı Bilgileri -->
      <div v-if="product.seller" class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4">👤 Satıcı Bilgileri</h2>
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
              <span class="text-2xl font-bold text-blue-600">{{ product.seller.name[0] }}</span>
            </div>
            <div>
              <div class="text-xl font-semibold">{{ product.seller.name }}</div>
              <div class="flex items-center space-x-2">
                <span class="text-yellow-500">⭐</span>
                <span class="font-semibold">{{ product.seller.rating }}</span>
                <span class="text-gray-600 text-sm">/5.0</span>
              </div>
            </div>
          </div>
          <button
            @click="viewSellerStore"
            class="px-6 py-2 border-2 border-blue-600 text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-all"
          >
            Mağazayı Ziyaret Et
          </button>
        </div>
      </div>

      <!-- Satıcının Diğer Ürünleri -->
      <div v-if="product.seller && product.seller.other_products.length > 0" class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Bu Satıcının Diğer Ürünleri</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div
            v-for="otherProduct in product.seller.other_products"
            :key="otherProduct.id"
            @click="goToProduct(otherProduct.id)"
            class="cursor-pointer hover:shadow-lg transition-all rounded-lg overflow-hidden border border-gray-200"
          >
            <img
              :src="otherProduct.image || '/placeholder.png'"
              :alt="otherProduct.name"
              class="w-full h-40 object-cover"
            />
            <div class="p-3">
              <div class="text-sm font-medium text-gray-800 truncate">{{ otherProduct.name }}</div>
              <div class="text-lg font-bold text-blue-600 mt-1">{{ formatPrice(otherProduct.price) }} ₺</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-20">
      <p class="text-xl text-gray-600">Ürün bulunamadı</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const product = ref<any>(null);
const loading = ref(true);
const quantity = ref(1);

function formatPrice(price: number) {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price);
}

async function loadProduct() {
  loading.value = true;
  try {
    const res = await axios.get(`/api/products/${route.params.id}`);
    product.value = res.data;
  } catch (error) {
    console.error('Ürün verisi alınamadı:', error);
    product.value = null;
  } finally {
    loading.value = false;
  }
}

async function addToCart() {
  try {
    await axios.post('/api/cart', {
      product_id: product.value.id,
      quantity: quantity.value,
    });
    alert(`${product.value.name} sepete eklendi!`);
  } catch (error) {
    console.error('Sepete eklenirken hata:', error);
    alert('Sepete eklenirken bir hata oluştu');
  }
}

function viewSellerStore() {
  if (product.value?.seller) {
    router.push(`/seller/${product.value.seller.id}/products`);
  }
}

function goToProduct(productId: number) {
  router.push(`/products/${productId}`);
}

// Route değiştiğinde ürünü yeniden yükle
watch(() => route.params.id, () => {
  if (route.params.id) {
    loadProduct();
  }
});

onMounted(() => {
  loadProduct();
});
</script>

<style scoped>
/* Özel stiller gerekirse eklenebilir */
</style>