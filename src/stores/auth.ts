import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import logger from '@/utils/logger'

// Type definitions
export interface User {
  id: number
  name: string
  email: string
  role: 'admin' | 'seller' | 'buyer'
  avatar?: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  role?: 'buyer' | 'seller'
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
  const loading = ref(false)
  const error = ref<string | null>(null)
  const router = useRouter()

  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed(() => user.value?.role || null)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isSeller = computed(() => user.value?.role === 'seller')

  async function login(credentials: LoginCredentials): Promise<boolean> {
    loading.value = true
    error.value = null
    try {
      const response = await axios.post('/api/v1/login', credentials)
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value!)
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      return true
    } catch (err) {
      logger.error('Login failed', err)
      error.value = 'Giriş başarısız. Lütfen bilgilerinizi kontrol edin.'
      return false
    } finally {
      loading.value = false
    }
  }

  async function register(data: RegisterData): Promise<boolean> {
    loading.value = true
    error.value = null
    try {
      const response = await axios.post('/api/v1/register', data)
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value!)
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      return true
    } catch (err: any) {
      logger.error('Register failed', err)
      error.value = err.response?.data?.message || 'Kayıt başarısız. Lütfen bilgilerinizi kontrol edin.'
      return false
    } finally {
      loading.value = false
    }
  }

  async function forgotPassword(email: string): Promise<boolean> {
    loading.value = true
    error.value = null
    try {
      await axios.post('/api/v1/forgot-password', { email })
      return true
    } catch (err: any) {
      logger.error('Forgot password failed', err)
      error.value = err.response?.data?.message || 'Şifre sıfırlama başarısız.'
      return false
    } finally {
      loading.value = false
    }
  }

  async function resetPassword(token: string, password: string, passwordConfirmation: string): Promise<boolean> {
    loading.value = true
    error.value = null
    try {
      await axios.post('/api/v1/reset-password', {
        token,
        password,
        password_confirmation: passwordConfirmation
      })
      return true
    } catch (err: any) {
      logger.error('Reset password failed', err)
      error.value = err.response?.data?.message || 'Şifre sıfırlama başarısız.'
      return false
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await axios.post('/api/v1/logout')
    } catch (e) {
      // ignore
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
      router.push('/login')
    }
  }

  async function fetchUser() {
    if (!token.value) return
    loading.value = true
    try {
      const response = await axios.get('/api/v1/me')
      user.value = response.data
    } catch (err) {
      logger.error('Failed to fetch user', err)
      logout()
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    userRole,
    isAdmin,
    isSeller,
    login,
    register,
    forgotPassword,
    resetPassword,
    logout,
    fetchUser
  }
})
