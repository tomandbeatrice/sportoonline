// Service Worker registration and PWA utilities

export function registerServiceWorker() {
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker
        .register('/sw.js')
        .then((registration) => {
          console.log('✅ Service Worker registered:', registration.scope)
          
          // Check for updates
          registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing
            if (newWorker) {
              newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                  // New content is available
                  if (confirm('Yeni versiyon mevcut! Sayfayı yenilemek ister misiniz?')) {
                    globalThis.location.reload()
                  }
                }
              })
            }
          })
        })
        .catch((error) => {
          console.error('❌ Service Worker registration failed:', error)
        })
    })
  }
}

export function unregisterServiceWorker() {
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.ready
      .then((registration) => {
        registration.unregister()
      })
      .catch((error) => {
        console.error('Service Worker unregistration failed:', error)
      })
  }
}

// Install prompt
let deferredPrompt: any = null

export function setupInstallPrompt() {
  globalThis.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    deferredPrompt = e
  })
}

export async function showInstallPrompt(): Promise<boolean> {
  if (!deferredPrompt) {
    return false
  }

  deferredPrompt.prompt()
  const { outcome } = await deferredPrompt.userChoice
  deferredPrompt = null
  
  return outcome === 'accepted'
}

export function isInstallable(): boolean {
  return deferredPrompt !== null
}

// Check if running as PWA
export function isPWA(): boolean {
  return globalThis.matchMedia('(display-mode: standalone)').matches ||
         (globalThis.navigator as any).standalone === true
}

// Network status
export function useNetworkStatus() {
  const isOnline = navigator.onLine

  window.addEventListener('online', () => {
    console.log('✅ Network: Online')
  })

  window.addEventListener('offline', () => {
    console.log('❌ Network: Offline')
  })

  return isOnline
}
