const CACHE_NAME = 'sportoonline-v1'
const RUNTIME_CACHE = 'sportoonline-runtime'

// Assets to cache on install
const PRECACHE_URLS = [
  '/',
  '/index.html',
  '/favicon.ico',
]

// Install event - cache static assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_URLS)
    })
  )
  self.skipWaiting()
})

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME && name !== RUNTIME_CACHE)
          .map((name) => caches.delete(name))
      )
    })
  )
  self.clients.claim()
})

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
  const { request } = event

  // Skip cross-origin requests
  if (!request.url.startsWith(self.location.origin)) {
    return
  }

  // API requests - network first, cache fallback
  if (request.url.includes('/api/')) {
    event.respondWith(
      fetch(request)
        .then((response) => {
          const clonedResponse = response.clone()
          caches.open(RUNTIME_CACHE).then((cache) => {
            cache.put(request, clonedResponse)
          })
          return response
        })
        .catch(() => {
          return caches.match(request).then((cachedResponse) => {
            return cachedResponse || new Response('Offline - no cached data available')
          })
        })
    )
    return
  }

  // Static assets - cache first, network fallback
  event.respondWith(
    caches.match(request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse
      }

      return fetch(request).then((response) => {
        // Don't cache non-successful responses
        if (!response || response.status !== 200 || response.type === 'error') {
          return response
        }

        const clonedResponse = response.clone()
        caches.open(RUNTIME_CACHE).then((cache) => {
          cache.put(request, clonedResponse)
        })

        return response
      })
    })
  )
})

// Background sync for offline actions
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-orders') {
    event.waitUntil(syncOrders())
  }
})

async function syncOrders() {
  // Sync pending orders when online
  try {
    const response = await fetch('/api/orders/sync', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
    })
    return response.ok
  } catch (error) {
    throw new Error('Sync failed')
  }
}

// Push notifications
self.addEventListener('push', (event) => {
  const data = event.data ? event.data.json() : {}
  
  const options = {
    body: data.body || 'Yeni bildiriminiz var',
    icon: '/icon-192x192.png',
    badge: '/badge-72x72.png',
    vibrate: [200, 100, 200],
    data: data.url || '/',
  }

  event.waitUntil(
    self.registration.showNotification(data.title || 'SportoOnline', options)
  )
})

self.addEventListener('notificationclick', (event) => {
  event.notification.close()
  event.waitUntil(
    clients.openWindow(event.notification.data)
  )
})
