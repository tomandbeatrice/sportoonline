<template>
  <div class="register max-w-md mx-auto p-8 bg-white rounded-lg shadow mt-10">
    <h2 class="text-2xl font-bold mb-6">Kayıt Ol</h2>
    <div v-if="error" class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ error }}</div>
    <div v-if="success" class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ success }}</div>
    <form @submit.prevent="register" class="space-y-4">
      <div>
        <input 
          v-model="name" 
          placeholder="Ad Soyad" 
          type="text" 
          required 
          autocomplete="name"
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500"
          :class="{ 'border-red-500': errors?.name }"
        />
        <p v-if="errors?.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
      </div>
      <div>
        <input 
          v-model="email" 
          placeholder="Email" 
          type="email" 
          required 
          autocomplete="username"
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500"
          :class="{ 'border-red-500': errors?.email }"
        />
        <p v-if="errors?.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
      </div>
      <div>
        <input 
          v-model="password" 
          placeholder="Şifre" 
          type="password" 
          required 
          autocomplete="new-password"
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500"
          :class="{ 'border-red-500': errors?.password }"
        />
        <p v-if="errors?.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
      </div>
      <div>
        <input 
          v-model="passwordConfirm" 
          placeholder="Şifre Tekrar" 
          type="password" 
          required 
          autocomplete="new-password"
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500"
          :class="{ 'border-red-500': errors?.passwordConfirm }"
        />
        <p v-if="errors?.passwordConfirm" class="text-red-500 text-sm mt-1">{{ errors.passwordConfirm }}</p>
      </div>
      <div>
        <label class="flex items-center gap-2 text-sm">
          <input 
            v-model="acceptTerms" 
            type="checkbox" 
            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
            :class="{ 'border-red-500': errors?.acceptTerms }"
          />
          <span>
            <a href="/terms" target="_blank" class="text-blue-600 hover:text-blue-700">Kullanım Koşulları</a>'nı ve 
            <a href="/privacy" target="_blank" class="text-blue-600 hover:text-blue-700">Gizlilik Politikası</a>'nı kabul ediyorum
          </span>
        </label>
        <p v-if="errors?.acceptTerms" class="text-red-500 text-sm mt-1">{{ errors.acceptTerms }}</p>
      </div>
      <button 
        type="submit" 
        :disabled="loading"
        class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:bg-gray-400 transition-colors"
      >
        <span v-if="loading" class="flex items-center justify-center gap-2">
          <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Kayıt yapılıyor...
        </span>
        <span v-else>Kayıt Ol</span>
      </button>
    </form>
    <div class="mt-4 text-center text-sm text-slate-600">
      Zaten hesabınız var mı? 
      <router-link to="/login" class="text-blue-600 hover:text-blue-700 font-medium">Giriş Yap</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import mockAuth from '@/services/mockAuth.js'

const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirm = ref('')
const acceptTerms = ref(false)
const error = ref('')
const success = ref('')
const loading = ref(false)
const errors = ref<Record<string, string>>({})

// Auto-detect mock auth
mockAuth.autoDetectMockAuth()

function validateForm() {
  errors.value = {}
  let isValid = true

  // Name validation
  if (!name.value || name.value.trim().length === 0) {
    errors.value.name = 'Ad Soyad zorunludur'
    isValid = false
  } else if (name.value.length < 3) {
    errors.value.name = 'Ad Soyad en az 3 karakter olmalıdır'
    isValid = false
  }

  // Email validation
  if (!email.value || email.value.trim().length === 0) {
    errors.value.email = 'Email adresi zorunludur'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.value.email = 'Geçerli bir email adresi girin'
    isValid = false
  }

  // Password validation
  if (!password.value || password.value.length === 0) {
    errors.value.password = 'Şifre zorunludur'
    isValid = false
  } else if (password.value.length < 8) {
    errors.value.password = 'Şifre en az 8 karakter olmalıdır'
    isValid = false
  }

  // Password confirmation validation
  if (!passwordConfirm.value) {
    errors.value.passwordConfirm = 'Şifre tekrarı zorunludur'
    isValid = false
  } else if (password.value !== passwordConfirm.value) {
    errors.value.passwordConfirm = 'Şifreler eşleşmiyor'
    isValid = false
  }

  // Terms acceptance validation
  if (!acceptTerms.value) {
    errors.value.acceptTerms = 'Kullanım koşullarını kabul etmelisiniz'
    isValid = false
  }

  return isValid
}

async function register() {
  error.value = ''
  success.value = ''
  
  if (!validateForm()) {
    return
  }
  
  loading.value = true
  
  try {
    let response

    // Use mock auth if enabled or if real API fails
    if (mockAuth.isMockAuthEnabled()) {
      console.log('🎭 Using Mock Auth for registration')
      const data = await mockAuth.mockRegister(name.value, email.value, password.value)
      response = { data }
    } else {
      try {
        response = await axios.post('/api/register', {
          name: name.value,
          email: email.value,
          password: password.value,
          password_confirmation: passwordConfirm.value
        })
      } catch (apiError: any) {
        if (apiError.code === 'ERR_NETWORK' || apiError.message === 'Network Error') {
          console.warn('⚠️ Backend not available, falling back to mock auth')
          mockAuth.enableMockAuth()
          const data = await mockAuth.mockRegister(name.value, email.value, password.value)
          response = { data }
        } else {
          throw apiError
        }
      }
    }
    
    success.value = 'Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...'
    
    // 2 saniye bekleyip login sayfasına yönlendir
    setTimeout(() => {
      router.push('/login')
    }, 2000)
  } catch (e: any) {
    console.error('❌ Register error:', e)
    if (e.response?.data?.errors) {
      // Laravel validation errors
      const validationErrors = e.response.data.errors
      error.value = Object.values(validationErrors).flat().join(', ')
    } else {
      error.value = e.message || e.response?.data?.message || 'Kayıt başarısız. Lütfen bilgilerinizi kontrol edin.'
    }
  } finally {
    loading.value = false
  }
}
</script>