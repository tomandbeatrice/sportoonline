<template>
  <header class="sticky top-0 z-50">
    <div :class="[theme.gradient, 'text-white transition-colors duration-500']">
      <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-2 text-xs font-semibold">
        <div class="flex items-center gap-2">
          <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-[0.65rem]">24H</span>
          Express teslimat şehirlerinde 2 saat içinde kapında
        </div>
        <div class="flex items-center gap-3">
          <span class="hidden sm:flex items-center gap-1 text-white/80">
            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
            {{ theme.name }} Modu Aktif
          </span>
          <router-link to="/campaigns" class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-[0.7rem]">
            Kampanyalar
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7 7 7-7 7" />
            </svg>
          </router-link>
        </div>
      </div>
    </div>

    <div class="relative overflow-hidden backdrop-blur-xl bg-white/80 shadow-[0_25px_80px_-45px_rgba(15,23,42,0.75)]">
      <div class="pointer-events-none absolute inset-0">
        <div :class="['absolute -left-12 top-0 h-32 w-32 rounded-full blur-3xl opacity-15 transition-colors duration-500', `bg-${theme.primary}-500`]"></div>
        <div :class="['absolute -right-10 top-6 h-40 w-40 rounded-full blur-3xl opacity-10 transition-colors duration-500', `bg-${theme.secondary}-500`]"></div>
        <div class="absolute inset-x-10 bottom-0 h-12 rounded-full bg-white/40 blur-2xl"></div>
      </div>
      <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-4">
        <div class="flex items-center justify-between gap-4">
          <router-link to="/" class="flex flex-col items-center leading-none select-none group hover:scale-105 transition-transform duration-300">
            <span :class="['text-[0.7rem] font-bold tracking-wider mb-0.5 transition-colors duration-300', theme.text]">sportoonline</span>
            <div class="flex items-baseline text-5xl font-black tracking-tighter filter drop-shadow-sm -my-1">
              <span class="text-orange-500">M</span>
              <span class="text-red-500">u</span>
              <span class="text-fuchsia-600">l</span>
              <span class="text-purple-600">t</span>
              <span class="text-indigo-600">i</span>
              <span class="text-blue-500">P</span>
              <span class="text-sky-500">r</span>
              <span class="text-teal-500">i</span>
              <span class="text-emerald-500">c</span>
              <span class="text-amber-500">e</span>
            </div>
            <span class="text-xs font-bold text-slate-800 tracking-[0.35em] mt-1.5 group-hover:text-slate-900 transition-colors">Lifestyle Hub</span>
          </router-link>

          <div class="hidden flex-1 lg:flex">
            <div class="relative w-full" id="tour-navbar-search">
              <span class="absolute inset-y-0 left-4 flex items-center text-slate-300">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
              </span>
              <input
                v-model="searchQuery"
                type="search"
                :placeholder="`${theme.name} içinde ara...`"
                :class="['w-full rounded-2xl border bg-white/90 px-12 py-2.5 text-sm text-slate-700 shadow-inner shadow-slate-200 focus:outline-none transition-colors duration-300', theme.border, `focus:border-${theme.primary}-500`]"
                @keyup.enter="handleSearch"
              />
              <div class="absolute inset-y-0 right-2 flex items-center gap-1">
                <button
                  :class="['rounded-2xl px-4 py-1.5 text-sm font-semibold text-white shadow-lg transition-all duration-300', theme.gradient, `shadow-${theme.primary}-500/30`]"
                  @click="handleSearch"
                >
                  Ara
                </button>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <PointsBadge class="hidden md:flex" />
            <router-link
              to="/cart"
              id="tour-cart-btn"
              :class="['relative inline-flex items-center gap-2 rounded-2xl border bg-white/90 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:-translate-y-0.5 transition-all duration-300', theme.border, `hover:text-${theme.primary}-700`, `shadow-${theme.primary}-100`]"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 6h13" />
              </svg>
              Sepet
              <span v-if="cartCount > 0" :class="['absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full text-xs text-white transition-colors duration-300', `bg-${theme.primary}-600`]">
                {{ cartCount }}
              </span>
            </router-link>

            <div v-if="isAuthenticated" class="relative">
              <button
                @click="toggleUserMenu"
                :class="['inline-flex items-center gap-2 rounded-2xl border bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition-colors duration-300', theme.border]"
              >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM5 21a7 7 0 0114 0" />
                </svg>
                <span class="hidden sm:inline">{{ user?.name }}</span>
                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                </svg>
              </button>

              <div
                v-if="showUserMenu"
                class="absolute right-0 mt-3 w-56 rounded-2xl border border-slate-100 bg-white/95 p-3 shadow-2xl"
              >
                <p class="mb-2 text-xs uppercase tracking-[0.35em] text-slate-400">Hesap</p>
                <router-link
                  v-if="user?.role === 'buyer'"
                  to="/buyer/dashboard"
                  class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                  @click="closeDropdown"
                >
                  Alıcı paneli
                </router-link>
                <router-link
                  v-if="user?.role === 'seller'"
                  to="/seller/dashboard"
                  class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                  @click="closeDropdown"
                >
                  Satıcı paneli
                </router-link>
                <router-link
                  v-if="user?.role === 'admin'"
                  to="/admin/dashboard"
                  class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                  @click="closeDropdown"
                >
                  Admin cockpit
                </router-link>
                <button
                  class="mt-2 w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-rose-500 hover:bg-rose-50"
                  @click="handleLogout"
                >
                  Çıkış yap
                </button>
              </div>
            </div>

            <div v-else class="flex items-center gap-2">
              <router-link to="/login" class="nav-link-pill">Giriş</router-link>
              <router-link
                to="/register"
                :class="['rounded-2xl px-4 py-2 text-sm font-semibold text-white shadow-lg transition-all duration-300', theme.gradient, `shadow-${theme.primary}-500/30`]"
              >
                Ücretsiz üye ol
              </router-link>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-sm">
          <router-link
            v-for="item in primaryLinks"
            :key="item.to"
            class="nav-link-pill"
            :style="item.glow"
            :to="item.to"
          >
            <span class="flex items-center gap-1">
              <BadgeIcon v-if="item.icon" :name="item.icon" cls="w-4 h-4 inline-block" aria-hidden="true" />
              {{ item.label }}
            </span>
            <span v-if="item.badge" class="rounded-full px-2 text-[0.65rem] font-semibold" :class="item.badgeClass">
              {{ item.badge }}
            </span>
          </router-link>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cartStore'
import { useTheme } from '@/composables/useTheme'
import PointsBadge from '@/components/gamification/PointsBadge.vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()
const { theme } = useTheme()
const searchQuery = ref('')
const isUserMenuOpen = ref(false)
const isMobileMenuOpen = ref(false)

const isAuthenticated = computed(() => authStore.isAuthenticated)
const user = computed(() => authStore.user)
const cartCount = computed(() => cartStore.totalItems)

const primaryLinks = [
  { label: 'Market', to: '/market', icon: 'shopping-bag' },
  { label: 'Yemek', to: '/food', icon: 'utensils' },
  { label: 'Ulaşım', to: '/transport', icon: 'car' },
  { label: 'Otel', to: '/hotel', icon: 'hotel' },
  { label: 'Kariyer', to: '/career', icon: 'briefcase' },
]

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'Search', query: { q: searchQuery.value } })
  }
}

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value
}

const closeDropdown = () => {
  isUserMenuOpen.value = false
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

// Close menus when clicking outside
const closeMenus = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('.relative')) {
    isUserMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeMenus)
})

onUnmounted(() => {
  document.removeEventListener('click', closeMenus)
})
</script>
