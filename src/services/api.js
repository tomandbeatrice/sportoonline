import axios from 'axios';
import logger from '@/utils/logger';

// @deprecated Use apiClient.ts instead for TypeScript support
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_PATH || '/api',
});

// Request interceptor - her istekte token ekle
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    logger.api('GET', config.url || '');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - hata yönetimi
api.interceptors.response.use(
  (response) => response,
  (error) => {
    logger.error('API Error:', error.response?.status, error.response?.data)
    if (error.response?.status === 401) {
      logger.warn('401 Unauthorized - Logging out')
      localStorage.removeItem('token');
      localStorage.removeItem('role');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;
