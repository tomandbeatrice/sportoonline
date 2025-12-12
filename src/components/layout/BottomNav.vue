<template>
  <div class="fixed bottom-0 left-0 z-50 w-full border-t border-slate-200 bg-white/95 pb-safe backdrop-blur-lg md:hidden">
    <div class="flex h-16 items-center justify-around px-2">
      <!-- Home -->
      <router-link 
        to="/" 
        class="flex flex-col items-center justify-center gap-1 p-2 text-xs font-medium transition-colors"
        :class="isActive('/') ? theme.text : 'text-slate-400'"
      >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span>Ana Sayfa</span>
      </router-link>

      <!-- Explore -->
      <router-link 
        to="/market" 
        class="flex flex-col items-center justify-center gap-1 p-2 text-xs font-medium transition-colors"
        :class="isActive('/market') ? theme.text : 'text-slate-400'"
      >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <span>Keşfet</span>
      </router-link>

      <!-- SUPER BUTTON -->
      <div class="relative -top-5">
        <button 
          @click="handleSuperAction"
          class="flex h-14 w-14 items-center justify-center rounded-full text-white shadow-lg transition-transform active:scale-95"
          :class="[theme.gradient, `shadow-${theme.primary}-500/40`]"
        >
          <svg v-if="currentModule === 'food'" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          <svg v-else class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </button>
      </div>

      <!-- Cart -->
      <router-link 
        to="/cart" 
        class="relative flex flex-col items-center justify-center gap-1 p-2 text-xs font-medium transition-colors"
        :class="isActive('/cart') ? theme.text : 'text-slate-400'"
      >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <span>Sepet</span>
        <span v-if="cartCount > 0" class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] text-white">
          {{ cartCount }}
        </span>
      </router-link>

      <!-- Profile -->
      <router-link 
        to="/profile" 
        class="flex flex-col items-center justify-center gap-1 p-2 text-xs font-medium transition-colors"
        :class="isActive('/profile') ? theme.text : 'text-slate-400'"
      >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span>Hesabım</span>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTheme } from '@/composables/useTheme'
import { useCartStore } from '../../stores/cartStore'

const route = useRoute()
const router = useRouter()
const { theme, currentModule } = useTheme()
const cartStore = useCartStore()

const cartCount = computed(() => cartStore.totalItems)

const isActive = (path: string) => route.path === path

const handleSuperAction = () => {
  if (currentModule.value === 'food') {
    // Open restaurant search or quick add
    alert('Hızlı Restoran Arama')
  } else {
    // General quick menu
    alert('Hızlı İşlem Menüsü')
  }
}
</script>

<style scoped>
.pb-safe {
  padding-bottom: env(safe-area-inset-bottom);
}
</style>
