<template>
  <div class="login max-w-md mx-auto p-8 bg-white rounded-lg shadow mt-10">
    <h2 class="text-2xl font-bold mb-6">Giriş Yap</h2>
    
    <!-- Mock Auth Info -->
    <div v-if="mockAuth.isMockAuthEnabled()" class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-lg mb-4 text-sm">
      <div class="flex items-start gap-2">
        <span class="text-lg">🎭</span>
        <div>
          <p class="font-semibold mb-2">Demo Mod Aktif</p>
          <p class="text-xs text-blue-700 mb-2">Backend bağlantısı yok, test hesapları kullanılıyor:</p>
          <div class="space-y-1 text-xs font-mono bg-blue-100 p-2 rounded">
            <p><strong>Admin:</strong> admin@sportoonline.com / admin123</p>
            <p><strong>Satıcı:</strong> seller@sportoonline.com / seller123</p>
            <p><strong>Alıcı:</strong> buyer@sportoonline.com / buyer123</p>
          </div>
        </div>
      </div>
    </div>
    
    <div v-if="error" class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ error }}</div>
    <form @submit.prevent="login" class="space-y-4">
      <div>
        <input 
          v-model="email" 
          placeholder="Email" 
          type="email" 
          required 
          autocomplete="username"
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
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
          autocomplete="current-password"
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          :class="{ 'border-red-500': errors?.password }"
        />
        <p v-if="errors?.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
      </div>
      <button 
        type="submit" 
        :disabled="loading"
        class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400 transition-colors"
      >
        <span v-if="loading" class="flex items-center justify-center gap-2">
          <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Giriş yapılıyor...
        </span>
        <span v-else>Giriş</span>
      </button>
    </form>
    <div class="mt-4 text-center text-sm text-slate-600">
      Hesabınız yok mu? 
      <router-link to="/register" class="text-blue-600 hover:text-blue-700 font-medium">Kayıt Ol</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import mockAuth from '@/services/mockAuth.js'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const errors = ref<Record<string, string>>({})

// Auto-detect mock auth on component mount
mockAuth.autoDetectMockAuth()

function validateForm() {
  errors.value = {}
  let isValid = true

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
  } else if (password.value.length < 6) {
    errors.value.password = 'Şifre en az 6 karakter olmalıdır'
    isValid = false
  }

  return isValid
}

async function login() {
  error.value = ''
  
  if (!validateForm()) {
    return
  }
  
  loading.value = true
  
  try {
    let response

    // Use mock auth if enabled or if real API fails
    if (mockAuth.isMockAuthEnabled()) {
      console.log('🎭 Using Mock Auth')
      const data = await mockAuth.mockLogin(email.value, password.value)
      response = { data }
    } else {
      try {
        response = await axios.post('/api/login', {
          email: email.value,
          password: password.value
        })
      } catch (apiError: any) {
        if (apiError.code === 'ERR_NETWORK' || apiError.message === 'Network Error') {
          console.warn('⚠️ Backend not available, falling back to mock auth')
          mockAuth.enableMockAuth()
          const data = await mockAuth.mockLogin(email.value, password.value)
          response = { data }
        } else {
          throw apiError
        }
      }
    }
    
    console.log('✅ Login Success:', response.data)
    
    // Token ve user bilgisini auth store'a kaydet
    authStore.token = response.data.token
    authStore.user = response.data.user
    
    // Token ve user bilgisini localStorage'a kaydet
    localStorage.setItem('token', response.data.token)
    localStorage.setItem('role', response.data.user.role)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    
    console.log('💾 Saved to localStorage:', {
      token: localStorage.getItem('token'),
      role: localStorage.getItem('role'),
      user: localStorage.getItem('user')
    })
    
    // Axios default header'a token ekle
    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
    
    // Role göre yönlendir
    if (response.data.user.role === 'admin') {
      console.log('🔄 Redirecting to /admin/dashboard')
      router.push('/admin/dashboard')
    } else if (response.data.user.role === 'seller') {
      console.log('🔄 Redirecting to /seller/dashboard')
      router.push('/seller/dashboard')
    } else if (response.data.user.role === 'buyer') {
      console.log('🔄 Redirecting to /buyer/dashboard')
      router.push('/buyer/dashboard')
    } else {
      console.log('🔄 Redirecting to /')
      router.push('/')
    }
  } catch (e: any) {
    console.error('❌ Login error:', e)
    error.value = e.message || e.response?.data?.message || 'Giriş başarısız. Lütfen bilgilerinizi kontrol edin.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login {
  font-family: 'Inter', sans-serif;
}
</style>