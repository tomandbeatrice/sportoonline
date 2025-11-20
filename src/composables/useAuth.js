import { ref } from 'vue';
import api from '@/services/api';

const user = ref(null);
const isAuthenticated = ref(false);

export function useAuth() {
  const login = async (email, password) => {
    const res = await api.post('/login', { email, password });
    localStorage.setItem('token', res.data.token);
    user.value = res.data.user;
    isAuthenticated.value = true;
    return res.data;
  };

  const register = async (name, email, password) => {
    const res = await api.post('/register', { name, email, password });
    localStorage.setItem('token', res.data.token);
    user.value = res.data.user;
    isAuthenticated.value = true;
    return res.data;
  };

  const logout = () => {
    localStorage.removeItem('token');
    user.value = null;
    isAuthenticated.value = false;
  };

  const checkAuth = async () => {
    try {
      const res = await api.get('/user');
      user.value = res.data;
      isAuthenticated.value = true;
    } catch {
      isAuthenticated.value = false;
    }
  };

  const hasRole = (role) => {
    return user.value?.role === role;
  };

  return {
    user,
    isAuthenticated,
    login,
    register,
    logout,
    checkAuth,
    hasRole,
  };
}
