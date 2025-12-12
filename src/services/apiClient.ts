import axios, { type AxiosInstance, type AxiosResponse } from 'axios'

const apiClient: AxiosInstance = axios.create({
  baseURL: '/api', // Vite proxy handles the target
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  timeout: 10000
})

// Request Interceptor
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response Interceptor
apiClient.interceptors.response.use(
  (response: AxiosResponse) => response,
  (error) => {
    // Handle common errors
    if (error.response) {
      switch (error.response.status) {
        case 401:
          // Unauthorized - clear token and redirect if needed
          // Avoid redirect loop if already on login
          if (!globalThis.location.pathname.includes('/login')) {
             localStorage.removeItem('token')
             // Optional: window.location.href = '/login'
          }
          break
        case 403:
          // Forbidden
          console.error('Access Forbidden')
          break
        case 404:
          // Not Found
          console.error('Resource Not Found')
          break
        case 500:
          // Server Error
          console.error('Internal Server Error')
          break
      }
    }
    return Promise.reject(error)
  }
)

export default apiClient
