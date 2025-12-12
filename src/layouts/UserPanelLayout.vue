<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-blue-600 to-blue-700 text-white flex-shrink-0 shadow-xl">
      <div class="p-6">
        <div class="text-2xl font-bold mb-2">👤 Hesabım</div>
        <p class="text-blue-100 text-sm">{{ user?.name || 'Kullanıcı' }}</p>
      </div>
      <nav>
        <ul class="space-y-1 px-3">
          <li>
            <router-link to="/buyer/dashboard" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
              <span class="mr-3 text-xl">🏠</span>
              <span class="font-medium">Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/orders" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
              <span class="mr-3 text-xl">📦</span>
              <span class="font-medium">Siparişlerim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/favorites" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
              <span class="mr-3 text-xl">❤️</span>
              <span class="font-medium">Favorilerim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/coupons" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
              <span class="mr-3 text-xl">🎫</span>
              <span class="font-medium">Kuponlarım</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/following" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
              <span class="mr-3 text-xl">⭐</span>
              <span class="font-medium">Takip Ettiklerim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/addresses" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
              <span class="mr-3 text-xl">📍</span>
              <span class="font-medium">Adreslerim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/returns" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
              <span class="mr-3 text-xl">🔄</span>
              <span class="font-medium">İadelerim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/messages" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
              <span class="mr-3 text-xl">💬</span>
              <span class="font-medium">Mesajlar</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/profile" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
              <span class="mr-3 text-xl">⚙️</span>
              <span class="font-medium">Ayarlar</span>
            </router-link>
          </li>
        </ul>
      </nav>
      
      <!-- Logout -->
      <div class="absolute bottom-0 w-64 p-4 border-t border-blue-500">
        <button 
          @click="handleLogout"
          class="w-full px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium flex items-center justify-center gap-2"
        >
          <span>🚪</span>
          <span>Çıkış Yap</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white shadow-sm p-4 flex justify-between items-center border-b border-slate-200">
        <div>
          <h1 class="text-xl font-semibold text-slate-900">{{ pageTitle }}</h1>
          <p class="text-sm text-slate-500">{{ currentDate }}</p>
        </div>
        <div class="flex items-center space-x-4">
          <router-link 
            to="/notifications" 
            class="relative p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition"
          >
            <span class="text-xl">🔔</span>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
          </router-link>
          <router-link 
            to="/" 
            class="text-sm text-blue-600 hover:text-blue-800 font-medium"
          >
            Alışverişe Dön
          </router-link>
        </div>
      </header>
      <div class="flex-1 overflow-y-auto bg-slate-50">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const user = computed(() => authStore.user)

const pageTitle = computed(() => {
  return route.meta.title as string || 'Kullanıcı Paneli'
})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('tr-TR', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.router-link-exact-active,
.router-link-active {
  background-color: rgba(255, 255, 255, 0.15);
  border-left: 4px solid #60a5fa;
}
</style>
