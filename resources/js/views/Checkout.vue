<template>
  <div class="checkout-page max-w-7xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-8">Ödeme</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Sol Taraf - Teslimat ve Ödeme Bilgileri -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Teslimat Bilgileri -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">📦 Teslimat Bilgileri</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad</label>
              <input
                v-model="formData.name"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="Adınız ve soyadınız"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
              <input
                v-model="formData.phone"
                type="tel"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="0532 123 45 67"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Adres</label>
              <textarea
                v-model="formData.address"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="Teslimat adresiniz"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Ödeme Yöntemi Seçimi -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">💳 Ödeme Yöntemi</h2>
          <div class="space-y-3">
            <div
              v-for="method in paymentMethods"
              :key="method.value"
              @click="selectedPaymentMethod = method.value"
              :class="[
                'border-2 rounded-lg p-4 cursor-pointer transition-all',
                selectedPaymentMethod === method.value
                  ? 'border-blue-500 bg-blue-50'
                  : 'border-gray-200 hover:border-gray-300'
              ]"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <input
                    type="radio"
                    :value="method.value"
                    v-model="selectedPaymentMethod"
                    class="h-4 w-4 text-blue-600"
                  />
                  <div>
                    <div class="font-semibold">{{ method.name }}</div>
                    <div class="text-sm text-gray-600">{{ method.description }}</div>
                  </div>
                </div>
                <img v-if="method.logo" :src="method.logo" :alt="method.name" class="h-8" />
              </div>
            </div>
          </div>
        </div>

        <!-- Kargo Seçimi -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">🚚 Kargo Firması Seçimi</h2>
          
          <div v-if="loadingShippingQuotes" class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="text-gray-600 mt-2">Kargo fiyatları hesaplanıyor...</p>
          </div>

          <div v-else-if="shippingQuotes.length === 0" class="text-center py-8 text-gray-500">
            Teslimat bilgilerinizi doldurun, kargo seçenekleri gösterilecek.
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="quote in shippingQuotes"
              :key="quote.provider"
              @click="selectedShippingProvider = quote.provider"
              :class="[
                'border-2 rounded-lg p-4 cursor-pointer transition-all',
                selectedShippingProvider === quote.provider
                  ? 'border-green-500 bg-green-50'
                  : 'border-gray-200 hover:border-gray-300'
              ]"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <input
                    type="radio"
                    :value="quote.provider"
                    v-model="selectedShippingProvider"
                    class="h-4 w-4 text-green-600"
                  />
                  <div>
                    <div class="font-semibold">{{ quote.provider_name }}</div>
                    <div class="text-sm text-gray-600">
                      Tahmini Teslimat: {{ quote.estimated_delivery_days }} iş günü
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div v-if="quote.price > 0" class="font-bold text-lg">{{ formatPrice(quote.price) }} ₺</div>
                  <div v-else class="font-bold text-lg text-green-600">Ücretsiz</div>
                </div>
              </div>
            </div>
          </div>

          <button
            v-if="!loadingShippingQuotes && shippingQuotes.length === 0 && canGetShippingQuotes"
            @click="getShippingQuotes"
            class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-all"
          >
            Kargo Seçeneklerini Göster
          </button>
        </div>
      </div>

      <!-- Sağ Taraf - Sipariş Özeti -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 sticky top-8">
          <h2 class="text-xl font-semibold mb-4">Sipariş Özeti</h2>
          
          <!-- Sepet Ürünleri -->
          <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
            <div
              v-for="item in cartItems"
              :key="item.id"
              class="flex items-center space-x-3 pb-3 border-b"
            >
              <img
                :src="item.product.image || '/placeholder.png'"
                :alt="item.product.name"
                class="w-16 h-16 object-cover rounded"
              />
              <div class="flex-1">
                <div class="font-medium text-sm">{{ item.product.name }}</div>
                <div class="text-sm text-gray-600">{{ item.quantity }} adet</div>
              </div>
              <div class="font-semibold">{{ formatPrice(item.product.price * item.quantity) }} ₺</div>
            </div>
          </div>

          <!-- Fiyat Özeti -->
          <div class="space-y-2 mb-6 pt-4 border-t">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Ara Toplam</span>
              <span>{{ formatPrice(subtotal) }} ₺</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Kargo</span>
              <span :class="shippingCost > 0 ? '' : 'text-green-600'">
                {{ shippingCost > 0 ? formatPrice(shippingCost) + ' ₺' : 'Ücretsiz' }}
              </span>
            </div>
            <div class="flex justify-between text-lg font-bold pt-2 border-t">
              <span>Toplam</span>
              <span class="text-blue-600">{{ formatPrice(total) }} ₺</span>
            </div>
          </div>

          <!-- Ödeme Butonu -->
          <button
            @click="proceedToPayment"
            :disabled="!canProceed || isProcessing"
            :class="[
              'w-full py-3 rounded-lg font-semibold text-white transition-all',
              canProceed && !isProcessing
                ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer'
                : 'bg-gray-300 cursor-not-allowed'
            ]"
          >
            <span v-if="!isProcessing">Ödemeye Geç</span>
            <span v-else>İşleniyor...</span>
          </button>

          <!-- Güvenlik Bilgisi -->
          <div class="mt-4 text-center text-xs text-gray-500">
            🔒 Ödemeniz güvenli bir şekilde işlenir
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const formData = ref({
  name: '',
  phone: '',
  address: '',
});

const selectedPaymentMethod = ref('iyzico');
const selectedShippingProvider = ref<string | null>(null);
const cartItems = ref<any[]>([]);
const isProcessing = ref(false);
const loadingShippingQuotes = ref(false);
const shippingQuotes = ref<any[]>([]);

const paymentMethods = [
  {
    value: 'iyzico',
    name: 'Iyzico',
    description: 'Kredi/Banka Kartı ile ödeme',
    logo: 'https://www.iyzico.com/assets/images/content/logo-iyzico.svg'
  },
  {
    value: 'paytr',
    name: 'PayTR',
    description: 'Güvenli kredi kartı ödemesi',
    logo: null
  },
  {
    value: 'stripe',
    name: 'Stripe',
    description: 'Uluslararası kart ödemeleri',
    logo: 'https://stripe.com/img/v3/newsroom/social.png'
  }
];

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
});

const shippingCost = computed(() => {
  const selectedQuote = shippingQuotes.value.find(q => q.provider === selectedShippingProvider.value);
  return selectedQuote?.price || 0;
});

const total = computed(() => subtotal.value + shippingCost.value);

const canGetShippingQuotes = computed(() => {
  return formData.value.address.trim() !== '';
});

const canProceed = computed(() => {
  return (
    formData.value.name.trim() !== '' &&
    formData.value.phone.trim() !== '' &&
    formData.value.address.trim() !== '' &&
    selectedPaymentMethod.value !== '' &&
    selectedShippingProvider.value !== null &&
    cartItems.value.length > 0
  );
});

function formatPrice(price: number) {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price);
}

async function loadCart() {
  try {
    const response = await axios.get('/api/cart');
    cartItems.value = response.data;
  } catch (error) {
    console.error('Sepet yüklenirken hata:', error);
    alert('Sepet bilgileri yüklenemedi');
  }
}

async function getShippingQuotes() {
  if (!canGetShippingQuotes.value) {
    alert('Lütfen teslimat adresinizi girin');
    return;
  }

  loadingShippingQuotes.value = true;

  try {
    const totalWeight = cartItems.value.reduce((sum, item) => sum + (item.quantity * 0.5), 0);

    const response = await axios.post('/api/shipping/quotes', {
      origin: {
        city: 'İstanbul',
      },
      destination: {
        city: 'İstanbul', // Gerçek uygulamada adres parse edilmeli
        district: 'Kadıköy',
      },
      weight: totalWeight,
      dimensions: null,
    });

    if (response.data.success) {
      shippingQuotes.value = response.data.quotes;
      
      // İlk seçeneği otomatik seç
      if (shippingQuotes.value.length > 0) {
        selectedShippingProvider.value = shippingQuotes.value[0].provider;
      }
    }
  } catch (error) {
    console.error('Kargo fiyatları alınırken hata:', error);
    alert('Kargo seçenekleri yüklenemedi. Lütfen tekrar deneyin.');
  } finally {
    loadingShippingQuotes.value = false;
  }
}

async function proceedToPayment() {
  if (!canProceed.value || isProcessing.value) return;

  isProcessing.value = true;

  try {
    // 1. Sipariş oluştur
    const orderResponse = await axios.post('/api/orders', {
      address: formData.value.address,
      phone: formData.value.phone,
      shipping_provider: selectedShippingProvider.value,
    });

    const orderId = orderResponse.data.order_id;

    // 2. Ödeme işlemini başlat
    const paymentResponse = await axios.post('/api/payments/start', {
      order_id: orderId,
      payment_method: selectedPaymentMethod.value,
    });

    // 3. Ödeme sayfasına yönlendir
    if (paymentResponse.data.payment_page_url) {
      window.location.href = paymentResponse.data.payment_page_url;
    } else {
      alert('Ödeme sayfası URL\'si alınamadı');
      isProcessing.value = false;
    }
  } catch (error: any) {
    console.error('Ödeme işlemi başlatılırken hata:', error);
    alert(error.response?.data?.message || 'Ödeme işlemi başlatılamadı. Lütfen tekrar deneyin.');
    isProcessing.value = false;
  }
}

onMounted(() => {
  loadCart();
  
  // Kullanıcı bilgilerini otomatik doldur
  axios.get('/api/me').then(response => {
    if (response.data) {
      formData.value.name = response.data.name || '';
      formData.value.phone = response.data.phone || '';
      formData.value.address = response.data.address || '';
    }
  }).catch(() => {
    // Kullanıcı giriş yapmamış olabilir
  });
});
</script>

<style scoped>
/* Özel stil gerekirse eklenebilir */
</style>