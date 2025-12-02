import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const user = ref<any>(null)
const token = ref<string | null>(localStorage.getItem('token'))

export function useAuth() {
  const router = useRouter()

  const isAuthenticated = computed(() => !!token.value)

  const login = async (credentials: { email: string; password: string }) => {
    // TODO: Implement actual API call
    console.log('Login attempt:', credentials)
    token.value = 'mock-token'
    localStorage.setItem('token', token.value)
    user.value = { name: 'Mock User', email: credentials.email }
  }

  const register = async (userData: any) => {
    // TODO: Implement actual API call
    console.log('Register attempt:', userData)
    token.value = 'mock-token'
    localStorage.setItem('token', token.value)
    user.value = { name: userData.name, email: userData.email }
  }

  const logout = () => {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    router.push('/login')
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout
  }
}
