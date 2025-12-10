import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

declare global {
  interface Window {
    Pusher: typeof Pusher
    Echo: Echo<any>
  }
}

window.Pusher = Pusher

// Laravel Echo Configuration
window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY || 'local-key',
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
  wsHost: import.meta.env.VITE_PUSHER_HOST || '127.0.0.1',
  wsPort: import.meta.env.VITE_PUSHER_PORT || 6001,
  wssPort: import.meta.env.VITE_PUSHER_PORT || 6001,
  forceTLS: (import.meta.env.VITE_PUSHER_SCHEME || 'http') === 'https',
  enabledTransports: ['ws', 'wss'],
  disableStats: true,
})

export default window.Echo
