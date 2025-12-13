import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import * as authService from '@/services/authService'
import type { User, LoginCredentials, RegisterData } from '@/types/auth'

const user = ref<User | null>(null)
const token = ref<string | null>(localStorage.getItem('token'))
const loading = ref<boolean>(false)
const error = ref<string | null>(null)

export function useAuth() {
  const router = useRouter()

  const isAuthenticated = computed(() => !!token.value)

  const login = async (credentials: LoginCredentials) => {
    try {
      loading.value = true
      error.value = null
      
      const response = await authService.login(credentials)
      
      token.value = response.token
      user.value = response.user
      authService.setAuthToken(response.token)
      
      return response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Login failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  const register = async (userData: RegisterData) => {
    try {
      loading.value = true
      error.value = null
      
      const response = await authService.register(userData)
      
      token.value = response.token
      user.value = response.user
      authService.setAuthToken(response.token)
      
      return response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Registration failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      loading.value = true
      error.value = null
      
      if (token.value) {
        await authService.logout()
      }
    } finally {
      token.value = null
      user.value = null
      authService.removeAuthToken()
      loading.value = false
      router.push('/login')
    }
  }

  const fetchCurrentUser = async () => {
    try {
      loading.value = true
      error.value = null
      
      user.value = await authService.getCurrentUser()
      return user.value
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch user'
      // If unauthorized, clear auth state
      if (err.response?.status === 401) {
        token.value = null
        user.value = null
        authService.removeAuthToken()
      }
      throw err
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
    login,
    register,
    logout,
    fetchCurrentUser
  }
}
