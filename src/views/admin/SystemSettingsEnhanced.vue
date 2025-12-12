<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
            ⚙️ Sistem Ayarları
            <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">ADMIN</span>
          </h1>
          <p class="text-slate-500 text-sm mt-1">Kargo, ödeme, komisyon ve abonelik yönetimi</p>
        </div>
        <button @click="saveAllSettings" class="px-6 py-2 bg-orange-600 text-white rounded-lg font-bold hover:bg-orange-700 transition shadow-lg">
          💾 Ayarları Kaydet
        </button>
      </div>
    </header>

    <!-- Tabs -->
    <div class="bg-white border-b border-slate-200">
      <div class="px-6 flex gap-1">
        <button @click="activeTab = 'shipping'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'shipping' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          🚚 Kargo
        </button>
        <button @click="activeTab = 'payment'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'payment' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          💳 Ödeme
        </button>
        <button @click="activeTab = 'commission'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'commission' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          💰 Komisyon
        </button>
        <button @click="activeTab = 'subscription'" class="px-6 py-3 font-medium border-b-2 transition" :class="activeTab === 'subscription' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600 hover:text-slate-800'">
          📦 Abonelik
        </button>
      </div>
    </div>

    <!-- Shipping Tab -->
    <div v-if="activeTab === 'shipping'" class="p-6">
      <div class="max-w-5xl space-y-6">
        <!-- Carriers -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Kargo Taşıyıcıları</h2>
            <button @click="showCarrierModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
              ➕ Taşıyıcı Ekle
            </button>
          </div>
          <div class="divide-y divide-slate-100">
            <div v-for="carrier in carriers" :key="carrier.id" class="p-6 hover:bg-slate-50">
              <div class="flex items-start justify-between">
                <div class="flex items-start gap-4 flex-1">
                  <div class="w-16 h-16 bg-slate-100 rounded-lg flex items-center justify-center overflow-hidden">
                    <img v-if="carrier.logo" :src="carrier.logo" class="w-full h-full object-cover" alt="">
                    <span v-else class="text-2xl">🚚</span>
                  </div>
                  <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                      <h3 class="text-lg font-bold text-slate-800">{{ carrier.name }}</h3>
                      <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="carrier.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'">
                        {{ carrier.is_active ? 'Aktif' : 'Pasif' }}
                      </span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                      <div>
                        <p class="text-slate-500 text-xs">Sabit Ücret</p>
                        <p class="font-medium text-slate-800">{{ formatCurrency(carrier.base_price) }}</p>
                      </div>
                      <div>
                        <p class="text-slate-500 text-xs">Kg Başına</p>
                        <p class="font-medium text-slate-800">{{ formatCurrency(carrier.price_per_kg) }}</p>
                      </div>
                      <div>
                        <p class="text-slate-500 text-xs">Ücretsiz Kargo Limiti</p>
                        <p class="font-medium text-slate-800">{{ formatCurrency(carrier.free_shipping_limit) }}</p>
                      </div>
                      <div>
                        <p class="text-slate-500 text-xs">Teslimat Süresi</p>
                        <p class="font-medium text-slate-800">{{ carrier.delivery_time }}</p>
                      </div>
                      <div>
                        <p class="text-slate-500 text-xs">SLA (gün)</p>
                        <p class="font-medium text-slate-800">{{ carrier.sla_days }} gün</p>
                      </div>
                      <div>
                        <p class="text-slate-500 text-xs">Kapsama Alanı</p>
                        <p class="font-medium text-slate-800">{{ carrier.regions?.length || 0 }} bölge</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="flex gap-2">
                  <button @click="editCarrier(carrier)" class="px-3 py-1 text-orange-600 hover:text-orange-800 text-sm font-medium">Düzenle</button>
                  <button @click="deleteCarrier(carrier)" class="px-3 py-1 text-red-600 hover:text-red-800 text-sm font-medium">Sil</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Regions -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
          <h2 class="text-lg font-bold text-slate-800 mb-4">Bölge Tanımları</h2>
          <div class="grid grid-cols-2 gap-4">
            <div v-for="region in regions" :key="region.id" class="p-4 border border-slate-200 rounded-lg">
              <div class="flex justify-between items-start mb-2">
                <h3 class="font-medium text-slate-800">{{ region.name }}</h3>
                <button class="text-slate-400 hover:text-slate-600 text-sm">⚙️</button>
              </div>
              <p class="text-sm text-slate-500 mb-2">{{ region.cities?.join(', ') }}</p>
              <div class="flex justify-between text-xs">
                <span class="text-slate-600">Ek Ücret:</span>
                <span class="font-medium text-slate-800">{{ formatCurrency(region.extra_charge) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Tab -->
    <div v-if="activeTab === 'payment'" class="p-6">
      <div class="max-w-5xl space-y-6">
        <!-- Payment Providers -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Ödeme Sağlayıcıları</h2>
            <button @click="showProviderModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
              ➕ Sağlayıcı Ekle
            </button>
          </div>
          <div class="divide-y divide-slate-100">
            <div v-for="provider in paymentProviders" :key="provider.id" class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                  <div class="w-16 h-16 bg-slate-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">{{ provider.icon }}</span>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-slate-800">{{ provider.name }}</h3>
                    <p class="text-sm text-slate-500">{{ provider.type }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="provider.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'">
                    {{ provider.is_active ? 'Aktif' : 'Pasif' }}
                  </span>
                  <button @click="toggleProvider(provider)" class="px-3 py-1 text-orange-600 hover:text-orange-800 text-sm font-medium">Düzenle</button>
                </div>
              </div>

              <div class="grid grid-cols-4 gap-4">
                <div class="bg-slate-50 p-3 rounded-lg">
                  <p class="text-xs text-slate-500 mb-1">3D Secure</p>
                  <p class="font-medium text-slate-800">{{ provider.requires_3d ? 'Zorunlu' : 'Opsiyonel' }}</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-lg">
                  <p class="text-xs text-slate-500 mb-1">Taksit</p>
                  <p class="font-medium text-slate-800">Max {{ provider.max_installment }} ay</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-lg">
                  <p class="text-xs text-slate-500 mb-1">Komisyon</p>
                  <p class="font-medium text-slate-800">{{ provider.commission_rate }}%</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-lg">
                  <p class="text-xs text-slate-500 mb-1">İade Süresi</p>
                  <p class="font-medium text-slate-800">{{ provider.refund_days }} gün</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Installment Settings -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
          <h2 class="text-lg font-bold text-slate-800 mb-4">Taksit Ayarları</h2>
          <div class="space-y-3">
            <div v-for="inst in installmentRules" :key="inst.months" class="flex items-center justify-between p-4 border border-slate-200 rounded-lg">
              <div class="flex items-center gap-4">
                <span class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center font-bold text-orange-600">{{ inst.months }}</span>
                <div>
                  <p class="font-medium text-slate-800">{{ inst.months }} Taksit</p>
                  <p class="text-sm text-slate-500">Min tutar: {{ formatCurrency(inst.min_amount) }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="font-medium text-slate-800">Komisyon: {{ inst.commission_rate }}%</p>
                <p class="text-xs text-slate-500">Müşteriye ek: +{{ inst.customer_fee }}%</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Commission Tab -->
    <div v-if="activeTab === 'commission'" class="p-6">
      <div class="max-w-5xl space-y-6">
        <!-- Default Commission Rates -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
          <h2 class="text-lg font-bold text-slate-800 mb-4">Kategori Bazlı Komisyon Oranları</h2>
          <table class="w-full">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Kategori</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Komisyon Oranı</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Min Komisyon</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Max Komisyon</th>
                <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">İşlemler</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="rate in commissionRates" :key="rate.id" class="hover:bg-slate-50">
                <td class="px-4 py-4">
                  <div class="font-medium text-slate-800">{{ rate.category }}</div>
                  <div class="text-xs text-slate-500">{{ rate.subcategories?.length || 0 }} alt kategori</div>
                </td>
                <td class="px-4 py-4">
                  <div class="font-bold text-orange-600">{{ rate.rate }}%</div>
                </td>
                <td class="px-4 py-4 text-sm text-slate-600">{{ formatCurrency(rate.min_commission) }}</td>
                <td class="px-4 py-4 text-sm text-slate-600">{{ formatCurrency(rate.max_commission) }}</td>
                <td class="px-4 py-4 text-right">
                  <button @click="editCommissionRate(rate)" class="text-orange-600 hover:text-orange-800 text-sm font-medium">Düzenle</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Seller Specific Rates -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Satıcı Özel Komisyon Oranları</h2>
            <button @click="showSellerRateModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
              ➕ Özel Oran Ekle
            </button>
          </div>
          <div class="divide-y divide-slate-100">
            <div v-for="sellerRate in sellerCommissionRates" :key="sellerRate.id" class="p-6">
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-bold text-slate-800 mb-1">{{ sellerRate.seller_name }}</h3>
                  <p class="text-sm text-slate-500 mb-3">{{ sellerRate.reason }}</p>
                  <div class="flex gap-4 text-sm">
                    <div>
                      <span class="text-slate-600">Oran: </span>
                      <span class="font-bold text-orange-600">{{ sellerRate.rate }}%</span>
                    </div>
                    <div>
                      <span class="text-slate-600">Geçerlilik: </span>
                      <span class="font-medium text-slate-800">{{ formatDate(sellerRate.valid_from) }} - {{ formatDate(sellerRate.valid_until) }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex gap-2">
                  <button @click="editSellerRate(sellerRate)" class="px-3 py-1 text-orange-600 hover:text-orange-800 text-sm font-medium">Düzenle</button>
                  <button @click="deleteSellerRate(sellerRate)" class="px-3 py-1 text-red-600 hover:text-red-800 text-sm font-medium">Sil</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Subscription Tab -->
    <div v-if="activeTab === 'subscription'" class="p-6">
      <div class="max-w-5xl space-y-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-slate-800">Abonelik Planları</h2>
          <button @click="showPlanModal = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
            ➕ Yeni Plan
          </button>
        </div>

        <div class="grid grid-cols-3 gap-6">
          <div v-for="plan in subscriptionPlans" :key="plan.id" class="bg-white rounded-xl border-2 shadow-lg overflow-hidden" :class="plan.is_popular ? 'border-orange-500 relative' : 'border-slate-200'">
            <div v-if="plan.is_popular" class="absolute top-4 right-4 px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-full">POPÜLERr</div>
            
            <div class="p-6 bg-gradient-to-br" :class="getPlanGradient(plan.tier)">
              <h3 class="text-2xl font-bold text-white mb-2">{{ plan.name }}</h3>
              <div class="flex items-baseline gap-2 mb-1">
                <span class="text-4xl font-bold text-white">{{ formatCurrency(plan.price) }}</span>
                <span class="text-white text-opacity-80">/ {{ plan.billing_period }}</span>
              </div>
              <p class="text-white text-opacity-90 text-sm">{{ plan.description }}</p>
            </div>

            <div class="p-6">
              <div class="space-y-3 mb-6">
                <div v-for="feature in plan.features" :key="feature.name" class="flex items-start gap-3">
                  <span class="text-green-500 text-lg flex-shrink-0">{{ feature.included ? '✓' : '−' }}</span>
                  <div class="flex-1">
                    <p class="text-sm text-slate-800">{{ feature.name }}</p>
                    <p v-if="feature.value" class="text-xs text-slate-500">{{ feature.value }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-slate-50 p-4 rounded-lg mb-4">
                <div class="grid grid-cols-2 gap-3 text-xs">
                  <div>
                    <p class="text-slate-500">Komisyon Oranı</p>
                    <p class="font-bold text-slate-800">{{ plan.commission_rate }}%</p>
                  </div>
                  <div>
                    <p class="text-slate-500">Aylık Limit</p>
                    <p class="font-bold text-slate-800">{{ plan.monthly_limit || 'Sınırsız' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-500">Aktif Satıcı</p>
                    <p class="font-bold text-slate-800">{{ plan.active_sellers || 0 }}</p>
                  </div>
                  <div>
                    <p class="text-slate-500">Aylık Gelir</p>
                    <p class="font-bold text-green-600">{{ formatCurrency((plan.active_sellers || 0) * plan.price) }}</p>
                  </div>
                </div>
              </div>

              <div class="flex gap-2">
                <button @click="editPlan(plan)" class="flex-1 px-4 py-2 bg-orange-50 text-orange-600 rounded-lg text-sm font-medium hover:bg-orange-100 transition">Düzenle</button>
                <button @click="deletePlan(plan)" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition">Sil</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Carrier Modal -->
    <Transition name="modal">
      <div v-if="showCarrierModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showCarrierModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center sticky top-0 bg-white">
            <h2 class="text-xl font-bold text-slate-800">Yeni Kargo Taşıyıcısı</h2>
            <button @click="showCarrierModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>

          <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Taşıyıcı Adı *</label>
                <input v-model="carrierForm.name" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Sabit Ücret (TL)</label>
                <input v-model.number="carrierForm.base_price" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Kg Başına (TL)</label>
                <input v-model.number="carrierForm.price_per_kg" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Ücretsiz Kargo Limiti (TL)</label>
                <input v-model.number="carrierForm.free_shipping_limit" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Teslimat Süresi</label>
                <input v-model="carrierForm.delivery_time" type="text" placeholder="1-3 iş günü" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">SLA (gün)</label>
                <input v-model.number="carrierForm.sla_days" type="number" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Logo URL</label>
                <input v-model="carrierForm.logo" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              </div>
            </div>
          </div>

          <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
            <button @click="showCarrierModal = false" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition">İptal</button>
            <button @click="saveCarrier" class="px-6 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">Kaydet</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

// Tab State
const activeTab = ref('shipping')

// Carriers
const carriers = ref([
  { id: 1, name: 'Yurtiçi Kargo', base_price: 29.90, price_per_kg: 5, free_shipping_limit: 500, delivery_time: '1-3 iş günü', sla_days: 3, is_active: true, logo: null, regions: ['Marmara', 'Ege', 'Akdeniz'] },
  { id: 2, name: 'Aras Kargo', base_price: 24.90, price_per_kg: 4.5, free_shipping_limit: 400, delivery_time: '2-4 iş günü', sla_days: 4, is_active: true, logo: null, regions: ['Tüm Türkiye'] },
  { id: 3, name: 'MNG Kargo', base_price: 27.90, price_per_kg: 4.8, free_shipping_limit: 450, delivery_time: '1-3 iş günü', sla_days: 3, is_active: true, logo: null, regions: ['Tüm Türkiye'] },
])

const regions = ref([
  { id: 1, name: 'Marmara Bölgesi', cities: ['İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Kocaeli'], extra_charge: 0 },
  { id: 2, name: 'Doğu Anadolu', cities: ['Erzurum', 'Van', 'Elazığ', 'Malatya'], extra_charge: 15 },
  { id: 3, name: 'Güneydoğu Anadolu', cities: ['Diyarbakır', 'Şanlıurfa', 'Gaziantep', 'Mardin'], extra_charge: 12 },
])

// Payment Providers
const paymentProviders = ref([
  { id: 1, name: 'İyzico', type: 'Kredi Kartı', icon: '💳', is_active: true, requires_3d: true, max_installment: 12, commission_rate: 2.9, refund_days: 14 },
  { id: 2, name: 'PayTR', type: 'Kredi Kartı', icon: '💳', is_active: true, requires_3d: true, max_installment: 9, commission_rate: 2.5, refund_days: 14 },
  { id: 3, name: 'Havale/EFT', type: 'Banka Transferi', icon: '🏦', is_active: true, requires_3d: false, max_installment: 0, commission_rate: 0, refund_days: 7 },
])

const installmentRules = ref([
  { months: 3, min_amount: 500, commission_rate: 0, customer_fee: 0 },
  { months: 6, min_amount: 1000, commission_rate: 1.5, customer_fee: 0.5 },
  { months: 9, min_amount: 2000, commission_rate: 2.5, customer_fee: 1.0 },
  { months: 12, min_amount: 3000, commission_rate: 3.5, customer_fee: 1.5 },
])

// Commission
const commissionRates = ref([
  { id: 1, category: 'Elektronik', rate: 15, min_commission: 10, max_commission: 5000, subcategories: ['Telefon', 'Bilgisayar', 'Tablet'] },
  { id: 2, category: 'Giyim', rate: 20, min_commission: 5, max_commission: 1000, subcategories: ['Kadın', 'Erkek', 'Çocuk'] },
  { id: 3, category: 'Ev & Yaşam', rate: 12, min_commission: 8, max_commission: 2000, subcategories: ['Mobilya', 'Dekorasyon'] },
  { id: 4, category: 'Spor', rate: 18, min_commission: 7, max_commission: 1500, subcategories: ['Fitness', 'Outdoor'] },
])

const sellerCommissionRates = ref([
  { id: 1, seller_name: 'Tech Store', rate: 10, reason: 'Yüksek ciro sözleşmesi', valid_from: '2025-01-01', valid_until: '2025-12-31' },
  { id: 2, seller_name: 'Fashion House', rate: 15, reason: 'Yeni satıcı teşvik', valid_from: '2025-06-01', valid_until: '2025-12-31' },
])

// Subscription Plans
const subscriptionPlans = ref([
  {
    id: 1,
    name: 'Başlangıç',
    tier: 'basic',
    price: 499,
    billing_period: 'ay',
    description: 'Küçük işletmeler için',
    commission_rate: 20,
    monthly_limit: 10000,
    active_sellers: 45,
    is_popular: false,
    features: [
      { name: 'Ürün listeleme', included: true, value: '100 ürüne kadar' },
      { name: 'Temel raporlar', included: true },
      { name: 'Email destek', included: true },
      { name: 'Gelişmiş analitik', included: false },
      { name: 'Öncelikli destek', included: false },
    ]
  },
  {
    id: 2,
    name: 'Profesyonel',
    tier: 'pro',
    price: 999,
    billing_period: 'ay',
    description: 'Büyüyen işletmeler için',
    commission_rate: 15,
    monthly_limit: 50000,
    active_sellers: 128,
    is_popular: true,
    features: [
      { name: 'Ürün listeleme', included: true, value: 'Sınırsız' },
      { name: 'Gelişmiş raporlar', included: true },
      { name: 'Öncelikli email destek', included: true },
      { name: 'Gelişmiş analitik', included: true },
      { name: 'API erişimi', included: true },
    ]
  },
  {
    id: 3,
    name: 'Kurumsal',
    tier: 'enterprise',
    price: 2499,
    billing_period: 'ay',
    description: 'Kurumsal çözümler',
    commission_rate: 10,
    monthly_limit: null,
    active_sellers: 34,
    is_popular: false,
    features: [
      { name: 'Ürün listeleme', included: true, value: 'Sınırsız' },
      { name: 'Özel raporlar', included: true },
      { name: '7/24 destek', included: true },
      { name: 'Özel analitik', included: true },
      { name: 'Özel API', included: true },
      { name: 'Hesap yöneticisi', included: true },
    ]
  }
])

// Modals
const showCarrierModal = ref(false)
const showProviderModal = ref(false)
const showSellerRateModal = ref(false)
const showPlanModal = ref(false)

// Forms
const carrierForm = ref({
  name: '',
  base_price: 0,
  price_per_kg: 0,
  free_shipping_limit: 0,
  delivery_time: '',
  sla_days: 0,
  logo: '',
  is_active: true
})

// Actions
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 2 }).format(value)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR')
}

const getPlanGradient = (tier: string) => {
  switch (tier) {
    case 'basic': return 'from-blue-500 to-blue-600'
    case 'pro': return 'from-orange-500 to-orange-600'
    case 'enterprise': return 'from-purple-500 to-purple-600'
    default: return 'from-slate-500 to-slate-600'
  }
}

const saveAllSettings = () => {
  toast.success('Tüm ayarlar kaydedildi')
}

const editCarrier = (carrier: any) => {
  carrierForm.value = { ...carrier }
  showCarrierModal.value = true
}

const deleteCarrier = (carrier: any) => {
  if (confirm(`${carrier.name} silinecek. Emin misiniz?`)) {
    const index = carriers.value.findIndex(c => c.id === carrier.id)
    if (index > -1) {
      carriers.value.splice(index, 1)
      toast.success('Taşıyıcı silindi')
    }
  }
}

const saveCarrier = () => {
  if (!carrierForm.value.name) {
    toast.error('Taşıyıcı adı gerekli')
    return
  }

  const newCarrier = {
    id: carriers.value.length + 1,
    ...carrierForm.value,
    regions: []
  }
  carriers.value.push(newCarrier)
  toast.success('Taşıyıcı eklendi')
  showCarrierModal.value = false
  carrierForm.value = { name: '', base_price: 0, price_per_kg: 0, free_shipping_limit: 0, delivery_time: '', sla_days: 0, logo: '', is_active: true }
}

const toggleProvider = (provider: any) => {
  toast.info(`${provider.name} düzenleme özelliği yakında`)
}

const editCommissionRate = (rate: any) => {
  toast.info('Komisyon düzenleme özelliği yakında')
}

const editSellerRate = (rate: any) => {
  toast.info('Satıcı oranı düzenleme özelliği yakında')
}

const deleteSellerRate = (rate: any) => {
  if (confirm(`${rate.seller_name} için özel oran silinecek. Emin misiniz?`)) {
    const index = sellerCommissionRates.value.findIndex(r => r.id === rate.id)
    if (index > -1) {
      sellerCommissionRates.value.splice(index, 1)
      toast.success('Özel oran silindi')
    }
  }
}

const editPlan = (plan: any) => {
  toast.info(`${plan.name} planı düzenleme özelliği yakında`)
}

const deletePlan = (plan: any) => {
  if (plan.active_sellers > 0) {
    toast.error('Aktif satıcısı olan plan silinemez')
    return
  }
  
  if (confirm(`${plan.name} planı silinecek. Emin misiniz?`)) {
    const index = subscriptionPlans.value.findIndex(p => p.id === plan.id)
    if (index > -1) {
      subscriptionPlans.value.splice(index, 1)
      toast.success('Plan silindi')
    }
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
