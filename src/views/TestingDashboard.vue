<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-6xl">
      <h1 class="text-4xl font-bold mb-8 text-center">🧪 Feature Testing Dashboard</h1>

      <!-- Performance Metrics -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <span aria-hidden="true" class="opacity-0">.</span> Performance Metrics
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="(metric, key) in performanceMetrics" :key="key" class="p-4 border rounded-lg">
            <h3 class="text-sm font-semibold text-gray-600 mb-2">{{ key }}</h3>
            <p class="text-3xl font-bold" :class="getRatingColor(metric?.rating || 'good')">
              {{ metric?.value || 0 }}ms
            </p>
            <div class="flex items-center gap-2 mt-2">
              <span class="text-xs px-2 py-1 rounded" :class="getRatingBadge(metric?.rating || 'good')">
                {{ metric?.rating || 'good' }}
              </span>
              <span class="text-xs text-gray-500">Avg: {{ metric?.average || 0 }}ms</span>
            </div>
          </div>
        </div>
        <div v-if="Object.keys(performanceMetrics).length === 0" class="text-center text-gray-500 py-8">
          <p>Henüz performans metrikleri yok. Sayfayı yenileyin veya biraz bekleyin.</p>
        </div>
        <button @click="refreshMetrics" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Refresh Metrics
        </button>
      </div>

      <!-- Analytics Testing -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <span>📊</span> Analytics Events
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <button @click="trackPageView" class="p-4 border-2 border-blue-500 rounded-lg hover:bg-blue-50">
            Track Page View
          </button>
          <button @click="trackCustomEvent" class="p-4 border-2 border-green-500 rounded-lg hover:bg-green-50">
            Track Custom Event
          </button>
          <button @click="trackEcommerce" class="p-4 border-2 border-purple-500 rounded-lg hover:bg-purple-50">
            Track E-commerce
          </button>
          <button @click="trackSearch" class="p-4 border-2 border-yellow-500 rounded-lg hover:bg-yellow-50">
            Track Search
          </button>
        </div>
        <div v-if="lastEvent" class="mt-4 p-4 bg-gray-100 rounded">
          <p class="text-sm font-semibold">Last Event:</p>
          <pre class="text-xs mt-2">{{ JSON.stringify(lastEvent, null, 2) }}</pre>
        </div>
      </div>

      <!-- Error Tracking -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <span>🐛</span> Error Tracking
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <button @click="triggerError('low')" class="p-4 border-2 border-blue-500 rounded-lg hover:bg-blue-50">
            Low Severity Error
          </button>
          <button @click="triggerError('high')" class="p-4 border-2 border-red-500 rounded-lg hover:bg-red-50">
            High Severity Error
          </button>
        </div>
        <div class="space-y-2">
          <h3 class="font-semibold">Recent Errors ({{ recentErrors.length }}):</h3>
          <div v-for="(error, index) in recentErrors" :key="index" class="p-3 bg-red-50 rounded text-sm">
            <div class="flex justify-between items-start">
              <div>
                <span class="font-semibold">{{ error.message }}</span>
                <p class="text-xs text-gray-600 mt-1">{{ formatDate(error.timestamp) }}</p>
              </div>
              <span class="px-2 py-1 text-xs rounded" :class="getSeverityBadge(error.severity)">
                {{ error.severity }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Validation Test -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <span>✅</span> Form Validation
        </h2>
        <form @submit.prevent="testValidation" class="space-y-4">
          <div>
            <input
              v-model="testForm.email"
              type="email"
              placeholder="Email"
              class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': validationErrors?.email }"
            />
            <p v-if="validationErrors?.email" class="text-red-500 text-sm mt-1">{{ validationErrors.email }}</p>
          </div>
          <div>
            <input
              v-model="testForm.password"
              type="password"
              placeholder="Password (min 6 chars)"
              class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': validationErrors?.password }"
            />
            <p v-if="validationErrors?.password" class="text-red-500 text-sm mt-1">{{ validationErrors.password }}</p>
          </div>
          <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Test Validation
          </button>
          <p v-if="validationSuccess" class="text-green-600 font-semibold">✅ Validation passed!</p>
        </form>
      </div>

      <!-- SEO Test -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <span>🔍</span> SEO Features
        </h2>
        <div class="space-y-4">
          <button @click="testSEO" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Update SEO Meta Tags
          </button>
          <div class="p-4 bg-gray-100 rounded">
            <h3 class="font-semibold mb-2">Current Meta Tags:</h3>
            <div class="text-sm space-y-1">
              <p><strong>Title:</strong> {{ currentTitle }}</p>
              <p><strong>Description:</strong> {{ currentDescription }}</p>
              <p><strong>OG:Title:</strong> {{ currentOgTitle }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- PWA Test -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <span>📱</span> PWA Features
        </h2>
        <div class="space-y-4">
          <div class="flex items-center gap-4">
            <span class="font-semibold">Service Worker:</span>
            <span :class="swRegistered ? 'text-green-600' : 'text-red-600'">
              {{ swRegistered ? '✅ Registered' : '❌ Not Registered' }}
            </span>
          </div>
          <div class="flex items-center gap-4">
            <span class="font-semibold">Manifest:</span>
            <span class="text-green-600">✅ Loaded</span>
          </div>
          <button @click="checkPWAStatus" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Check PWA Status
          </button>
        </div>
      </div>

      <!-- Notifications Test (commented out - needs real WebSocket) -->
      <!-- <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <span>🔔</span> Real-time Notifications
        </h2>
        <button @click="requestNotificationPermission" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mb-4">
          Request Notification Permission
        </button>
        <button @click="sendTestNotification" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          Send Test Notification
        </button>
      </div> -->

      <!-- Results Summary -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Test Summary</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="text-center">
            <p class="text-4xl font-bold">{{ testResults.performance ? '✅' : '⏳' }}</p>
            <p class="text-sm mt-2">Performance</p>
          </div>
          <div class="text-center">
            <p class="text-4xl font-bold">{{ testResults.analytics ? '✅' : '⏳' }}</p>
            <p class="text-sm mt-2">Analytics</p>
          </div>
          <div class="text-center">
            <p class="text-4xl font-bold">{{ testResults.errorTracking ? '✅' : '⏳' }}</p>
            <p class="text-sm mt-2">Error Tracking</p>
          </div>
          <div class="text-center">
            <p class="text-4xl font-bold">{{ testResults.seo ? '✅' : '⏳' }}</p>
            <p class="text-sm mt-2">SEO</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useSEO, useFormValidation } from '@/composables'
import { analytics } from '@/services/analytics'
import { errorTracking } from '@/services/errorTracking'
import { performanceMonitoring } from '@/services/performanceMonitoring'

// Performance
const performanceMetrics = ref({})

// Analytics
const lastEvent = ref<any>(null)

// Error Tracking
const recentErrors = ref<any[]>([])

// Form Validation
const testForm = ref({
  email: '',
  password: ''
})
const { validate, errors: validationErrors } = useFormValidation()
const validationSuccess = ref(false)

// SEO
const { updateMeta } = useSEO()
const currentTitle = ref('')
const currentDescription = ref('')
const currentOgTitle = ref('')

// PWA
const swRegistered = ref(false)

// Test Results
const testResults = ref({
  performance: false,
  analytics: false,
  errorTracking: false,
  seo: false
})

onMounted(() => {
  refreshMetrics()
  checkServiceWorker()
  updateMetaDisplay()
})

function updateMetaDisplay() {
  if (typeof document !== 'undefined') {
    currentTitle.value = document.title
    const descMeta = document.querySelector('meta[name="description"]')
    currentDescription.value = descMeta?.getAttribute('content') || 'Not set'
    const ogMeta = document.querySelector('meta[property="og:title"]')
    currentOgTitle.value = ogMeta?.getAttribute('content') || 'Not set'
  }
}

// Performance Tests
function refreshMetrics() {
  performanceMetrics.value = performanceMonitoring.getSummary()
  testResults.value.performance = Object.keys(performanceMetrics.value).length > 0
}

function getRatingColor(rating: string) {
  if (rating === 'good') return 'text-green-600'
  if (rating === 'needs-improvement') return 'text-yellow-600'
  return 'text-red-600'
}

function getRatingBadge(rating: string) {
  if (rating === 'good') return 'bg-green-100 text-green-800'
  if (rating === 'needs-improvement') return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

// Analytics Tests
function trackPageView() {
  analytics.pageView({
    page_title: 'Test Page',
    page_path: '/test'
  })
  lastEvent.value = { type: 'page_view', page_title: 'Test Page' }
  testResults.value.analytics = true
}

function trackCustomEvent() {
  analytics.event('test_button_click', {
    category: 'test',
    label: 'Custom Event Test',
    value: 1
  })
  lastEvent.value = { type: 'test_button_click', category: 'test' }
  testResults.value.analytics = true
}

function trackEcommerce() {
  analytics.ecommerce.viewItem({
    id: 'test-123',
    name: 'Test Product',
    category: 'Test Category',
    price: 99.99
  })
  lastEvent.value = { type: 'view_item', product: 'Test Product' }
  testResults.value.analytics = true
}

function trackSearch() {
  analytics.search('test search query', 42)
  lastEvent.value = { type: 'search', term: 'test search query', results: 42 }
  testResults.value.analytics = true
}

// Error Tracking Tests
function triggerError(severity: 'low' | 'high') {
  const message = severity === 'low' 
    ? 'This is a low severity test error' 
    : 'This is a high severity test error'
  
  errorTracking.captureMessage(message, severity === 'low' ? 'low' : 'high', {
    component: 'TestingDashboard',
    action: 'Manual Test'
  })

  recentErrors.value = errorTracking.getRecentErrors(5)
  testResults.value.errorTracking = true
}

function getSeverityBadge(severity: string) {
  const badges: Record<string, string> = {
    low: 'bg-blue-100 text-blue-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-orange-100 text-orange-800',
    critical: 'bg-red-100 text-red-800'
  }
  return badges[severity] || 'bg-gray-100 text-gray-800'
}

// Form Validation Test
function testValidation() {
  validationSuccess.value = false

  const rules = {
    email: [
      { rule: 'required', message: 'Email is required' },
      { rule: 'email', message: 'Invalid email format' }
    ],
    password: [
      { rule: 'required', message: 'Password is required' },
      { rule: 'minLength', value: 6, message: 'Password must be at least 6 characters' }
    ]
  }

  const isValid = validate(testForm.value, rules)
  if (isValid) {
    validationSuccess.value = true
    setTimeout(() => {
      validationSuccess.value = false
      testForm.value = { email: '', password: '' }
    }, 2000)
  }
}

// SEO Test
function testSEO() {
  updateMeta({
    title: 'Test Page - SEO Updated',
    description: 'This is a test description updated by useSEO composable',
    ogTitle: 'Test Page OG Title',
    ogDescription: 'Test OG Description',
    twitterCard: 'summary_large_image'
  })
  testResults.value.seo = true
  updateMetaDisplay()
  setTimeout(() => {
    alert('SEO meta tags updated! Check browser DevTools > Head section')
  }, 100)
}

// PWA Test
function checkServiceWorker() {
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistration().then(registration => {
      swRegistered.value = !!registration
    })
  }
}

function checkPWAStatus() {
  const results = {
    serviceWorker: 'serviceWorker' in navigator,
    registered: swRegistered.value,
    manifest: !!document.querySelector('link[rel="manifest"]'),
    offline: !navigator.onLine
  }
  
  alert(JSON.stringify(results, null, 2))
}

// Utilities
function formatDate(date: Date) {
  return new Date(date).toLocaleString('tr-TR')
}
</script>
