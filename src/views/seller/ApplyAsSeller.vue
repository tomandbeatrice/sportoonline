<template>
  <div class="seller-application max-w-2xl mx-auto p-8 bg-white rounded-lg shadow mt-8">
    <h1 class="text-3xl font-bold mb-6">Satıcı Başvurusu</h1>
    
    <div v-if="success" class="bg-green-100 text-green-700 p-4 rounded mb-6">
      Başvurunuz alındı! Admin onayından sonra satış yapabileceksiniz.
    </div>

    <form v-else @submit.prevent="submitApplication" class="space-y-6">
      <div>
        <label class="block font-semibold mb-2">Mağaza Adı *</label>
        <input 
          v-model="form.store_name" 
          type="text" 
          required
          class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Mağazanızın adı"
        />
      </div>

      <div>
        <label class="block font-semibold mb-2">İş Açıklaması *</label>
        <textarea 
          v-model="form.business_description" 
          required
          rows="4"
          class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Ne tür ürünler satacaksınız?"
        ></textarea>
      </div>

      <div>
        <label class="block font-semibold mb-2">Telefon *</label>
        <input 
          v-model="form.phone" 
          type="tel" 
          required
          class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="0555 123 45 67"
        />
      </div>

      <div>
        <label class="block font-semibold mb-2">Adres *</label>
        <textarea 
          v-model="form.address" 
          required
          rows="3"
          class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="İş adresiniz"
        ></textarea>
      </div>

      <div>
        <label class="block font-semibold mb-2">Vergi Numarası</label>
        <input 
          v-model="form.tax_number" 
          type="text"
          class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Vergi numaranız (opsiyonel)"
        />
      </div>

      <div>
        <label class="block font-semibold mb-2">Banka Bilgileri *</label>
        <input 
          v-model="form.bank_account" 
          type="text" 
          required
          class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="IBAN: TR..."
        />
      </div>

      <div v-if="error" class="bg-red-100 text-red-700 p-3 rounded">
        {{ error }}
      </div>

      <button 
        type="submit" 
        :disabled="loading"
        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded disabled:bg-gray-400"
      >
        {{ loading ? 'Gönderiliyor...' : 'Başvuru Gönder' }}
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const form = ref({
  store_name: '',
  business_description: '',
  phone: '',
  address: '',
  tax_number: '',
  bank_account: ''
})

const loading = ref(false)
const error = ref('')
const success = ref(false)

async function submitApplication() {
  loading.value = true
  error.value = ''

  try {
    await axios.post('/api/apply-seller', form.value)
    success.value = true
    
    setTimeout(() => {
      router.push('/')
    }, 3000)
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Başvuru gönderilemedi'
  } finally {
    loading.value = false
  }
}
</script>
