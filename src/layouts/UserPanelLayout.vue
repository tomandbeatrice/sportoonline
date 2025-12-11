<template>
  <div class="min-h-screen bg-slate-50 font-sans">
    <!-- Sidebar -->
    <aside 
      class="fixed left-0 top-0 z-40 h-screen w-64 bg-gradient-to-b from-blue-600 via-blue-700 to-purple-700 text-white transition-transform lg:translate-x-0"
      :class="{ '-translate-x-full': !isSidebarOpen }"
    >
      <!-- User Profile Section -->
      <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-3 mb-4">
          <div class="relative">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-yellow-300 to-orange-400 flex items-center justify-center font-bold text-lg shadow-lg text-purple-900">
              {{ userInitials }}
            </div>
            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-400 rounded-full border-2 border-white flex items-center justify-center text-xs font-bold text-green-900">
              {{ userLevel }}
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-bold text-white truncate">{{ userName }}</p>
            <p class="text-xs text-blue-200 truncate">{{ userEmail }}</p>
          </div>
        </div>
        
        <!-- Points & Rewards -->
        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs text-blue-100">Puan Bakiyesi</span>
            <div class="flex items-center gap-1">
              <Star class="w-4 h-4 text-yellow-300 fill-yellow-300" />
              <span class="font-bold text-white">{{ userPoints }}</span>
            </div>
          </div>
          <div class="w-full bg-white/20 rounded-full h-1.5">
            <div class="bg-gradient-to-r from-yellow-300 to-orange-400 h-1.5 rounded-full transition-all" :style="{ width: pointsProgress + '%' }"></div>
          </div>
          <p class="text-[10px] text-blue-200">Sonraki seviyeye {{ pointsToNextLevel }} puan</p>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="px-4 py-3 grid grid-cols-2 gap-2 border-b border-white/10">
        <router-link to="/user/orders" class="bg-white/10 backdrop-blur-sm rounded-lg p-2 hover:bg-white/20 transition-all group">
          <div class="flex items-center gap-2">
            <ShoppingBag class="w-4 h-4 text-blue-200 group-hover:text-white transition-colors" />
            <div class="flex-1 min-w-0">
              <p class="text-[10px] text-blue-200">Siparişler</p>
              <p class="font-bold text-sm text-white">{{ orderCount }}</p>
            </div>
          </div>
        </router-link>
        <router-link to="/user/favorites" class="bg-white/10 backdrop-blur-sm rounded-lg p-2 hover:bg-white/20 transition-all group">
          <div class="flex items-center gap-2">
            <Heart class="w-4 h-4 text-pink-300 group-hover:text-pink-200 transition-colors" />
            <div class="flex-1 min-w-0">
              <p class="text-[10px] text-blue-200">Favoriler</p>
              <p class="font-bold text-sm text-white">{{ favoriteCount }}</p>
            </div>
          </div>
        </router-link>
      </div>

      <!-- Navigation -->
      <nav class="px-4 py-4 space-y-1 overflow-y-auto h-[calc(100vh-400px)] custom-scrollbar">
        <router-link 
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all group"
          :class="isActive(item.path) 
            ? 'bg-white text-blue-700 font-medium shadow-lg' 
            : 'text-blue-100 hover:bg-white/10 hover:text-white'"
        >
          <component :is="item.icon" class="w-5 h-5 transition-transform group-hover:scale-110" />
          <span class="flex-1">{{ item.name }}</span>
          <span v-if="item.badge" class="px-2 py-0.5 text-xs rounded-full bg-red-500 text-white font-bold">
            {{ item.badge }}
          </span>
        </router-link>
      </nav>

      <!-- Cart Quick Access -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10 bg-gradient-to-t from-purple-900 to-transparent">
        <router-link 
          to="/cart" 
          class="flex items-center justify-between p-3 bg-white text-blue-700 rounded-xl font-medium hover:shadow-lg transition-all group"
        >
          <div class="flex items-center gap-2">
            <ShoppingCart class="w-5 h-5 group-hover:scale-110 transition-transform" />
            <span>Sepetim</span>
          </div>
          <div class="flex items-center gap-2">
            <span v-if="cartItemCount > 0" class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full font-bold">{{ cartItemCount }}</span>
            <ChevronRight class="w-4 h-4" />
          </div>
        </router-link>
      </div>
    </aside>

    <!-- Mobile Overlay -->
    <div 
      v-if="isSidebarOpen" 
      @click="isSidebarOpen = false" 
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden transition-opacity"
    ></div>

    <!-- Main Content -->
    <div class="lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
      <!-- Top Header -->
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200 px-4 py-3 shadow-sm">
        <div class="flex items-center justify-between max-w-7xl mx-auto w-full">
          <div class="flex items-center gap-4">
            <button 
              @click="isSidebarOpen = !isSidebarOpen"
              class="p-2 rounded-lg hover:bg-slate-100 lg:hidden text-slate-600 transition-colors"
              aria-label="Toggle sidebar"
            >
              <Menu class="w-6 h-6" />
            </button>
            
            <!-- Breadcrumbs -->
            <nav class="hidden md:flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
              <router-link to="/" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                <Home class="w-4 h-4" />
                <span>Ana Sayfa</span>
              </router-link>
              <ChevronRight class="w-4 h-4 text-slate-400" />
              <span class="font-medium text-slate-900">{{ currentRouteName }}</span>
            </nav>
          </div>

          <div class="flex items-center gap-2 md:gap-4">
            <!-- Active Orders -->
            <router-link 
              v-if="activeOrderCount > 0"
              to="/user/orders"
              class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-blue-50 to-purple-50 text-blue-700 rounded-full text-xs font-medium hover:from-blue-100 hover:to-purple-100 transition border border-blue-200"
            >
              <Package class="w-4 h-4" />
              <span>{{ activeOrderCount }} Aktif Sipariş</span>
            </router-link>

            <!-- Notifications -->
            <div class="relative">
              <button 
                @click="toggleNotifications"
                class="p-2 rounded-full hover:bg-slate-100 relative text-slate-600 transition-colors"
                aria-label="Notifications"
              >
                <Bell class="w-5 h-5" />
                <span v-if="unreadNotifications > 0" class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white ring-2 ring-white"></span>
              </button>
              
              <!-- Notification Dropdown -->
              <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50 animate-in fade-in slide-in-from-top-2">
                <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center">
                  <h3 class="font-semibold text-sm">Bildirimler</h3>
                  <button class="text-xs text-blue-600 hover:underline" @click="markAllRead">Tümünü Oku</button>
                </div>
                <div class="max-h-64 overflow-y-auto">
                  <div v-for="notification in notifications" :key="notification.id" class="px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-50 last:border-0">
                    <p class="text-sm font-medium text-slate-800">{{ notification.title }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ notification.time }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- User Menu Button -->
            <button 
              @click="toggleUserMenu"
              class="flex items-center gap-2 p-1.5 rounded-full hover:bg-slate-100 transition-all border border-transparent hover:border-slate-200"
              aria-label="User menu"
            >
              <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                {{ userInitials }}
              </div>
              <ChevronDown class="w-4 h-4 text-slate-400 hidden md:block" />
            </button>

            <!-- User Dropdown -->
            <div v-if="showUserMenu" class="absolute right-4 top-16 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50 animate-in fade-in slide-in-from-top-2">
              <div class="px-4 py-2 border-b border-slate-100">
                <p class="text-sm font-medium text-slate-900">{{ userName }}</p>
                <p class="text-xs text-slate-500">Seviye {{ userLevel }}</p>
              </div>
              <router-link to="/user" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                Profilim
              </router-link>
              <router-link to="/user/settings" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                Ayarlar
              </router-link>
              <div class="border-t border-slate-100 my-1"></div>
              <button @click="handleLogout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                Çıkış Yap
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 md:p-8 overflow-x-hidden max-w-7xl mx-auto w-full">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
    
    <!-- Backdrop for dropdowns -->
    <div v-if="showNotifications || showUserMenu" @click="closeDropdowns" class="fixed inset-0 z-40 bg-transparent"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { 
  Menu, Bell, ChevronRight, ChevronDown, Home, Star, ShoppingBag, Heart, 
  ShoppingCart, Package, User, MapPin, Settings, Gift, CreditCard
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const isSidebarOpen = ref(false)
const showNotifications = ref(false)
const showUserMenu = ref(false)
const unreadNotifications = ref(2)

// User data - can be connected to auth store
const userName = ref('Ahmet Yılmaz')
const userEmail = ref('ahmet@example.com')
const userLevel = ref(5)
const userPoints = ref(850)
const pointsToNextLevel = ref(150)
const orderCount = ref(12)
const favoriteCount = ref(8)
const cartItemCount = ref(3)
const activeOrderCount = ref(2)

const userInitials = computed(() => {
  return userName.value.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const pointsProgress = computed(() => {
  return Math.min((userPoints.value / (userPoints.value + pointsToNextLevel.value)) * 100, 100)
})

const currentRouteName = computed(() => {
  const nameMap: Record<string, string> = {
    'user': 'Profilim',
    'user-orders': 'Siparişlerim',
    'user-favorites': 'Favorilerim',
    'user-addresses': 'Adreslerim',
    'user-settings': 'Ayarlar',
    'user-wallet': 'Cüzdanım',
    'user-rewards': 'Ödüllerim',
  }
  return nameMap[route.name as string] || 'Hesabım'
})

const menuItems = [
  { name: 'Profilim', path: '/user', icon: User },
  { name: 'Siparişlerim', path: '/user/orders', icon: Package, badge: activeOrderCount.value > 0 ? activeOrderCount.value : null },
  { name: 'Favorilerim', path: '/user/favorites', icon: Heart },
  { name: 'Adreslerim', path: '/user/addresses', icon: MapPin },
  { name: 'Cüzdanım', path: '/user/wallet', icon: CreditCard },
  { name: 'Ödüllerim', path: '/user/rewards', icon: Gift },
  { name: 'Ayarlar', path: '/user/settings', icon: Settings },
]

const notifications = ref([
  { id: 1, title: 'Siparişiniz kargoya verildi', time: '10 dakika önce' },
  { id: 2, title: 'Favori ürününüzde indirim!', time: '2 saat önce' },
])

const isActive = (path: string) => {
  return route.path === path || route.path.startsWith(path + '/')
}

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  showUserMenu.value = false
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  showNotifications.value = false
}

const closeDropdowns = () => {
  showNotifications.value = false
  showUserMenu.value = false
}

const markAllRead = () => {
  unreadNotifications.value = 0
}

const handleLogout = () => {
  console.log('Logging out...')
  router.push('/login')
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

.animate-in {
  animation: slideIn 0.2s ease;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style>
