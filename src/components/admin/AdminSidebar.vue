<template>
  <aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-slate-900 text-white transition-transform" :class="{ '-translate-x-full': !isOpen }">
    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-700">
      <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg shadow-indigo-500/20">
        S
      </div>
      <div>
        <p class="font-bold text-lg tracking-tight">SportoOnline</p>
        <p class="text-xs text-slate-400 font-medium">Admin Panel</p>
      </div>
    </div>

    <!-- Search -->
    <div class="px-4 py-3 border-b border-slate-800">
      <div class="relative group">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 group-focus-within:text-indigo-400 transition-colors" />
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Menüde ara... (Ctrl+K)"
          class="w-full pl-9 pr-3 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:bg-slate-800 transition-all"
          @keydown.ctrl.k.prevent="focusSearch"
        >
      </div>
    </div>

    <!-- Navigation -->
    <nav class="px-3 py-4 overflow-y-auto h-[calc(100vh-200px)] custom-scrollbar">
      <!-- Favorites -->
      <div v-if="favorites.length" class="mb-6">
        <div class="flex items-center gap-2 px-3 py-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
          <Star class="w-3 h-3" />
          <span>Favoriler</span>
        </div>
        <router-link 
          v-for="item in favorites"
          :key="item.path"
          :to="item.path"
          class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all mb-1"
          :class="isActive(item.path) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
        >
          <component :is="item.icon" class="w-5 h-5" />
          <span class="font-medium">{{ item.name }}</span>
        </router-link>
      </div>

      <!-- Filtered Results -->
      <div v-if="searchQuery" class="space-y-1">
        <router-link 
          v-for="item in filteredItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all"
          :class="isActive(item.path) ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800'"
        >
          <component :is="item.icon" class="w-5 h-5" />
          <span>{{ item.name }}</span>
          <span class="ml-auto text-xs text-slate-500">{{ item.category }}</span>
        </router-link>
        <p v-if="!filteredItems.length" class="px-3 py-4 text-center text-sm text-slate-500">
          Sonuç bulunamadı
        </p>
      </div>

      <!-- Collapsible Categories -->
      <div v-else class="space-y-4">
        <!-- Main Menu -->
        <div class="menu-group">
          <button 
            @click="toggleGroup('main')"
            class="flex items-center justify-between w-full px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider hover:text-slate-200 transition-colors"
          >
            <div class="flex items-center gap-2">
              <LayoutDashboard class="w-3 h-3" />
              <span>Ana Menü</span>
            </div>
            <ChevronDown 
              class="w-3 h-3 transition-transform duration-200" 
              :class="{ 'rotate-180': expandedGroups.main }"
            />
          </button>
          <div v-show="expandedGroups.main" class="space-y-1 mt-1">
            <router-link 
              v-for="item in mainMenu"
              :key="item.path"
              :to="item.path"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all group relative"
              :class="isActive(item.path) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
            >
              <component :is="item.icon" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-slate-400 group-hover:text-white'" />
              <span class="flex-1 font-medium">{{ item.name }}</span>
              <span v-if="item.badge" class="px-1.5 py-0.5 text-[10px] font-bold rounded-full shadow-sm" :class="item.badgeClass">{{ item.badge }}</span>
              <button 
                @click.prevent.stop="toggleFavorite(item)"
                class="opacity-0 group-hover:opacity-100 text-slate-500 hover:text-yellow-400 transition-all absolute right-2"
                :class="{ 'opacity-100 text-yellow-400': isFavorite(item) }"
              >
                <Star class="w-4 h-4" :class="{ 'fill-current': isFavorite(item) }" />
              </button>
            </router-link>
          </div>
        </div>

        <!-- Services -->
        <div class="menu-group">
          <button 
            @click="toggleGroup('services')"
            class="flex items-center justify-between w-full px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider hover:text-slate-200 transition-colors"
          >
            <div class="flex items-center gap-2">
              <Briefcase class="w-3 h-3" />
              <span>Hizmetler</span>
            </div>
            <ChevronDown 
              class="w-3 h-3 transition-transform duration-200" 
              :class="{ 'rotate-180': expandedGroups.services }"
            />
          </button>
          <div v-show="expandedGroups.services" class="space-y-1 mt-1">
            <router-link 
              v-for="item in serviceMenu"
              :key="item.path"
              :to="item.path"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all group relative"
              :class="isActive(item.path) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
            >
              <component :is="item.icon" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-slate-400 group-hover:text-white'" />
              <span class="flex-1 font-medium">{{ item.name }}</span>
              <button 
                @click.prevent.stop="toggleFavorite(item)"
                class="opacity-0 group-hover:opacity-100 text-slate-500 hover:text-yellow-400 transition-all absolute right-2"
                :class="{ 'opacity-100 text-yellow-400': isFavorite(item) }"
              >
                <Star class="w-4 h-4" :class="{ 'fill-current': isFavorite(item) }" />
              </button>
            </router-link>
          </div>
        </div>

        <!-- Content -->
        <div class="menu-group">
          <button 
            @click="toggleGroup('content')"
            class="flex items-center justify-between w-full px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider hover:text-slate-200 transition-colors"
          >
            <div class="flex items-center gap-2">
              <FileText class="w-3 h-3" />
              <span>İçerik</span>
            </div>
            <ChevronDown 
              class="w-3 h-3 transition-transform duration-200" 
              :class="{ 'rotate-180': expandedGroups.content }"
            />
          </button>
          <div v-show="expandedGroups.content" class="space-y-1 mt-1">
            <router-link 
              v-for="item in contentMenu"
              :key="item.path"
              :to="item.path"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all group relative"
              :class="isActive(item.path) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
            >
              <component :is="item.icon" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-slate-400 group-hover:text-white'" />
              <span class="flex-1 font-medium">{{ item.name }}</span>
              <button 
                @click.prevent.stop="toggleFavorite(item)"
                class="opacity-0 group-hover:opacity-100 text-slate-500 hover:text-yellow-400 transition-all absolute right-2"
                :class="{ 'opacity-100 text-yellow-400': isFavorite(item) }"
              >
                <Star class="w-4 h-4" :class="{ 'fill-current': isFavorite(item) }" />
              </button>
            </router-link>
          </div>
        </div>

        <!-- System -->
        <div class="menu-group">
          <button 
            @click="toggleGroup('system')"
            class="flex items-center justify-between w-full px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider hover:text-slate-200 transition-colors"
          >
            <div class="flex items-center gap-2">
              <Settings class="w-3 h-3" />
              <span>Sistem</span>
            </div>
            <ChevronDown 
              class="w-3 h-3 transition-transform duration-200" 
              :class="{ 'rotate-180': expandedGroups.system }"
            />
          </button>
          <div v-show="expandedGroups.system" class="space-y-1 mt-1">
            <router-link 
              v-for="item in systemMenu"
              :key="item.path"
              :to="item.path"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all group relative"
              :class="isActive(item.path) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
            >
              <component :is="item.icon" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-slate-400 group-hover:text-white'" />
              <span class="flex-1 font-medium">{{ item.name }}</span>
              <button 
                @click.prevent.stop="toggleFavorite(item)"
                class="opacity-0 group-hover:opacity-100 text-slate-500 hover:text-yellow-400 transition-all absolute right-2"
                :class="{ 'opacity-100 text-yellow-400': isFavorite(item) }"
              >
                <Star class="w-4 h-4" :class="{ 'fill-current': isFavorite(item) }" />
              </button>
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- User Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-800 bg-slate-900">
      <div class="flex items-center gap-3 group cursor-pointer hover:bg-slate-800 p-2 rounded-lg transition-colors">
        <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center font-bold shadow-lg shadow-emerald-900/20">
          {{ userInitials }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium truncate text-white group-hover:text-indigo-300 transition-colors">{{ userName }}</p>
          <p class="text-xs text-slate-400">Administrator</p>
        </div>
        <button @click="$emit('logout')" class="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-700 rounded-lg transition-all" title="Çıkış">
          <LogOut class="w-5 h-5" />
        </button>
      </div>
    </div>
  </aside>

  <!-- Mobile Overlay -->
  <div 
    v-if="isOpen" 
    @click="$emit('close')" 
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden transition-opacity"
  ></div>
</template>

<script setup lang="ts">
import { ref, computed, reactive, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { 
  LayoutDashboard, 
  Globe, 
  ShoppingCart, 
  Package, 
  Store, 
  FileText, 
  Users, 
  Wallet, 
  Utensils, 
  Briefcase, 
  FileEdit, 
  Layout, 
  Image, 
  List, 
  Megaphone, 
  UserCog, 
  Settings, 
  Bell, 
  BarChart, 
  LogOut, 
  Search, 
  Star, 
  ChevronDown,
  Hotel,
  Bus,
  Car,
  Shield,
  Map,
  Ticket,
  RotateCcw,
  TrendingUp
} from 'lucide-vue-next'

interface MenuItem {
  name: string
  path: string
  icon: any
  badge?: string
  badgeClass?: string
  category?: string
}

const props = defineProps<{
  isOpen: boolean
  userName?: string
}>()

defineEmits(['close', 'logout'])

const route = useRoute()
const searchQuery = ref('')
const favorites = ref<MenuItem[]>([])

const expandedGroups = reactive({
  main: true,
  services: false,
  content: false,
  system: false
})

const userInitials = computed(() => {
  if (!props.userName) return 'A'
  return props.userName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const isActive = (path: string) => {
  return route.path === path || route.path.startsWith(path + '/')
}

const toggleGroup = (group: keyof typeof expandedGroups) => {
  expandedGroups[group] = !expandedGroups[group]
}

const mainMenu: MenuItem[] = [
  { name: 'Dashboard', path: '/admin/dashboard', icon: LayoutDashboard, category: 'Ana Menü' },
  { name: 'Pazaryeri', path: '/admin/marketplace', icon: Globe, category: 'Ana Menü' },
  { name: 'Siparişler', path: '/admin/orders', icon: ShoppingCart, badge: '12', badgeClass: 'bg-orange-500 text-white', category: 'Ana Menü' },
  { name: 'Ürünler', path: '/admin/products', icon: Package, category: 'Ana Menü' },
  { name: 'Satıcılar', path: '/admin/sellers', icon: Store, category: 'Ana Menü' },
  { name: 'Başvurular', path: '/admin/seller-applications', icon: FileText, badge: '3', badgeClass: 'bg-blue-500 text-white', category: 'Ana Menü' },
  { name: 'Müşteriler', path: '/admin/customers', icon: Users, category: 'Ana Menü' },
  { name: 'Finans', path: '/admin/finance', icon: Wallet, category: 'Ana Menü' },
  { name: 'Komisyonlar', path: '/admin/commissions', icon: TrendingUp, category: 'Ana Menü' },
]

const serviceMenu: MenuItem[] = [
  { name: 'Restoranlar', path: '/admin/restaurants', icon: Utensils, category: 'Hizmetler' },
  { name: 'Oteller', path: '/admin/hotels', icon: Hotel, category: 'Hizmetler' },
  { name: 'Ulaşım', path: '/admin/transport', icon: Bus, category: 'Hizmetler' },
  { name: 'Araç Kiralama', path: '/admin/car-rental', icon: Car, category: 'Hizmetler' },
  { name: 'Sigorta', path: '/admin/insurance', icon: Shield, category: 'Hizmetler' },
  { name: 'Turlar', path: '/admin/tours', icon: Map, category: 'Hizmetler' },
  { name: 'Etkinlikler', path: '/admin/activities', icon: Ticket, category: 'Hizmetler' },
]

const contentMenu: MenuItem[] = [
  { name: 'Kampanyalar', path: '/admin/campaigns', icon: Megaphone, category: 'İçerik' },
  { name: 'Blog', path: '/admin/blog', icon: FileEdit, category: 'İçerik' },
  { name: 'Kategoriler', path: '/admin/categories', icon: List, category: 'İçerik' },
  { name: 'Bannerlar', path: '/admin/banners', icon: Image, category: 'İçerik' },
  { name: 'Sayfalar', path: '/admin/pages', icon: Layout, category: 'İçerik' },
]

const systemMenu: MenuItem[] = [
  { name: 'Ayarlar', path: '/admin/settings', icon: Settings, category: 'Sistem' },
  { name: 'Kullanıcılar', path: '/admin/users', icon: UserCog, category: 'Sistem' },
  { name: 'İadeler', path: '/admin/returns', icon: RotateCcw, category: 'Sistem' },
  { name: 'Raporlar', path: '/admin/reports', icon: BarChart, category: 'Sistem' },
  { name: 'Bildirimler', path: '/admin/notifications', icon: Bell, category: 'Sistem' },
]

const allMenuItems = computed(() => [
  ...mainMenu,
  ...serviceMenu,
  ...contentMenu,
  ...systemMenu
])

const filteredItems = computed(() => {
  if (!searchQuery.value) return []
  const query = searchQuery.value.toLowerCase()
  return allMenuItems.value.filter(item => 
    item.name.toLowerCase().includes(query) ||
    item.category?.toLowerCase().includes(query)
  )
})

const isFavorite = (item: MenuItem) => {
  return favorites.value.some(f => f.path === item.path)
}

const toggleFavorite = (item: MenuItem) => {
  const index = favorites.value.findIndex(f => f.path === item.path)
  if (index > -1) {
    favorites.value.splice(index, 1)
  } else {
    favorites.value.push(item)
  }
  localStorage.setItem('admin_favorites', JSON.stringify(favorites.value))
}

const focusSearch = () => {
  document.querySelector<HTMLInputElement>('input[placeholder*="Menüde ara"]')?.focus()
}

// Global Ctrl+K handler
const handleKeydown = (e: KeyboardEvent) => {
  if (e.ctrlKey && e.key === 'k') {
    e.preventDefault()
    focusSearch()
  }
}

onMounted(() => {
  const saved = localStorage.getItem('admin_favorites')
  if (saved) {
    try {
      const savedPaths = JSON.parse(saved).map((i: any) => i.path)
      favorites.value = allMenuItems.value.filter(item => savedPaths.includes(item.path))
    } catch {}
  }
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
  border-radius: 2px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #475569;
}
</style>
