import { createApp } from 'vue'
import App from './App.vue'
import router from './router/index.js'
import { createPinia } from 'pinia'
import { createHead } from '@vueuse/head'
import Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import './assets/tailwind.css'
import './assets/theme.css'
import axios from 'axios'
import { registerServiceWorker, setupInstallPrompt } from './utils/pwa.js'
import { analytics } from './services/analytics.js'
import { errorTracking } from './services/errorTracking.js'
import { performanceMonitoring } from './services/performanceMonitoring.js'
import mockAuth from './services/mockAuth.js'

// Initialize Mock Auth auto-detection
mockAuth.autoDetectMockAuth()

// Axios global configuration
axios.defaults.baseURL = 'http://127.0.0.1:8000'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// Token varsa header'a ekle
const token = localStorage.getItem('token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// Initialize Analytics (Production only)
if (import.meta.env.PROD) {
  analytics.init({
    measurementId: import.meta.env.VITE_GA_MEASUREMENT_ID || 'G-XXXXXXXXXX',
    debug: false
  })
}

// Initialize Error Tracking
errorTracking.init()

// Initialize Performance Monitoring
performanceMonitoring.init()

// Axios interceptors for error tracking
axios.interceptors.response.use(
  response => response,
  error => {
    errorTracking.captureAPIError(
      error,
      error.config?.url || 'unknown',
      error.config?.method?.toUpperCase() || 'GET'
    )
    return Promise.reject(error)
  }
)

const app = createApp(App)
const pinia = createPinia()
const head = createHead()

// Store Vue app globally for error tracking
window.__VUE_APP__ = app

// Expose services globally for console testing (dev only)
if (import.meta.env.DEV) {
  window.__ANALYTICS__ = analytics
  window.__ERROR_TRACKING__ = errorTracking
  window.__PERFORMANCE__ = performanceMonitoring
}

app.use(pinia)
app.use(head)
app.use(router)
app.use(Toastify, {
  autoClose: 3000,
  position: 'top-right'
})

// Track page views
router.afterEach((to) => {
  analytics.pageView({
    page_title: to.meta.title as string || document.title,
    page_path: to.path
  })
})

app.mount('#app')

// Register Service Worker for PWA
if (import.meta.env.PROD) {
  registerServiceWorker()
  setupInstallPrompt()
}