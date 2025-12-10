<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-900">
    <!-- Sidebar -->
    <AdminSidebar 
      :is-open="isSidebarOpen" 
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
            >
              <Menu class="w-6 h-6" />
            </button>
            
            <!-- Breadcrumbs -->
            <nav class="hidden md:flex items-center gap-2 text-sm text-slate-500">
              <router-link to="/admin/dashboard" class="hover:text-indigo-600 transition-colors flex items-center gap-1">
                <LayoutDashboard class="w-4 h-4" />
                <span>Admin</span>
              </router-link>
              <ChevronRight class="w-4 h-4 text-slate-400" />
              <span class="font-medium text-slate-900">{{ currentRouteName }}</span>
            </nav>
          </div>

          <div class="flex items-center gap-2 md:gap-4">
            <!-- Notifications -->
            <div class="relative">
              <button 
                @click="toggleNotifications"
                class="p-2 rounded-full hover:bg-slate-100 relative text-slate-600 transition-colors"
              >
                <Bell class="w-5 h-5" />
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white ring-2 ring-white"></span>
              </button>
              
              <!-- Notification Dropdown -->
              <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50 animate-in fade-in slide-in-from-top-2">
                <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center">
                  <h3 class="font-semibold text-sm">Bildirimler</h3>
                  <span class="text-xs text-indigo-600 cursor-pointer hover:underline">Tümünü Oku</span>
                </div>
                <div class="max-h-64 overflow-y-auto">
                  <div class="px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-50 last:border-0">
                    <p class="text-sm font-medium text-slate-800">Yeni Sipariş #1234</p>
                    <p class="text-xs text-slate-500 mt-0.5">2 dakika önce</p>
                  </div>
                  <div class="px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-50 last:border-0">
                    <p class="text-sm font-medium text-slate-800">Stok Uyarısı: Nike Air Max</p>
                    <p class="text-xs text-slate-500 mt-0.5">15 dakika önce</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- User Menu -->
            <div class="relative">
              <button 
                @click="toggleUserMenu"
                class="flex items-center gap-3 p-1.5 rounded-full hover:bg-slate-100 transition-all border border-transparent hover:border-slate-200"
              >
                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                  A
                </div>
                <ChevronDown class="w-4 h-4 text-slate-400 hidden md:block" />
              </button>

              <!-- User Dropdown -->
              <div v-if="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50 animate-in fade-in slide-in-from-top-2">
                <div class="px-4 py-2 border-b border-slate-100">
                  <p class="text-sm font-medium text-slate-900">Admin User</p>
                  <p class="text-xs text-slate-500">admin@sportoonline.com</p>
                </div>
                <router-link to="/admin/profile" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                  Profil
                </router-link>
                <router-link to="/admin/settings" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                  Ayarlar
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
import AdminSidebar from '@/components/admin/AdminSidebar.vue'
import { Menu, Bell, ChevronRight, ChevronDown, LayoutDashboard } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const isSidebarOpen = ref(true) // Desktop'ta sidebar her zaman açık
const showNotifications = ref(false)
const showUserMenu = ref(false)

const currentRouteName = computed(() => {
  const nameMap: Record<string, string> = {
    'AdminDashboard': 'Dashboard',
    'AdminOrders': 'Sipariş Yönetimi',
    'AdminSellers': 'Satıcı Yönetimi',
    'AdminCustomers': 'Müşteri Yönetimi',
    'AdminProducts': 'Ürün Yönetimi',
    'AdminCategories': 'Kategori Yönetimi',
    'AdminCampaigns': 'Kampanya Yönetimi',
    'AdminReports': 'Raporlar',
    'AdminSettings': 'Ayarlar',
    'AdminNotifications': 'Bildirimler',
    'AdminThemes': 'Tema Yönetimi',
    'AdminPages': 'Sayfa Yönetimi',
    'AdminBlog': 'Blog Yönetimi',
    'AdminMarketplace': 'Pazaryeri',
    'AdminUsers': 'Kullanıcı Yönetimi'
  }
  return nameMap[route.name as string] || route.name || 'Sayfa'
})

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
</style>
