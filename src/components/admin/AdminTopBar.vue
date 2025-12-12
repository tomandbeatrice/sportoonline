<template>
  <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-sm border-b border-slate-200 shadow-sm">
    <div class="flex items-center justify-between px-4 py-3">
      <!-- Left: Toggle + Breadcrumb -->
      <div class="flex items-center gap-3">
        <!-- Mobile Toggle -->
        <button 
          @click="$emit('toggle-sidebar')"
          class="p-2 hover:bg-slate-100 rounded-lg transition-colors lg:hidden"
          aria-label="Toggle Menu"
        >
          <Menu class="w-5 h-5 text-slate-700" />
        </button>

        <!-- Desktop Collapse Toggle -->
        <button 
          @click="$emit('toggle-collapse')"
          class="hidden lg:flex p-2 hover:bg-slate-100 rounded-lg transition-colors"
          :title="isCollapsed ? 'Genişlet' : 'Daralt'"
        >
          <component :is="isCollapsed ? 'ChevronsRight' : 'ChevronsLeft'" class="w-5 h-5 text-slate-700" />
        </button>

        <!-- Breadcrumb -->
        <nav class="hidden md:flex items-center gap-2 text-sm">
          <router-link to="/admin/dashboard" class="flex items-center gap-1.5 text-slate-500 hover:text-indigo-600 transition-colors">
            <LayoutDashboard class="w-4 h-4" />
            <span class="font-medium">Dashboard</span>
          </router-link>
          <ChevronRight class="w-4 h-4 text-slate-300" />
          <span class="font-semibold text-slate-900">{{ currentPageTitle }}</span>
        </nav>
      </div>

      <!-- Right: Actions -->
      <div class="flex items-center gap-2">
        <!-- Quick Search -->
        <button 
          @click="openQuickSearch"
          class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm text-slate-600 transition-colors"
        >
          <Search class="w-4 h-4" />
          <span>Ara...</span>
          <kbd class="hidden lg:inline-flex px-1.5 py-0.5 bg-white rounded text-xs border border-slate-300">⌘K</kbd>
        </button>

        <!-- Notifications -->
        <button 
          @click="toggleNotifications"
          class="relative p-2 hover:bg-slate-100 rounded-lg transition-colors"
          aria-label="Bildirimler"
        >
          <Bell class="w-5 h-5 text-slate-700" />
          <span v-if="notificationCount > 0" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
        </button>

        <!-- Notifications Dropdown -->
        <div 
          v-if="showNotifications"
          v-click-outside="() => showNotifications = false"
          class="absolute right-4 top-16 w-80 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50"
        >
          <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-semibold text-sm">Bildirimler</h3>
            <button class="text-xs text-indigo-600 hover:underline">Tümünü Oku</button>
          </div>
          <div class="max-h-96 overflow-y-auto">
            <div v-for="n in 3" :key="n" class="px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100">
              <p class="text-sm font-medium text-slate-900">Yeni sipariş alındı</p>
              <p class="text-xs text-slate-500 mt-1">2 dakika önce</p>
            </div>
          </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative">
          <button 
            @click="showProfileMenu = !showProfileMenu"
            class="flex items-center gap-2 p-1.5 hover:bg-slate-100 rounded-lg transition-colors"
          >
            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
              {{ userInitials }}
            </div>
            <div class="hidden lg:block text-left">
              <p class="text-sm font-medium text-slate-900">{{ userName }}</p>
              <p class="text-xs text-slate-500">Administrator</p>
            </div>
            <ChevronDown class="w-4 h-4 text-slate-400 hidden lg:block" />
          </button>

          <!-- Profile Dropdown Menu -->
          <div 
            v-if="showProfileMenu"
            v-click-outside="() => showProfileMenu = false"
            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50"
          >
            <div class="px-4 py-3 border-b border-slate-100">
              <p class="text-sm font-semibold text-slate-900">{{ userName }}</p>
              <p class="text-xs text-slate-500">admin@sportoonline.com</p>
            </div>
            <router-link 
              to="/admin/profile" 
              class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 text-sm text-slate-700 transition-colors"
            >
              <User class="w-4 h-4" />
              <span>Profilim</span>
            </router-link>
            <router-link 
              to="/admin/settings" 
              class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 text-sm text-slate-700 transition-colors"
            >
              <Settings class="w-4 h-4" />
              <span>Ayarlar</span>
            </router-link>
            <hr class="my-2 border-slate-100">
            <button 
              @click="$emit('logout')" 
              class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 text-sm text-red-600 transition-colors"
            >
              <LogOut class="w-4 h-4" />
              <span>Çıkış Yap</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Menu, Search, Bell, User, Settings, LogOut, LayoutDashboard, ChevronRight, ChevronDown, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'

const props = defineProps<{
  isCollapsed: boolean
  userName?: string
}>()

defineEmits(['toggle-sidebar', 'toggle-collapse', 'logout'])

const route = useRoute()
const showNotifications = ref(false)
const showProfileMenu = ref(false)
const notificationCount = ref(3)

const userInitials = computed(() => {
  const name = props.userName || 'Admin User'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const currentPageTitle = computed(() => {
  const titles: Record<string, string> = {
    '/admin/dashboard': 'Dashboard',
    '/admin/orders': 'Siparişler',
    '/admin/products': 'Ürünler',
    '/admin/sellers': 'Satıcılar',
    '/admin/customers': 'Müşteriler',
    '/admin/users': 'Kullanıcı Yönetimi',
    '/admin/category-attributes': 'Kategori & Özellikler',
    '/admin/campaign-coupon': 'Kampanya & Kuponlar',
    '/admin/system-settings-enhanced': 'Sistem Ayarları',
    '/admin/moderation': 'Moderasyon',
    '/test/e2e': 'E2E Testler',
  }
  return titles[route.path] || 'Sayfa'
})

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  showProfileMenu.value = false
}

const openQuickSearch = () => {
  window.dispatchEvent(new KeyboardEvent('keydown', { key: 'k', metaKey: true, ctrlKey: true }))
}

// Click outside directive
const vClickOutside = {
  mounted(el: HTMLElement, binding: any) {
    el.clickOutsideEvent = (event: Event) => {
      if (!(el === event.target || el.contains(event.target as Node))) {
        binding.value()
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el: HTMLElement) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>
