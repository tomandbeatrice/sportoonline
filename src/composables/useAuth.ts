import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const user = ref<any>(null)
const token = ref<string | null>(localStorage.getItem('token'))

export function useAuth() {
  const router = useRouter()

  const isAuthenticated = computed(() => !!token.value)

  const login = async (credentials: { email: string; password: string }) => {
    // Use auth store for authentication
    const authStore = await import('@/stores/auth').then(m => m.useAuthStore())
    return await authStore.login(credentials)
  }

  const register = async (userData: any) => {
    // Use auth store for registration
    const authStore = await import('@/stores/auth').then(m => m.useAuthStore())
    return await authStore.register(userData)
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
