import { useAuthStore } from "@/stores/auth";

export function useAuth() {
  const authStore = useAuthStore();

  return {
    user: authStore.user,
    token: authStore.token,
    isAuthenticated: authStore.isAuthenticated,
    userRole: authStore.userRole,
    isAdmin: authStore.isAdmin,
    isSeller: authStore.isSeller,
    login: authStore.login,
    register: authStore.register,
    logout: authStore.logout,
    forgotPassword: authStore.forgotPassword,
    resetPassword: authStore.resetPassword,
    fetchUser: authStore.fetchUser,
    loading: authStore.loading,
    error: authStore.error,
  };
}
