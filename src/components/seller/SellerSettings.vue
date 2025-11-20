<template>
  <div class="seller-settings p-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Mağaza Ayarları</h1>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
      <nav class="flex space-x-8">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors"
          :class="activeTab === tab.id
            ? 'border-blue-500 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        >
          {{ tab.icon }} {{ tab.label }}
        </button>
      </nav>
    </div>

    <!-- Store Profile Tab -->
    <div v-if="activeTab === 'profile'" class="space-y-6">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Mağaza Bilgileri</h2>
        <form @submit.prevent="saveProfile" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Mağaza Adı *</label>
              <input
                v-model="profile.store_name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Mağaza Açıklaması</label>
              <textarea
                v-model="profile.description"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="Mağazanızı tanıtan kısa bir açıklama yazın..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Logo URL</label>
              <input
                v-model="profile.logo_url"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="https://..."
              />
              <div v-if="profile.logo_url" class="mt-2">
                <img :src="profile.logo_url" alt="Logo" class="h-20 w-20 object-cover rounded border" />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kapak Görseli URL</label>
              <input
                v-model="profile.cover_url"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="https://..."
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">İletişim E-posta *</label>
              <input
                v-model="profile.contact_email"
                type="email"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">İletişim Telefon *</label>
              <input
                v-model="profile.contact_phone"
                type="tel"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="0555 123 45 67"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
              <textarea
                v-model="profile.address"
                rows="2"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
              <input
                v-model="profile.city"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Posta Kodu</label>
              <input
                v-model="profile.postal_code"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <div class="flex justify-end pt-4 border-t">
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Payment Methods Tab -->
    <div v-if="activeTab === 'payment'" class="space-y-6">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Ödeme Bilgileri</h2>
        <form @submit.prevent="savePayment" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Banka Adı *</label>
              <select
                v-model="payment.bank_name"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Seçiniz</option>
                <option value="Ziraat Bankası">Ziraat Bankası</option>
                <option value="İş Bankası">İş Bankası</option>
                <option value="Garanti BBVA">Garanti BBVA</option>
                <option value="Akbank">Akbank</option>
                <option value="Yapı Kredi">Yapı Kredi</option>
                <option value="QNB Finansbank">QNB Finansbank</option>
                <option value="Denizbank">Denizbank</option>
                <option value="TEB">TEB</option>
                <option value="Vakıfbank">Vakıfbank</option>
                <option value="Halkbank">Halkbank</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Hesap Sahibi *</label>
              <input
                v-model="payment.account_holder"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">IBAN *</label>
              <input
                v-model="payment.iban"
                type="text"
                required
                maxlength="32"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono"
                placeholder="TR00 0000 0000 0000 0000 0000 00"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şube Kodu</label>
              <input
                v-model="payment.branch_code"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
            <strong>ℹ️ Bilgi:</strong> Ödeme bilgileri yalnızca satış gelirlerinizin aktarılması için kullanılacaktır. Güvenliğiniz için bilgileriniz şifrelenerek saklanır.
          </div>

          <div class="flex justify-end pt-4 border-t">
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Shipping Tab -->
    <div v-if="activeTab === 'shipping'" class="space-y-6">
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-gray-900">Kargo Ayarları</h2>
          <button
            @click="addShippingZone"
            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold text-sm"
          >
            + Yeni Bölge Ekle
          </button>
        </div>

        <!-- Shipping Carriers -->
        <div class="mb-6">
          <h3 class="font-semibold text-gray-900 mb-3">Kargo Firmaları</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <label
              v-for="carrier in availableCarriers"
              :key="carrier.id"
              class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
            >
              <input
                type="checkbox"
                v-model="shipping.carriers"
                :value="carrier.id"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              />
              <span class="text-sm font-medium text-gray-700">{{ carrier.name }}</span>
            </label>
          </div>
        </div>

        <!-- Shipping Zones -->
        <div>
          <h3 class="font-semibold text-gray-900 mb-3">Kargo Bölgeleri ve Ücretleri</h3>
          <div class="space-y-3">
            <div
              v-for="(zone, index) in shipping.zones"
              :key="index"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Bölge Adı</label>
                  <input
                    v-model="zone.name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="Örn: İstanbul"
                  />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Kargo Ücreti (₺)</label>
                  <input
                    v-model.number="zone.price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                  />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Ücretsiz Kargo Limiti (₺)</label>
                  <input
                    v-model.number="zone.free_shipping_threshold"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                  />
                </div>
                <div class="flex items-end">
                  <button
                    @click="removeShippingZone(index)"
                    class="w-full px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm font-medium"
                  >
                    Sil
                  </button>
                </div>
              </div>
            </div>

            <div v-if="shipping.zones.length === 0" class="text-center py-8 text-gray-500">
              Henüz kargo bölgesi eklenmedi. "Yeni Bölge Ekle" butonuna tıklayın.
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t mt-6">
          <button
            @click="saveShipping"
            :disabled="saving"
            class="px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
          >
            {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Notifications Tab -->
    <div v-if="activeTab === 'notifications'" class="space-y-6">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Bildirim Tercihleri</h2>
        
        <div class="space-y-4">
          <div class="border-b pb-4">
            <h3 class="font-semibold text-gray-900 mb-3">E-posta Bildirimleri</h3>
            <div class="space-y-2">
              <label
                v-for="notification in emailNotifications"
                :key="notification.id"
                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg cursor-pointer"
              >
                <div>
                  <div class="font-medium text-gray-900">{{ notification.label }}</div>
                  <div class="text-sm text-gray-500">{{ notification.description }}</div>
                </div>
                <input
                  type="checkbox"
                  v-model="notifications.email[notification.id]"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-5 w-5"
                />
              </label>
            </div>
          </div>

          <div class="border-b pb-4">
            <h3 class="font-semibold text-gray-900 mb-3">SMS Bildirimleri</h3>
            <div class="space-y-2">
              <label
                v-for="notification in smsNotifications"
                :key="notification.id"
                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg cursor-pointer"
              >
                <div>
                  <div class="font-medium text-gray-900">{{ notification.label }}</div>
                  <div class="text-sm text-gray-500">{{ notification.description }}</div>
                </div>
                <input
                  type="checkbox"
                  v-model="notifications.sms[notification.id]"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-5 w-5"
                />
              </label>
            </div>
          </div>

          <div>
            <h3 class="font-semibold text-gray-900 mb-3">Uygulama İçi Bildirimler</h3>
            <div class="space-y-2">
              <label
                v-for="notification in pushNotifications"
                :key="notification.id"
                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg cursor-pointer"
              >
                <div>
                  <div class="font-medium text-gray-900">{{ notification.label }}</div>
                  <div class="text-sm text-gray-500">{{ notification.description }}</div>
                </div>
                <input
                  type="checkbox"
                  v-model="notifications.push[notification.id]"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-5 w-5"
                />
              </label>
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t mt-6">
          <button
            @click="saveNotifications"
            :disabled="saving"
            class="px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
          >
            {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Tax Settings Tab -->
    <div v-if="activeTab === 'tax'" class="space-y-6">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Vergi Ayarları</h2>
        <form @submit.prevent="saveTax" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şirket Türü *</label>
              <select
                v-model="tax.company_type"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Seçiniz</option>
                <option value="individual">Şahıs Şirketi</option>
                <option value="limited">Limited Şirket</option>
                <option value="joint_stock">Anonim Şirket</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Dairesi *</label>
              <input
                v-model="tax.tax_office"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ tax.company_type === 'individual' ? 'TC Kimlik No' : 'Vergi Kimlik No' }} *
              </label>
              <input
                v-model="tax.tax_number"
                type="text"
                required
                :maxlength="tax.company_type === 'individual' ? 11 : 10"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ticaret Sicil No</label>
              <input
                v-model="tax.trade_registry_number"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Şirket Unvanı *</label>
              <input
                v-model="tax.company_title"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
            <strong>⚠️ Önemli:</strong> Vergi bilgileriniz fatura kesimi ve mali raporlamalar için kullanılacaktır. Lütfen doğru ve eksiksiz bilgi girdiğinizden emin olun.
          </div>

          <div class="flex justify-end pt-4 border-t">
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- API Keys Tab -->
    <div v-if="activeTab === 'api'" class="space-y-6">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">API Anahtarları</h2>
        
        <div class="space-y-4">
          <div
            v-for="apiKey in apiKeys"
            :key="apiKey.id"
            class="border border-gray-200 rounded-lg p-4"
          >
            <div class="flex items-center justify-between mb-2">
              <div>
                <div class="font-semibold text-gray-900">{{ apiKey.name }}</div>
                <div class="text-sm text-gray-500">Oluşturulma: {{ formatDate(apiKey.created_at) }}</div>
              </div>
              <button
                @click="deleteApiKey(apiKey.id)"
                class="px-3 py-1 text-sm bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-medium"
              >
                Sil
              </button>
            </div>
            <div class="flex items-center gap-2">
              <code class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded font-mono text-sm">
                {{ showApiKey[apiKey.id] ? apiKey.key : '••••••••••••••••••••••••••••••••' }}
              </code>
              <button
                @click="toggleApiKeyVisibility(apiKey.id)"
                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm"
              >
                {{ showApiKey[apiKey.id] ? '🙈' : '👁️' }}
              </button>
              <button
                @click="copyApiKey(apiKey.key)"
                class="px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded text-sm font-medium"
              >
                📋 Kopyala
              </button>
            </div>
          </div>

          <div v-if="apiKeys.length === 0" class="text-center py-8 text-gray-500">
            Henüz API anahtarı oluşturmadınız
          </div>
        </div>

        <div class="flex justify-between items-center pt-4 border-t mt-6">
          <div class="text-sm text-gray-600">
            API anahtarları harici entegrasyonlar için kullanılır
          </div>
          <button
            @click="generateApiKey"
            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold"
          >
            + Yeni Anahtar Oluştur
          </button>
        </div>
      </div>

      <!-- API Documentation -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">API Dokümantasyonu</h2>
        <div class="prose prose-sm max-w-none">
          <p class="text-gray-600">
            API anahtarınızı kullanarak aşağıdaki endpoint'lere erişebilirsiniz:
          </p>
          <div class="bg-gray-50 rounded-lg p-4 my-4 font-mono text-sm">
            <div class="mb-2"><strong>Base URL:</strong> https://api.sportoonline.com/v1</div>
            <div class="mb-2"><strong>Authentication:</strong> Bearer Token</div>
            <div><strong>Header:</strong> Authorization: Bearer YOUR_API_KEY</div>
          </div>
          <ul class="space-y-2 text-gray-700">
            <li><code>GET /products</code> - Ürünleri listele</li>
            <li><code>POST /products</code> - Yeni ürün ekle</li>
            <li><code>GET /orders</code> - Siparişleri listele</li>
            <li><code>PUT /orders/:id/status</code> - Sipariş durumu güncelle</li>
            <li><code>GET /inventory</code> - Stok durumu</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const activeTab = ref('profile')
const saving = ref(false)
const showApiKey = ref({})

const tabs = [
  { id: 'profile', label: 'Mağaza Profili', icon: '🏪' },
  { id: 'payment', label: 'Ödeme Bilgileri', icon: '💳' },
  { id: 'shipping', label: 'Kargo Ayarları', icon: '📦' },
  { id: 'notifications', label: 'Bildirimler', icon: '🔔' },
  { id: 'tax', label: 'Vergi Bilgileri', icon: '📄' },
  { id: 'api', label: 'API Anahtarları', icon: '🔑' },
]

const profile = reactive({
  store_name: '',
  description: '',
  logo_url: '',
  cover_url: '',
  contact_email: '',
  contact_phone: '',
  address: '',
  city: '',
  postal_code: ''
})

const payment = reactive({
  bank_name: '',
  account_holder: '',
  iban: '',
  branch_code: ''
})

const shipping = reactive({
  carriers: [],
  zones: []
})

const availableCarriers = [
  { id: 'aras', name: 'Aras Kargo' },
  { id: 'yurtici', name: 'Yurtiçi Kargo' },
  { id: 'mng', name: 'MNG Kargo' },
  { id: 'ptt', name: 'PTT Kargo' },
  { id: 'surat', name: 'Sürat Kargo' },
  { id: 'ups', name: 'UPS' },
  { id: 'dhl', name: 'DHL' },
  { id: 'fedex', name: 'FedEx' }
]

const notifications = reactive({
  email: {
    new_order: true,
    order_cancelled: true,
    low_stock: true,
    review_received: false,
    daily_summary: false
  },
  sms: {
    new_order: true,
    order_cancelled: false,
    urgent_alerts: true
  },
  push: {
    new_order: true,
    order_cancelled: true,
    low_stock: true,
    customer_message: true
  }
})

const emailNotifications = [
  { id: 'new_order', label: 'Yeni Sipariş', description: 'Yeni sipariş geldiğinde e-posta gönder' },
  { id: 'order_cancelled', label: 'Sipariş İptali', description: 'Sipariş iptal edildiğinde bildir' },
  { id: 'low_stock', label: 'Düşük Stok Uyarısı', description: 'Ürün stoğu azaldığında uyar' },
  { id: 'review_received', label: 'Yeni Yorum', description: 'Ürünlerinize yorum geldiğinde bildir' },
  { id: 'daily_summary', label: 'Günlük Özet', description: 'Her gün satış özeti gönder' }
]

const smsNotifications = [
  { id: 'new_order', label: 'Yeni Sipariş', description: 'Yeni sipariş geldiğinde SMS gönder' },
  { id: 'order_cancelled', label: 'Sipariş İptali', description: 'Sipariş iptal edildiğinde SMS uyarısı' },
  { id: 'urgent_alerts', label: 'Acil Uyarılar', description: 'Kritik durumlar için SMS bildirimi' }
]

const pushNotifications = [
  { id: 'new_order', label: 'Yeni Sipariş', description: 'Anlık sipariş bildirimi' },
  { id: 'order_cancelled', label: 'Sipariş İptali', description: 'İptal edilen siparişler için bildirim' },
  { id: 'low_stock', label: 'Düşük Stok', description: 'Stok azaldığında bildirim' },
  { id: 'customer_message', label: 'Müşteri Mesajı', description: 'Müşterilerden mesaj geldiğinde bildirim' }
]

const tax = reactive({
  company_type: '',
  tax_office: '',
  tax_number: '',
  trade_registry_number: '',
  company_title: ''
})

const apiKeys = ref([])

const loadSettings = async () => {
  try {
    const { data } = await axios.get('/api/seller/settings')
    if (data.profile) Object.assign(profile, data.profile)
    if (data.payment) Object.assign(payment, data.payment)
    if (data.shipping) {
      shipping.carriers = data.shipping.carriers || []
      shipping.zones = data.shipping.zones || []
    }
    if (data.notifications) Object.assign(notifications, data.notifications)
    if (data.tax) Object.assign(tax, data.tax)
    if (data.api_keys) apiKeys.value = data.api_keys
  } catch (error) {
    console.error('Ayarlar yüklenemedi:', error)
  }
}

const saveProfile = async () => {
  saving.value = true
  try {
    await axios.put('/api/seller/settings/profile', profile)
    alert('Mağaza bilgileri kaydedildi')
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Kaydetme işlemi başarısız')
  } finally {
    saving.value = false
  }
}

const savePayment = async () => {
  saving.value = true
  try {
    await axios.put('/api/seller/settings/payment', payment)
    alert('Ödeme bilgileri kaydedildi')
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Kaydetme işlemi başarısız')
  } finally {
    saving.value = false
  }
}

const saveShipping = async () => {
  saving.value = true
  try {
    await axios.put('/api/seller/settings/shipping', shipping)
    alert('Kargo ayarları kaydedildi')
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Kaydetme işlemi başarısız')
  } finally {
    saving.value = false
  }
}

const saveNotifications = async () => {
  saving.value = true
  try {
    await axios.put('/api/seller/settings/notifications', notifications)
    alert('Bildirim tercihleri kaydedildi')
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Kaydetme işlemi başarısız')
  } finally {
    saving.value = false
  }
}

const saveTax = async () => {
  saving.value = true
  try {
    await axios.put('/api/seller/settings/tax', tax)
    alert('Vergi bilgileri kaydedildi')
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Kaydetme işlemi başarısız')
  } finally {
    saving.value = false
  }
}

const addShippingZone = () => {
  shipping.zones.push({
    name: '',
    price: 0,
    free_shipping_threshold: 0
  })
}

const removeShippingZone = (index) => {
  shipping.zones.splice(index, 1)
}

const generateApiKey = async () => {
  const name = prompt('API anahtarı için bir isim girin:')
  if (!name) return

  try {
    const { data } = await axios.post('/api/seller/settings/api-keys', { name })
    apiKeys.value.push(data.api_key)
    alert('Yeni API anahtarı oluşturuldu')
  } catch (error) {
    console.error('API key oluşturma hatası:', error)
    alert('API anahtarı oluşturulamadı')
  }
}

const deleteApiKey = async (id) => {
  if (!confirm('Bu API anahtarını silmek istediğinizden emin misiniz?')) return

  try {
    await axios.delete(`/api/seller/settings/api-keys/${id}`)
    apiKeys.value = apiKeys.value.filter(k => k.id !== id)
    alert('API anahtarı silindi')
  } catch (error) {
    console.error('Silme hatası:', error)
    alert('API anahtarı silinemedi')
  }
}

const toggleApiKeyVisibility = (id) => {
  showApiKey.value[id] = !showApiKey.value[id]
}

const copyApiKey = (key) => {
  navigator.clipboard.writeText(key)
  alert('API anahtarı kopyalandı')
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR')
}

onMounted(() => {
  loadSettings()
})
</script>
