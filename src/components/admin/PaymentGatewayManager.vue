<template>
  <div class="payment-gateway-manager">
    <div class="header mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Ödeme Sistemleri Yönetimi</h1>
      <p class="text-gray-600 mt-2">Ödeme gateway'lerini yapılandırın, aktif/pasif yapın ve test modunu yönetin</p>
    </div>

    <!-- Available Gateways -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div
        v-for="gateway in gateways"
        :key="gateway.id"
        class="bg-white rounded-lg shadow-md p-6 border-l-4"
        :class="{
          'border-green-500': gateway.is_active,
          'border-gray-300': !gateway.is_active
        }"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-bold text-gray-900">{{ gateway.display_name }}</h3>
            <p class="text-sm text-gray-600 mt-1">{{ gateway.description }}</p>
          </div>
          <span
            class="px-3 py-1 rounded-full text-xs font-semibold"
            :class="{
              'bg-green-100 text-green-800': gateway.is_active,
              'bg-gray-100 text-gray-800': !gateway.is_active
            }"
          >
            {{ gateway.is_active ? 'Aktif' : 'Pasif' }}
          </span>
        </div>

        <!-- Gateway Stats -->
        <div v-if="gateway.stats" class="grid grid-cols-2 gap-3 mb-4 text-sm">
          <div class="bg-blue-50 p-3 rounded">
            <div class="text-blue-600 font-semibold">{{ gateway.stats.total_transactions || 0 }}</div>
            <div class="text-gray-600 text-xs">Toplam İşlem</div>
          </div>
          <div class="bg-green-50 p-3 rounded">
            <div class="text-green-600 font-semibold">₺{{ formatMoney(gateway.stats.total_amount || 0) }}</div>
            <div class="text-gray-600 text-xs">Toplam Tutar</div>
          </div>
        </div>

        <!-- Configuration Status -->
        <div class="mb-4">
          <div class="flex items-center text-sm mb-2">
            <span class="text-gray-600">Yapılandırma:</span>
            <span
              class="ml-2 px-2 py-1 rounded text-xs font-semibold"
              :class="{
                'bg-green-100 text-green-800': gateway.has_credentials,
                'bg-yellow-100 text-yellow-800': !gateway.has_credentials
              }"
            >
              {{ gateway.has_credentials ? 'Tamamlandı' : 'Eksik' }}
            </span>
          </div>
          <div v-if="gateway.is_test_mode" class="flex items-center text-sm">
            <svg class="w-4 h-4 text-orange-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span class="text-orange-600 font-semibold">Test Modu</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
          <button
            @click="toggleActive(gateway)"
            class="flex-1 px-4 py-2 rounded text-sm font-semibold transition-colors"
            :class="{
              'bg-red-500 hover:bg-red-600 text-white': gateway.is_active,
              'bg-green-500 hover:bg-green-600 text-white': !gateway.is_active
            }"
          >
            {{ gateway.is_active ? 'Pasif Yap' : 'Aktif Yap' }}
          </button>
          <button
            @click="openConfigModal(gateway)"
            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-semibold transition-colors"
          >
            Ayarla
          </button>
        </div>
      </div>
    </div>

    <!-- Configuration Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold text-gray-900">{{ selectedGateway?.display_name }} Ayarları</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveConfiguration" class="space-y-4">
          <!-- Test Mode Toggle -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <label class="font-semibold text-gray-900">Test Modu</label>
              <p class="text-sm text-gray-600">Test ortamında çalışsın mı?</p>
            </div>
            <button
              type="button"
              @click="formData.is_test_mode = !formData.is_test_mode"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
              :class="formData.is_test_mode ? 'bg-blue-600' : 'bg-gray-300'"
            >
              <span
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                :class="formData.is_test_mode ? 'translate-x-6' : 'translate-x-1'"
              />
            </button>
          </div>

          <!-- Credentials -->
          <div class="space-y-3">
            <h3 class="font-semibold text-gray-900">API Bilgileri</h3>
            <div v-for="(value, key) in formData.credentials" :key="key">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ formatCredentialLabel(key) }}
              </label>
              <input
                v-model="formData.credentials[key]"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                :placeholder="formatCredentialLabel(key)"
              />
            </div>
          </div>

          <!-- Commission Settings -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Komisyon Oranı (%)
              </label>
              <input
                v-model.number="formData.commission_rate"
                type="number"
                step="0.01"
                min="0"
                max="100"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Sabit Komisyon (₺)
              </label>
              <input
                v-model.number="formData.fixed_commission"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <!-- Amount Limits -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Min. Tutar (₺)
              </label>
              <input
                v-model.number="formData.min_amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Max. Tutar (₺)
              </label>
              <input
                v-model.number="formData.max_amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button
              type="button"
              @click="closeModal"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"
            >
              İptal
            </button>
            <button
              type="submit"
              class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold"
              :disabled="loading"
            >
              {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const gateways = ref([])
const showModal = ref(false)
const selectedGateway = ref(null)
const loading = ref(false)
const formData = ref({
  is_test_mode: true,
  credentials: {},
  commission_rate: 0,
  fixed_commission: 0,
  min_amount: 1,
  max_amount: null,
})

const loadGateways = async () => {
  try {
    const response = await axios.get('/api/admin/payment-gateways')
    gateways.value = response.data.gateways
  } catch (error) {
    console.error('Failed to load gateways:', error)
  }
}

const toggleActive = async (gateway) => {
  try {
    await axios.post(`/api/admin/payment-gateways/${gateway.id}/toggle-active`)
    gateway.is_active = !gateway.is_active
  } catch (error) {
    console.error('Failed to toggle gateway:', error)
    alert('Gateway durumu değiştirilemedi')
  }
}

const openConfigModal = (gateway) => {
  selectedGateway.value = gateway
  formData.value = {
    is_test_mode: gateway.is_test_mode,
    credentials: { ...gateway.credentials } || {},
    commission_rate: gateway.commission_rate,
    fixed_commission: gateway.fixed_commission,
    min_amount: gateway.min_amount,
    max_amount: gateway.max_amount,
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedGateway.value = null
}

const saveConfiguration = async () => {
  loading.value = true
  try {
    await axios.put(`/api/admin/payment-gateways/${selectedGateway.value.id}`, formData.value)
    await loadGateways()
    closeModal()
    alert('Ayarlar başarıyla kaydedildi')
  } catch (error) {
    console.error('Failed to save configuration:', error)
    alert('Ayarlar kaydedilemedi')
  } finally {
    loading.value = false
  }
}

const formatCredentialLabel = (key) => {
  return key
    .replace(/_/g, ' ')
    .replace(/\b\w/g, (l) => l.toUpperCase())
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

onMounted(() => {
  loadGateways()
})
</script>

<style scoped>
.payment-gateway-manager {
  padding: 2rem;
}
</style>
