<template>
  <div class="min-h-screen bg-slate-50 font-sans">
    <!-- Sidebar -->
    <SellerSidebar 
      :is-open="isSidebarOpen" 
      :user-name="storeName"
      @close="isSidebarOpen = false"
      @logout="handleLogout"
    />

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
              <router-link to="/seller" class="hover:text-orange-600 transition-colors flex items-center gap-1">
                <Store class="w-4 h-4" />
                <span>Satıcı Paneli</span>
              </router-link>
              <ChevronRight class="w-4 h-4 text-slate-400" />
              <span class="font-medium text-slate-900">{{ currentRouteName }}</span>
            </nav>
          </div>

          <div class="flex items-center gap-2 md:gap-4">
            <!-- Quick Campaign Button -->
            <button class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-orange-50 to-red-50 text-orange-700 rounded-full text-xs font-medium hover:from-orange-100 hover:to-red-100 transition border border-orange-200">
              <Megaphone class="w-4 h-4" />
              <span>Kampanyaya Katıl</span>
            </button>

            <div class="h-6 w-px bg-slate-200 hidden md:block"></div>

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
                  <button class="text-xs text-orange-600 hover:underline" @click="markAllRead">Tümünü Oku</button>
                </div>
                <div class="max-h-64 overflow-y-auto">
                  <div v-for="notification in notifications" :key="notification.id" class="px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-50 last:border-0">
                    <p class="text-sm font-medium text-slate-800">{{ notification.title }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ notification.time }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Messages -->
            <button 
              class="p-2 rounded-full hover:bg-slate-100 text-slate-600 transition-colors relative"
              aria-label="Messages"
            >
              <MessageSquare class="w-5 h-5" />
              <span v-if="unreadMessages > 0" class="absolute top-1.5 right-1.5 w-2 h-2 bg-green-500 rounded-full border border-white ring-2 ring-white"></span>
            </button>

            <!-- User Menu -->
            <div class="relative">
              <button 
                @click="toggleUserMenu"
                class="flex items-center gap-2 p-1.5 rounded-full hover:bg-slate-100 transition-all border border-transparent hover:border-slate-200"
                aria-label="User menu"
              >
                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                  {{ storeInitials }}
                </div>
                <ChevronDown class="w-4 h-4 text-slate-400 hidden md:block" />
              </button>

              <!-- User Dropdown -->
              <div v-if="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50 animate-in fade-in slide-in-from-top-2">
                <div class="px-4 py-2 border-b border-slate-100">
                  <p class="text-sm font-medium text-slate-900">{{ storeName }}</p>
                  <p class="text-xs text-slate-500">Mağaza Sahibi</p>
                </div>
                <router-link to="/seller/onboarding" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-orange-600 transition-colors">
                  Mağaza Ayarları
                </router-link>
                <router-link to="/seller/financial-report" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-orange-600 transition-colors">
                  Finans Raporu
                </router-link>
                <div class="border-t border-slate-100 my-1"></div>
                <button @click="handleLogout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                  Çıkış Yap
                </button>
              </div>
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
import SellerSidebar from '@/components/seller/SellerSidebar.vue'
import { Menu, Bell, ChevronRight, ChevronDown, Store, MessageSquare, Megaphone } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const isSidebarOpen = ref(false)
const showNotifications = ref(false)
const showUserMenu = ref(false)
const unreadNotifications = ref(3)
const unreadMessages = ref(1)

// Store data - can be connected to auth store
const storeName = ref('Mağazam')
const storeInitials = computed(() => {
  return storeName.value.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const currentRouteName = computed(() => {
  const nameMap: Record<string, string> = {
    'seller': 'Dashboard',
    'seller-dashboard': 'Özet',
    'seller-orders': 'Siparişler',
    'seller-products': 'Ürünlerim',
    'seller-campaigns': 'Kampanyalar',
    'seller-financial-report': 'Finansal Raporlar',
    'seller-onboarding': 'Mağaza Ayarları',
  }
  return nameMap[route.name as string] || 'Dashboard'
})

const notifications = ref([
  { id: 1, title: 'Yeni sipariş alındı #5421', time: '5 dakika önce' },
  { id: 2, title: 'Ürün stoğu azaldı', time: '1 saat önce' },
  { id: 3, title: 'Kampanya onaylandı', time: '2 saat önce' },
])

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

const handleLogout = async () => {
  await authStore.logout()
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
</style>
