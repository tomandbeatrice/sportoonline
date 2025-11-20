<template>
  <div class="buyer-profile">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Profilim & Ayarlar</h2>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
      <nav class="flex space-x-8">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
          :class="activeTab === tab.id
            ? 'border-blue-500 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        >
          <BadgeIcon v-if="tab.iconName" :name="tab.iconName" cls="w-5 h-5" />
          <span v-else>{{ tab.icon }}</span>
          {{ tab.label }}
        </button>
      </nav>
    </div>

    <!-- Personal Info Tab -->
    <div v-if="activeTab === 'personal'" class="bg-white rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-4">Kişisel Bilgiler</h3>
      <form @submit.prevent="savePersonalInfo" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ad *</label>
            <input
              v-model="personalInfo.first_name"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Soyad *</label>
            <input
              v-model="personalInfo.last_name"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-posta *</label>
            <input
              v-model="personalInfo.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
            <input
              v-model="personalInfo.phone"
              type="tel"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="0555 123 45 67"
            />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Doğum Tarihi</label>
            <input
              v-model="personalInfo.birth_date"
              type="date"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>
        <div class="flex justify-end pt-4 border-t">
          <button
            type="submit"
            :disabled="saving"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-lg font-semibold"
          >
            {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Addresses Tab -->
    <div v-if="activeTab === 'addresses'">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-900">Adreslerim</h3>
        <button
          @click="showAddressModal = true"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm"
        >
          + Yeni Adres Ekle
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="address in addresses"
          :key="address.id"
          class="bg-white rounded-xl shadow-sm p-6 border-2"
          :class="address.is_default ? 'border-blue-500' : 'border-gray-200'"
        >
          <div class="flex justify-between items-start mb-3">
            <div>
              <div class="font-bold text-gray-900">{{ address.title }}</div>
              <span v-if="address.is_default" class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full mt-1">
                Varsayılan Adres
              </span>
            </div>
            <button @click="deleteAddress(address.id)" class="text-red-600 hover:text-red-800">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
          <div class="text-sm text-gray-700 mb-4">
            <div>{{ address.full_address }}</div>
            <div>{{ address.district }} / {{ address.city }}</div>
            <div>{{ address.postal_code }}</div>
          </div>
          <div class="flex gap-2">
            <button
              @click="editAddress(address)"
              class="flex-1 px-3 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium"
            >
              Düzenle
            </button>
            <button
              v-if="!address.is_default"
              @click="setDefaultAddress(address.id)"
              class="flex-1 px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg text-sm font-medium"
            >
              Varsayılan Yap
            </button>
          </div>
        </div>
      </div>

      <div v-if="addresses.length === 0" class="bg-white rounded-xl shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">📍</div>
        <div class="text-xl font-semibold text-gray-900 mb-2">Kayıtlı Adresiniz Yok</div>
        <div class="text-gray-500 mb-6">Alışverişlerinizi hızlandırmak için adres ekleyin</div>
      </div>
    </div>

    <!-- Password Tab -->
    <div v-if="activeTab === 'password'" class="bg-white rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-4">Şifre Değiştir</h3>
      <form @submit.prevent="changePassword" class="space-y-4 max-w-md">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mevcut Şifre *</label>
          <input
            v-model="passwordForm.current_password"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre *</label>
          <input
            v-model="passwordForm.new_password"
            type="password"
            required
            minlength="8"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          />
          <p class="text-xs text-gray-500 mt-1">En az 8 karakter olmalıdır</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre (Tekrar) *</label>
          <input
            v-model="passwordForm.new_password_confirmation"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="flex justify-end pt-4 border-t">
          <button
            type="submit"
            :disabled="saving"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-lg font-semibold"
          >
            {{ saving ? 'Kaydediliyor...' : 'Şifreyi Değiştir' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Notifications Tab -->
    <div v-if="activeTab === 'notifications'" class="bg-white rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-4">Bildirim Tercihleri</h3>
      <div class="space-y-4">
        <label class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer">
          <div>
            <div class="font-medium text-gray-900">E-posta Bildirimleri</div>
            <div class="text-sm text-gray-500">Sipariş güncellemeleri ve kampanyalar</div>
          </div>
          <input
            type="checkbox"
            v-model="notifications.email_enabled"
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-5 w-5"
          />
        </label>
        <label class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer">
          <div>
            <div class="font-medium text-gray-900">SMS Bildirimleri</div>
            <div class="text-sm text-gray-500">Kargo ve teslimat bildirimleri</div>
          </div>
          <input
            type="checkbox"
            v-model="notifications.sms_enabled"
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-5 w-5"
          />
        </label>
        <label class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg cursor-pointer">
          <div>
            <div class="font-medium text-gray-900">Kampanya Bildirimleri</div>
            <div class="text-sm text-gray-500">Özel indirim ve fırsatlar</div>
          </div>
          <input
            type="checkbox"
            v-model="notifications.campaigns_enabled"
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-5 w-5"
          />
        </label>
      </div>
      <div class="flex justify-end pt-4 border-t mt-6">
        <button
          @click="saveNotifications"
          :disabled="saving"
          class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-lg font-semibold"
        >
          {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
        </button>
      </div>
    </div>

    <!-- Address Modal -->
    <div v-if="showAddressModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-bold text-gray-900">{{ editingAddress ? 'Adresi Düzenle' : 'Yeni Adres Ekle' }}</h3>
          <button @click="closeAddressModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveAddress" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adres Başlığı *</label>
            <input
              v-model="addressForm.title"
              type="text"
              required
              placeholder="Örn: Ev, İş, Yazlık"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">İl *</label>
              <input
                v-model="addressForm.city"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">İlçe *</label>
              <input
                v-model="addressForm.district"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adres *</label>
            <textarea
              v-model="addressForm.full_address"
              rows="3"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Posta Kodu</label>
            <input
              v-model="addressForm.postal_code"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div class="flex items-center">
            <input
              v-model="addressForm.is_default"
              type="checkbox"
              id="is_default"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4"
            />
            <label for="is_default" class="ml-2 text-sm text-gray-700">Varsayılan adres olarak ayarla</label>
          </div>
          <div class="flex gap-3 pt-4 border-t">
            <button
              type="button"
              @click="closeAddressModal"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-lg font-semibold"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const activeTab = ref('personal')
const saving = ref(false)
const showAddressModal = ref(false)
const editingAddress = ref(null)

const tabs = [
  { id: 'personal', label: 'Kişisel Bilgiler', icon: '👤' },
  { id: 'addresses', label: 'Adreslerim', icon: '📍' },
  { id: 'password', label: 'Şifre', icon: '🔒' },
  { id: 'notifications', label: 'Bildirimler', icon: '🔔', iconName: 'bell' },
]

const personalInfo = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  birth_date: ''
})

const addresses = ref([])

const addressForm = reactive({
  title: '',
  city: '',
  district: '',
  full_address: '',
  postal_code: '',
  is_default: false
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const notifications = reactive({
  email_enabled: true,
  sms_enabled: true,
  campaigns_enabled: false
})

const loadProfile = async () => {
  try {
    const { data } = await axios.get('/api/user/profile')
    Object.assign(personalInfo, data)
  } catch (error) {
    console.error('Profil yüklenemedi:', error)
  }
}

const loadAddresses = async () => {
  try {
    const { data } = await axios.get('/api/user/addresses')
    addresses.value = data.addresses || data
  } catch (error) {
    console.error('Adresler yüklenemedi:', error)
  }
}

const savePersonalInfo = async () => {
  saving.value = true
  try {
    await axios.put('/api/user/profile', personalInfo)
    alert('Bilgileriniz güncellendi')
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Kaydetme işlemi başarısız')
  } finally {
    saving.value = false
  }
}

const saveAddress = async () => {
  saving.value = true
  try {
    if (editingAddress.value) {
      await axios.put(`/api/user/addresses/${editingAddress.value.id}`, addressForm)
    } else {
      await axios.post('/api/user/addresses', addressForm)
    }
    await loadAddresses()
    closeAddressModal()
  } catch (error) {
    console.error('Adres kaydetme hatası:', error)
    alert('Adres kaydetme işlemi başarısız')
  } finally {
    saving.value = false
  }
}

const editAddress = (address) => {
  editingAddress.value = address
  Object.assign(addressForm, address)
  showAddressModal.value = true
}

const deleteAddress = async (id) => {
  if (!confirm('Bu adresi silmek istediğinize emin misiniz?')) return

  try {
    await axios.delete(`/api/user/addresses/${id}`)
    addresses.value = addresses.value.filter(a => a.id !== id)
  } catch (error) {
    console.error('Adres silme hatası:', error)
    alert('Silme işlemi başarısız')
  }
}

const setDefaultAddress = async (id) => {
  try {
    await axios.put(`/api/user/addresses/${id}/set-default`)
    await loadAddresses()
  } catch (error) {
    console.error('Varsayılan adres ayarlama hatası:', error)
    alert('İşlem başarısız')
  }
}

const closeAddressModal = () => {
  showAddressModal.value = false
  editingAddress.value = null
  Object.assign(addressForm, {
    title: '',
    city: '',
    district: '',
    full_address: '',
    postal_code: '',
    is_default: false
  })
}

const changePassword = async () => {
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    alert('Yeni şifreler eşleşmiyor')
    return
  }

  saving.value = true
  try {
    await axios.put('/api/user/change-password', passwordForm)
    alert('Şifreniz başarıyla değiştirildi')
    Object.assign(passwordForm, {
      current_password: '',
      new_password: '',
      new_password_confirmation: ''
    })
  } catch (error) {
    console.error('Şifre değiştirme hatası:', error)
    alert(error.response?.data?.message || 'Şifre değiştirme işlemi başarısız')
  } finally {
    saving.value = false
  }
}

const saveNotifications = async () => {
  saving.value = true
  try {
    await axios.put('/api/user/notification-preferences', notifications)
    alert('Bildirim tercihleri kaydedildi')
  } catch (error) {
    console.error('Kaydetme hatası:', error)
    alert('Kaydetme işlemi başarısız')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadProfile()
  loadAddresses()
})
</script>
