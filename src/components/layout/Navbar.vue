<template>
  <header class="sticky top-0 z-50">
    <div class="bg-market-gradient text-white">
      <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-2 text-xs font-semibold">
        <div class="flex items-center gap-2">
          <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-[0.65rem]">24H</span>
          Express teslimat şehirlerinde 2 saat içinde kapında
        </div>
        <div class="flex items-center gap-3">
          <span class="hidden sm:flex items-center gap-1 text-white/80">
            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
            Satıcı paneli v3 yayında
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
        <div class="absolute -left-12 top-0 h-32 w-32 rounded-full bg-blue-500/15 blur-3xl"></div>
        <div class="absolute -right-10 top-6 h-40 w-40 rounded-full bg-fuchsia-500/10 blur-3xl"></div>
        <div class="absolute inset-x-10 bottom-0 h-12 rounded-full bg-white/40 blur-2xl"></div>
      </div>
      <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-4">
        <div class="flex items-center justify-between gap-4">
          <router-link to="/" class="flex items-center gap-3">
            <span class="rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-fuchsia-500 p-2 text-white shadow-lg">
              <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4v6c0 5-8 8-8 8s-8-3-8-8V7l8-4z" />
              </svg>
            </span>
            <div>
              <p class="text-lg font-black tracking-tight text-slate-900">sportoonline</p>
              <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Marketplace</p>
            </div>
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
                placeholder="Ürün, satıcı veya marka ara"
                class="w-full rounded-2xl border border-slate-100 bg-white/90 px-12 py-2.5 text-sm text-slate-700 shadow-inner shadow-slate-200 focus:border-blue-500 focus:outline-none"
                @keyup.enter="handleSearch"
              />
              <div class="absolute inset-y-0 right-2 flex items-center gap-1">
                <button
                  class="rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-1.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/30"
                  @click="handleSearch"
                >
                  Ara
                </button>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <router-link
              to="/cart"
              id="tour-cart-btn"
              class="relative inline-flex items-center gap-2 rounded-2xl border border-blue-100 bg-white/90 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm shadow-blue-100 hover:-translate-y-0.5 hover:border-blue-300 hover:text-blue-700"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 6h13" />
              </svg>
              Sepet
              <span v-if="cartCount > 0" class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-xs text-white">
                {{ cartCount }}
              </span>
            </router-link>

            <div v-if="isAuthenticated" class="relative">
              <button
                @click="toggleUserMenu"
                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-blue-200"
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
                class="rounded-2xl bg-gradient-to-r from-fuchsia-600 via-pink-500 to-orange-400 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-fuchsia-500/30"
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
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useCartStore } from '../../stores/cartStore'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const router = useRouter()
const { user, isAuthenticated, logout } = useAuth()
const cartStore = useCartStore()

const showUserMenu = ref(false)
const searchQuery = ref('')

const cartCount = computed(() => cartStore.totalItems)

const primaryLinks = [
  {
    label: 'Çok satanlar',
    to: '/products',
    badge: 'Yeni',
    badgeClass: 'bg-blue-600/10 text-blue-600',
    icon: 'medal',
    glow: 'box-shadow: 0 10px 25px -18px rgba(37,99,235,0.9)'
  },
  {
    label: 'Satıcı başvurusu',
    to: '/apply-seller',
    badge: 'Pro',
    badgeClass: 'bg-emerald-500/10 text-emerald-600',
    icon: 'clipboard',
    glow: 'box-shadow: 0 10px 25px -18px rgba(16,185,129,0.8)'
  },
  {
    label: 'Kampanya stüdyosu',
    to: '/campaigns',
    badge: 'Canlı',
    badgeClass: 'bg-fuchsia-500/10 text-fuchsia-600',
    icon: 'target',
    glow: 'box-shadow: 0 10px 25px -18px rgba(192,38,211,0.55)'
  },
  {
    label: 'Sipariş takibi',
    to: '/orders',
    badge: 'Beta',
    badgeClass: 'bg-amber-400/15 text-amber-600',
    icon: 'box',
    glow: 'box-shadow: 0 10px 25px -18px rgba(245,158,11,0.6)'
  }
]

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const closeDropdown = () => {
  showUserMenu.value = false
}

const handleLogout = () => {
  logout()
  closeDropdown()
  router.push('/login')
}

const handleSearch = () => {
  if (!searchQuery.value.trim()) return
  router.push({ path: '/', query: { q: searchQuery.value.trim() } })
}

let clickHandler: ((event: MouseEvent) => void) | null = null

onMounted(() => {
  clickHandler = (event: MouseEvent) => {
    const target = event.target as HTMLElement | null
    if (showUserMenu.value && target && !target.closest('.relative')) {
      showUserMenu.value = false
    }
  }
  window.addEventListener('click', clickHandler)
})

onBeforeUnmount(() => {
  if (clickHandler) {
    window.removeEventListener('click', clickHandler)
  }
})
</script>
