<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-5xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Adreslerim</h1>
        <p class="text-slate-500">{{ addresses.length }} kayıtlı adres</p>
      </div>

      <!-- Add Address Button -->
      <div class="mb-6">
        <button 
          @click="showAddressForm = true"
          class="px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition flex items-center gap-2"
        >
          <span>➕</span>
          <span>Yeni Adres Ekle</span>
        </button>
      </div>

      <!-- Addresses Grid -->
      <div v-if="addresses.length > 0" class="grid md:grid-cols-2 gap-4 mb-6">
        <div 
          v-for="address in addresses" 
          :key="address.id"
          class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-lg transition relative"
        >
          <!-- Default Badge -->
          <div 
            v-if="address.isDefault"
            class="absolute top-3 right-3 bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full"
          >
            ✓ Varsayılan
          </div>

          <!-- Address Type Icon -->
          <div class="mb-4">
            <div class="inline-flex w-12 h-12 bg-blue-100 rounded-xl items-center justify-center text-2xl mb-2">
              {{ getAddressIcon(address.type) }}
            </div>
            <h3 class="font-bold text-lg text-slate-900">{{ address.title }}</h3>
          </div>

          <!-- Address Details -->
          <div class="space-y-2 mb-4 text-sm text-slate-600">
            <div class="flex items-start gap-2">
              <span class="text-slate-400 mt-0.5">👤</span>
              <span>{{ address.fullName }}</span>
            </div>
            <div class="flex items-start gap-2">
              <span class="text-slate-400 mt-0.5">📍</span>
              <span>{{ address.address }}</span>
            </div>
            <div class="flex items-start gap-2">
              <span class="text-slate-400 mt-0.5">🏙️</span>
              <span>{{ address.district }} / {{ address.city }}</span>
            </div>
            <div class="flex items-start gap-2">
              <span class="text-slate-400 mt-0.5">📞</span>
              <span>{{ address.phone }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2 pt-4 border-t border-slate-100">
            <button 
              v-if="!address.isDefault"
              @click="setAsDefault(address.id)"
              class="flex-1 py-2 text-sm font-medium text-green-600 border border-green-600 rounded-lg hover:bg-green-50"
            >
              Varsayılan Yap
            </button>
            <button 
              @click="editAddress(address)"
              class="flex-1 py-2 text-sm font-medium text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50"
            >
              ✏️ Düzenle
            </button>
            <button 
              @click="deleteAddress(address.id)"
              class="px-4 py-2 text-sm font-medium text-red-600 border border-red-600 rounded-lg hover:bg-red-50"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <div class="text-5xl mb-4">📍</div>
        <h3 class="font-bold text-slate-900 mb-2">Kayıtlı Adres Yok</h3>
        <p class="text-slate-500 mb-4">Hızlı teslimat için adres ekleyin.</p>
      </div>

      <!-- Address Form Modal -->
      <div 
        v-if="showAddressForm"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="closeForm"
      >
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-900">
              {{ editingAddress ? 'Adresi Düzenle' : 'Yeni Adres Ekle' }}
            </h2>
            <button @click="closeForm" class="text-slate-400 hover:text-slate-600 text-2xl">×</button>
          </div>

          <form @submit.prevent="saveAddress" class="space-y-4">
            <!-- Title -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Adres Başlığı *</label>
              <input 
                v-model="form.title"
                type="text" 
                placeholder="Örn: Evim, İşyerim" 
                required
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>

            <!-- Type -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Adres Tipi</label>
              <div class="flex gap-3">
                <label 
                  v-for="type in addressTypes" 
                  :key="type.value"
                  class="flex-1 cursor-pointer"
                >
                  <input 
                    type="radio" 
                    v-model="form.type" 
                    :value="type.value"
                    class="peer sr-only"
                  >
                  <div class="border-2 border-slate-200 rounded-xl p-3 text-center peer-checked:border-green-600 peer-checked:bg-green-50 hover:border-slate-300 transition">
                    <div class="text-2xl mb-1">{{ type.icon }}</div>
                    <div class="text-sm font-medium">{{ type.label }}</div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Full Name -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Ad Soyad *</label>
              <input 
                v-model="form.fullName"
                type="text" 
                required
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>

            <!-- Phone -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Telefon *</label>
              <input 
                v-model="form.phone"
                type="tel" 
                placeholder="05XX XXX XX XX"
                required
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>

            <!-- City & District -->
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">İl *</label>
                <select 
                  v-model="form.city"
                  required
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">Seçiniz</option>
                  <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">İlçe *</label>
                <select 
                  v-model="form.district"
                  required
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">Seçiniz</option>
                  <option v-for="district in districts" :key="district" :value="district">{{ district }}</option>
                </select>
              </div>
            </div>

            <!-- Address -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Adres *</label>
              <textarea 
                v-model="form.address"
                rows="3"
                placeholder="Mahalle, sokak, bina no, daire no"
                required
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
              ></textarea>
            </div>

            <!-- Postal Code -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Posta Kodu</label>
              <input 
                v-model="form.postalCode"
                type="text" 
                placeholder="34XXX"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
              >
            </div>

            <!-- Default Checkbox -->
            <div>
              <label class="flex items-center gap-2 cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="form.isDefault"
                  class="w-5 h-5 text-green-600 border-slate-300 rounded focus:ring-green-500"
                >
                <span class="text-sm text-slate-700">Varsayılan adres olarak ayarla</span>
              </label>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
              <button 
                type="button"
                @click="closeForm"
                class="flex-1 py-3 border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50"
              >
                İptal
              </button>
              <button 
                type="submit"
                class="flex-1 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700"
              >
                {{ editingAddress ? 'Güncelle' : 'Kaydet' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const showAddressForm = ref(false)
const editingAddress = ref<any>(null)

const addressTypes = [
  { value: 'home', label: 'Ev', icon: '🏠' },
  { value: 'work', label: 'İş', icon: '🏢' },
  { value: 'other', label: 'Diğer', icon: '📍' }
]

const cities = ['İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya']
const districts = computed(() => {
  if (form.value.city === 'İstanbul') {
    return ['Kadıköy', 'Beşiktaş', 'Şişli', 'Üsküdar', 'Maltepe']
  }
  return []
})

const form = ref({
  title: '',
  type: 'home',
  fullName: '',
  phone: '',
  city: '',
  district: '',
  address: '',
  postalCode: '',
  isDefault: false
})

const addresses = ref([
  {
    id: 1,
    title: 'Evim',
    type: 'home',
    fullName: 'Ahmet Yılmaz',
    phone: '0532 XXX XX XX',
    city: 'İstanbul',
    district: 'Kadıköy',
    address: 'Caferağa Mah. Moda Cad. No:45 D:12',
    postalCode: '34710',
    isDefault: true
  },
  {
    id: 2,
    title: 'İş Yerim',
    type: 'work',
    fullName: 'Ahmet Yılmaz',
    phone: '0532 XXX XX XX',
    city: 'İstanbul',
    district: 'Beşiktaş',
    address: 'Levent Mah. Büyükdere Cad. No:201 Plaza K:15',
    postalCode: '34394',
    isDefault: false
  }
])

const getAddressIcon = (type: string) => {
  const icons: Record<string, string> = {
    'home': '🏠',
    'work': '🏢',
    'other': '📍'
  }
  return icons[type] || '📍'
}

const setAsDefault = (id: number) => {
  addresses.value.forEach(addr => {
    addr.isDefault = addr.id === id
  })
  toast.success('Varsayılan adres güncellendi')
}

const editAddress = (address: any) => {
  editingAddress.value = address
  form.value = { ...address }
  showAddressForm.value = true
}

const deleteAddress = (id: number) => {
  if (confirm('Bu adresi silmek istediğinize emin misiniz?')) {
    addresses.value = addresses.value.filter(a => a.id !== id)
    toast.success('Adres silindi')
  }
}

const saveAddress = () => {
  if (editingAddress.value) {
    // Update existing
    const index = addresses.value.findIndex(a => a.id === editingAddress.value.id)
    if (index !== -1) {
      addresses.value[index] = { ...form.value, id: editingAddress.value.id }
      toast.success('Adres güncellendi')
    }
  } else {
    // Add new
    const newAddress = {
      ...form.value,
      id: Date.now()
    }
    
    if (form.value.isDefault) {
      addresses.value.forEach(addr => addr.isDefault = false)
    }
    
    addresses.value.push(newAddress)
    toast.success('Yeni adres eklendi')
  }
  
  closeForm()
}

const closeForm = () => {
  showAddressForm.value = false
  editingAddress.value = null
  form.value = {
    title: '',
    type: 'home',
    fullName: '',
    phone: '',
    city: '',
    district: '',
    address: '',
    postalCode: '',
    isDefault: false
  }
}
</script>
