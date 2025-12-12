<template>
  <aside 
    class="fixed left-0 top-0 z-40 h-screen bg-slate-900 text-white transition-all duration-300"
    :class="[
      isCollapsed ? 'w-16' : 'w-64',
      isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
  >
    <!-- Logo -->
    <div class="flex items-center gap-3 px-4 py-4 border-b border-slate-700" :class="{ 'justify-center px-2': isCollapsed }">
      <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg shadow-indigo-500/20 flex-shrink-0">
        S
      </div>
      <div v-if="!isCollapsed">
        <p class="font-bold text-lg tracking-tight">SportoOnline</p>
        <p class="text-xs text-slate-400 font-medium">Admin Panel</p>
      </div>
    </div>

    <!-- Search -->
    <div v-if="!isCollapsed" class="px-4 py-3 border-b border-slate-800">
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
    <div v-else class="px-2 py-3 border-b border-slate-800 flex justify-center">
      <Search class="w-5 h-5 text-slate-400 cursor-pointer hover:text-indigo-400 transition-colors" @click="$emit('toggle-collapse')" />
    </div>

    <!-- Navigation -->
    <nav class="px-2 py-4 overflow-y-auto h-[calc(100vh-200px)] custom-scrollbar" :class="{ 'px-1': isCollapsed }">
      <!-- Favorites -->
      <div v-if="!searchQuery && favorites.length && !isCollapsed" class="mb-6">
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
      <div v-if="searchQuery && !isCollapsed" class="space-y-1">
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
          <div v-if="isCollapsed" class="h-px bg-slate-700 my-2 mx-2"></div>
          
          <div v-show="isCollapsed || expandedGroups.main" class="space-y-0.5 mt-1">
            <router-link 
              v-for="item in mainMenu"
              :key="item.path"
              :to="item.path"
              :title="isCollapsed ? item.name : ''"
              class="flex items-center gap-3 rounded-lg text-sm transition-all group relative"
              :class="[
                isActive(item.path) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
                isCollapsed ? 'px-2 py-2.5 justify-center' : 'px-3 py-2'
              ]"
            >
              <component :is="item.icon" class="w-5 h-5 flex-shrink-0" :class="isActive(item.path) ? 'text-white' : 'text-slate-400 group-hover:text-white'" />
              <span v-if="!isCollapsed" class="flex-1 font-medium">{{ item.name }}</span>
              <span v-if="!isCollapsed && item.badge" class="px-1.5 py-0.5 text-[10px] font-bold rounded-full shadow-sm flex-shrink-0" :class="item.badgeClass">{{ item.badge }}</span>
              
              <!-- Badge indicator for collapsed mode -->
              <span v-if="isCollapsed && item.badge" class="absolute -top-1 -right-1 w-2 h-2 bg-orange-500 rounded-full ring-2 ring-slate-900"></span>
              
              <!-- Tooltip -->
              <div 
                v-if="isCollapsed"
                class="absolute left-full ml-2 px-3 py-2 bg-slate-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-lg"
              >
                {{ item.name }}
                <span v-if="item.badge" class="ml-2 px-2 py-0.5 text-xs rounded-full" :class="item.badgeClass">{{ item.badge }}</span>
              </div>
              
              <button 
                v-if="!isCollapsed"
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
              v-for="item in serviceMenu.filter(i => !i.flag || isFeatureEnabled(i.flag as any))"
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

        <!-- E-Commerce Operations -->
        <div class="menu-group">
          <button 
            @click="toggleGroup('commerce')"
            class="flex items-center justify-between w-full px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider hover:text-slate-200 transition-colors"
          >
            <div class="flex items-center gap-2">
              <ShoppingCart class="w-3 h-3" />
              <span>E-Ticaret</span>
            </div>
            <ChevronDown 
              class="w-3 h-3 transition-transform duration-200" 
              :class="{ 'rotate-180': expandedGroups.commerce }"
            />
          </button>
          <div v-show="expandedGroups.commerce" class="space-y-1 mt-1">
            <router-link 
              v-for="item in commerceMenu"
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

        <!-- User Management -->
        <div class="menu-group">
          <button 
            @click="toggleGroup('users')"
            class="flex items-center justify-between w-full px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider hover:text-slate-200 transition-colors"
          >
            <div class="flex items-center gap-2">
              <Users class="w-3 h-3" />
              <span>Kullanıcılar</span>
            </div>
            <ChevronDown 
              class="w-3 h-3 transition-transform duration-200" 
              :class="{ 'rotate-180': expandedGroups.users }"
            />
          </button>
          <div v-show="expandedGroups.users" class="space-y-1 mt-1">
            <router-link 
              v-for="item in userManagementMenu"
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

        <!-- Marketing & Content -->
        <div class="menu-group">
          <button 
            @click="toggleGroup('marketing')"
            class="flex items-center justify-between w-full px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider hover:text-slate-200 transition-colors"
          >
            <div class="flex items-center gap-2">
              <Target class="w-3 h-3" />
              <span>Pazarlama</span>
            </div>
            <ChevronDown 
              class="w-3 h-3 transition-transform duration-200" 
              :class="{ 'rotate-180': expandedGroups.marketing }"
            />
          </button>
          <div v-show="expandedGroups.marketing" class="space-y-1 mt-1">
            <router-link 
              v-for="item in marketingMenu"
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

        <!-- Finance & Reporting -->
        <div class="menu-group">
          <button 
            @click="toggleGroup('finance')"
            class="flex items-center justify-between w-full px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider hover:text-slate-200 transition-colors"
          >
            <div class="flex items-center gap-2">
              <DollarSign class="w-3 h-3" />
              <span>Finans & Raporlar</span>
            </div>
            <ChevronDown 
              class="w-3 h-3 transition-transform duration-200" 
              :class="{ 'rotate-180': expandedGroups.finance }"
            />
          </button>
          <div v-show="expandedGroups.finance" class="space-y-1 mt-1">
            <router-link 
              v-for="item in financeMenu"
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
  RotateCcw,
  TrendingUp,
  Ticket,
  DollarSign,
  Target,
  Shield,
  Boxes,
  Tags,
  MessageSquare,
  FlaskConical
} from 'lucide-vue-next'
import { isFeatureEnabled } from '@/utils/featureToggle'

interface MenuItem {
  name: string
  path: string
  icon: any
  badge?: string
  badgeClass?: string
  category?: string
}

const props = defineProps<{
  isOpen?: boolean
  isCollapsed: boolean
  isMobileOpen: boolean
  userName?: string
}>()

defineEmits(['close', 'logout', 'toggle-collapse'])

const route = useRoute()
const searchQuery = ref('')
const favorites = ref<MenuItem[]>([])

const expandedGroups = reactive({
  main: true,
  commerce: true,
  users: false,
  services: false,
  marketing: false,
  finance: false,
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

// Ana Dashboard ve Genel Yönetim
const mainMenu: MenuItem[] = [
  { name: 'Dashboard', path: '/admin/dashboard', icon: LayoutDashboard, category: 'Ana Menü' },
  { name: 'Pazaryeri', path: '/admin/marketplace', icon: Globe, category: 'Ana Menü' },
]

// E-Ticaret Operasyonları
const commerceMenu: MenuItem[] = [
  { name: 'Siparişler', path: '/admin/orders', icon: ShoppingCart, badge: '12', badgeClass: 'bg-orange-500 text-white', category: 'E-Ticaret' },
  { name: 'Ürünler', path: '/admin/products', icon: Package, category: 'E-Ticaret' },
  { name: 'Kategoriler', path: '/admin/categories', icon: List, category: 'E-Ticaret' },
  { name: 'Kategori & Özellikler', path: '/admin/category-attributes', icon: Boxes, badge: 'YENİ', badgeClass: 'bg-green-500 text-white', category: 'E-Ticaret' },
  { name: 'İadeler', path: '/admin/returns', icon: RotateCcw, category: 'E-Ticaret' },
]

// Satıcı ve Müşteri Yönetimi
const userManagementMenu: MenuItem[] = [
  { name: 'Satıcılar', path: '/admin/sellers', icon: Store, category: 'Kullanıcılar' },
  { name: 'Satıcı Başvuruları', path: '/admin/seller-applications', icon: FileText, badge: '3', badgeClass: 'bg-blue-500 text-white', category: 'Kullanıcılar' },
  { name: 'Müşteriler', path: '/admin/customers', icon: Users, category: 'Kullanıcılar' },
  { name: 'Admin Kullanıcıları', path: '/admin/users', icon: UserCog, category: 'Kullanıcılar' },
]

// Hizmet Modülleri (feature flag’lerle kontrol)
const serviceMenu: Array<MenuItem & { flag?: string }> = [
  { name: 'Restoranlar', path: '/admin/restaurants', icon: Utensils, category: 'Hizmetler', flag: 'admin.services.restaurants' },
  { name: 'Yemek Siparişleri', path: '/admin/food-orders', icon: Utensils, category: 'Hizmetler', flag: 'admin.services.foodOrders' },
  { name: 'Oteller', path: '/admin/hotels', icon: Hotel, category: 'Hizmetler', flag: 'admin.services.hotels' },
  { name: 'Rezervasyonlar', path: '/admin/reservations', icon: Hotel, category: 'Hizmetler', flag: 'admin.services.reservations' },
]

// Pazarlama ve İçerik
const marketingMenu: MenuItem[] = [
  { name: 'Kampanyalar', path: '/admin/campaigns', icon: Megaphone, category: 'Pazarlama' },
  { name: 'Kampanya & Kuponlar', path: '/admin/campaign-coupon', icon: Tags, badge: 'YENİ', badgeClass: 'bg-green-500 text-white', category: 'Pazarlama' },
  { name: 'Kuponlar', path: '/admin/coupons', icon: Ticket, category: 'Pazarlama' },
  { name: 'Bannerlar', path: '/admin/banners', icon: Image, category: 'Pazarlama' },
  { name: 'Blog', path: '/admin/blog', icon: FileEdit, category: 'Pazarlama' },
  { name: 'Blog Kategorileri', path: '/admin/blog-categories', icon: List, category: 'Pazarlama' },
  { name: 'Sayfalar', path: '/admin/pages', icon: Layout, category: 'Pazarlama' },
  { name: 'Temalar', path: '/admin/themes', icon: Layout, category: 'Pazarlama' },
]

// Finans ve Raporlama
const financeMenu: MenuItem[] = [
  { name: 'Finans Özeti', path: '/admin/finance', icon: Wallet, category: 'Finans' },
  { name: 'Komisyonlar', path: '/admin/commissions', icon: TrendingUp, category: 'Finans' },
  { name: 'Raporlar', path: '/admin/reports', icon: BarChart, category: 'Finans' },
  { name: 'Segment Export', path: '/admin/segment-export', icon: FileText, category: 'Finans' },
  { name: 'Export Dosyaları', path: '/admin/export-files', icon: FileText, category: 'Finans' },
]

// Sistem ve Ayarlar
const systemMenu: MenuItem[] = [
  { name: 'Sistem Ayarları', path: '/admin/settings', icon: Settings, category: 'Sistem' },
  { name: 'Sistem Ayarları (Gelişmiş)', path: '/admin/system-settings-enhanced', icon: Settings, badge: 'YENİ', badgeClass: 'bg-green-500 text-white', category: 'Sistem' },
  { name: 'Moderasyon Merkezi', path: '/admin/moderation', icon: MessageSquare, badge: '5', badgeClass: 'bg-red-500 text-white', category: 'Sistem' },
  { name: 'Bildirimler', path: '/admin/notifications', icon: Bell, category: 'Sistem' },
  { name: 'E2E Test Runner', path: '/test/e2e', icon: FlaskConical, badge: 'TEST', badgeClass: 'bg-purple-500 text-white', category: 'Sistem' },
]

const allMenuItems = computed(() => [
  ...mainMenu,
  ...commerceMenu,
  ...userManagementMenu,
  ...serviceMenu,
  ...marketingMenu,
  ...financeMenu,
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
