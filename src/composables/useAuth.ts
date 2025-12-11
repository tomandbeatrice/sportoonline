/**
 * useAuth Composable
 * 
 * This composable now delegates all authentication logic to the auth store.
 * This eliminates duplicate logic and ensures consistent auth state management.
 */
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

export function useAuth() {
  const authStore = useAuthStore()

  return {
    user: computed(() => authStore.user),
    token: computed(() => authStore.token),
    loading: computed(() => authStore.loading),
    error: computed(() => authStore.error),
    isAuthenticated: computed(() => authStore.isAuthenticated),
    userRole: computed(() => authStore.userRole),
    isAdmin: computed(() => authStore.isAdmin),
    isSeller: computed(() => authStore.isSeller),
    login: authStore.login,
    register: authStore.register,
    logout: authStore.logout,
    forgotPassword: authStore.forgotPassword,
    resetPassword: authStore.resetPassword,
    fetchUser: authStore.fetchUser
  }
}
