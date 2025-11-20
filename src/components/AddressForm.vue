<template>
  <form @submit.prevent="saveAddress" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Title -->
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Adres Başlığı <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.title"
          type="text"
          required
          placeholder="Ev, İş, vb."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- First Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Ad <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.first_name"
          type="text"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Last Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Soyad <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.last_name"
          type="text"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Phone -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Telefon <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.phone"
          type="tel"
          required
          placeholder="05XX XXX XX XX"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Email
        </label>
        <input
          v-model="form.email"
          type="email"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- City -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          İl <span class="text-red-500">*</span>
        </label>
        <select
          v-model="form.city"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">Seçiniz</option>
          <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
        </select>
      </div>

      <!-- District -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          İlçe <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.district"
          type="text"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Neighborhood -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Mahalle
        </label>
        <input
          v-model="form.neighborhood"
          type="text"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Zip Code -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Posta Kodu
        </label>
        <input
          v-model="form.zip_code"
          type="text"
          maxlength="10"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <!-- Address -->
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Adres <span class="text-red-500">*</span>
        </label>
        <textarea
          v-model="form.address"
          required
          rows="3"
          placeholder="Sokak, cadde, bina no, daire no..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
      </div>

      <!-- Address Type -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Adres Tipi <span class="text-red-500">*</span>
        </label>
        <select
          v-model="form.type"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="both">Teslimat ve Fatura</option>
          <option value="shipping">Sadece Teslimat</option>
          <option value="billing">Sadece Fatura</option>
        </select>
      </div>

      <!-- Is Default -->
      <div class="flex items-center">
        <input
          v-model="form.is_default"
          type="checkbox"
          id="is_default"
          class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
        />
        <label for="is_default" class="ml-2 text-sm text-gray-700">
          Varsayılan adres olarak kaydet
        </label>
      </div>
    </div>

    <!-- Buttons -->
    <div class="flex gap-3 pt-4 border-t">
      <button
        v-if="!hideCancel"
        type="button"
        @click="$emit('cancel')"
        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"
      >
        İptal
      </button>
      <button
        type="submit"
        :disabled="saving"
        class="flex-1 px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
      >
        {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  address: {
    type: Object,
    default: null
  },
  hideCancel: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['saved', 'cancel'])

const form = ref({
  title: '',
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  country: 'Turkey',
  city: '',
  district: '',
  neighborhood: '',
  address: '',
  zip_code: '',
  type: 'both',
  is_default: false,
})

const cities = ref([])
const saving = ref(false)

const loadCities = async () => {
  try {
    const { data } = await axios.get('/api/addresses/cities')
    cities.value = data.cities
  } catch (error) {
    console.error('Failed to load cities:', error)
  }
}

const saveAddress = async () => {
  saving.value = true
  try {
    if (props.address) {
      // Update existing address
      const { data } = await axios.put(`/api/addresses/${props.address.id}`, form.value)
      emit('saved', data.address)
    } else {
      // Create new address
      const { data } = await axios.post('/api/addresses', form.value)
      emit('saved', data.address)
    }
  } catch (error) {
    console.error('Failed to save address:', error)
    alert('Adres kaydedilemedi. Lütfen tüm alanları kontrol edin.')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadCities()
  
  // If editing, populate form
  if (props.address) {
    Object.assign(form.value, props.address)
  }
})
</script>
