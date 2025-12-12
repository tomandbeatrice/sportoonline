<template>
  <div class="min-h-screen bg-slate-50 font-sans">
    <!-- Sidebar -->
    <SellerSidebar 
      :is-open="isSidebarOpen" 
      :user-name="userName"
      @close="isSidebarOpen = false"
      @logout="handleLogout"
    />

    <!-- Main Content -->
    <div class="lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
      <!-- Top Header -->
      <header class="sticky top-0 z-30 bg-white border-b border-slate-200 px-4 py-3 shadow-sm">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <button 
              @click="isSidebarOpen = !isSidebarOpen"
              class="p-2 rounded-lg hover:bg-slate-100 lg:hidden text-slate-600"
            >
              <span class="text-xl">☰</span>
            </button>
            
            <!-- Breadcrumbs -->
            <div class="hidden md:flex items-center gap-2 text-sm text-slate-500">
              <span>Satıcı Paneli</span>
              <span class="text-slate-300">/</span>
              <span class="font-medium text-slate-900">{{ currentRouteName }}</span>
            </div>
          </div>

          <div class="flex items-center gap-4">
            <button class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-orange-50 text-orange-700 rounded-full text-xs font-medium hover:bg-orange-100 transition">
              <span>🔥</span>
              <span>Kampanyaya Katıl</span>
            </button>

            <div class="h-6 w-px bg-slate-200 mx-1"></div>

            <button class="p-2 rounded-full hover:bg-slate-100 relative text-slate-500">
              🔔
              <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
            </button>
            
            <button class="p-2 rounded-full hover:bg-slate-100 text-slate-500">
              💬
            </button>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 md:p-8 overflow-x-hidden">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import SellerSidebar from '@/components/seller/SellerSidebar.vue'

import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const isSidebarOpen = ref(false)
const userName = computed(() => authStore.user?.name || authStore.user?.store_name || 'Mağazam')

const currentRouteName = computed(() => {
  const nameMap: Record<string, string> = {
    'SellerDashboard': 'Özet',
    'SellerOrders': 'Siparişler',
    'SellerProducts': 'Ürünlerim',
    'SellerCampaigns': 'Kampanyalar',
    'SellerFinancialReport': 'Finansal Raporlar',
    'SellerOnboarding': 'Mağaza Ayarları'
  }
  return nameMap[route.name as string] || route.name || 'Sayfa'
})

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed', error)
    router.push('/login')
  }
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
  transform: translateY(5px);
}
</style>
