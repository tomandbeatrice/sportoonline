<template>
  <aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-white border-r border-slate-200 transition-transform" :class="{ '-translate-x-full': !isOpen }">
    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-100">
      <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center font-bold text-white text-lg shadow-lg shadow-orange-200">
        S
      </div>
      <div>
        <p class="font-bold text-slate-900">Satıcı Paneli</p>
        <p class="text-xs text-slate-500">SportoOnline</p>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="px-4 py-6 space-y-1 overflow-y-auto h-[calc(100vh-180px)]">
      <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider px-3 mb-3">
        Mağaza Yönetimi
      </div>
      
      <router-link 
        v-for="item in menuItems"
        :key="item.path"
        :to="item.path"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all group"
        :class="isActive(item.path) 
          ? 'bg-orange-50 text-orange-700 font-medium' 
          : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
      >
        <span class="text-lg transition-transform group-hover:scale-110">{{ item.icon }}</span>
        <span>{{ item.name }}</span>
        <span v-if="item.badge" class="ml-auto px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 font-bold">
          {{ item.badge }}
        </span>
      </router-link>

      <button 
        @click="$emit('logout')"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all group text-slate-600 hover:bg-red-50 hover:text-red-700"
      >
        <span class="text-lg transition-transform group-hover:scale-110">🚪</span>
        <span>Çıkış Yap</span>
      </button>

      <div class="mt-8 p-4 bg-slate-50 rounded-2xl border border-slate-100">
        <p class="text-xs font-semibold text-slate-900 mb-2">Mağaza Puanı</p>
        <div class="flex items-center gap-2 mb-2">
          <div class="flex text-yellow-400 text-sm">
            ★★★★☆
          </div>
          <span class="text-xs text-slate-600 font-medium">4.8/5.0</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-1.5">
          <div class="bg-green-500 h-1.5 rounded-full" style="width: 92%"></div>
        </div>
        <p class="text-[10px] text-slate-400 mt-2">Süper Satıcı olmaya %8 kaldı</p>
      </div>
    </nav>

    <!-- User Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-100 bg-white">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 font-bold border border-slate-200">
          {{ userInitials }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-slate-900 truncate">{{ userName }}</p>
          <p class="text-xs text-slate-500 truncate">Mağaza Sahibi</p>
        </div>
        <button @click="$emit('logout')" class="p-2 hover:bg-slate-50 rounded-lg transition text-slate-400 hover:text-red-600" title="Çıkış">
          🚪
        </button>
      </div>
    </div>
  </aside>

  <!-- Mobile Overlay -->
  <div 
    v-if="isOpen" 
    @click="$emit('close')" 
    class="fixed inset-0 bg-black/20 backdrop-blur-sm z-30 lg:hidden"
  ></div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps<{
  isOpen: boolean
  userName?: string
}>()

defineEmits(['close', 'logout'])

const route = useRoute()

const userInitials = computed(() => {
  if (!props.userName) return 'S'
  return props.userName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const isActive = (path: string) => {
  return route.path === path || route.path.startsWith(path + '/')
}

const menuItems = [
  { name: 'Özet', path: '/seller/dashboard', icon: '📊' },
  { name: 'Siparişler', path: '/seller/orders', icon: '📦', badge: '5' },
  { name: 'İade Talepleri', path: '/seller/returns', icon: '🔄' },
  { name: 'Ürünlerim', path: '/seller/products', icon: '🏷️' },
  { name: 'Kampanyalar', path: '/seller/campaigns', icon: '📢' },
  { name: 'Cüzdanım & Finans', path: '/seller/financial-report', icon: '💰' },
  { name: 'Mağaza Ayarları', path: '/seller/onboarding', icon: '⚙️' },
]
</script>
