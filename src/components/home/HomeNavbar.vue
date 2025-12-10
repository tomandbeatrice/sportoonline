<template>
  <nav class="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/80 backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white font-bold">
          S
        </div>
        <span class="text-xl font-bold text-slate-800">SportoOnline</span>
      </router-link>

      <!-- Navigation Links (Desktop) -->
      <div class="hidden md:flex items-center gap-6">
        <router-link to="/" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Ana Sayfa</router-link>
        <router-link to="/discovery" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Keşfet</router-link>
        <router-link to="/market" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Market</router-link>
        <router-link to="/campaigns" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Kampanyalar</router-link>
      </div>

      <!-- Auth Buttons -->
      <div class="flex items-center gap-3">
        <template v-if="authStore.isAuthenticated">
           <router-link to="/cart" class="relative p-2 text-slate-600 hover:text-blue-600 transition-colors">
            <ShoppingCart class="h-5 w-5" />
            <!-- Badge could go here -->
          </router-link>
          
          <div class="relative group">
             <button class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <User class="h-4 w-4" />
                <span>Hesabım</span>
             </button>
             <!-- Dropdown Menu -->
             <div class="absolute right-0 mt-2 w-48 origin-top-right rounded-xl bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden group-hover:block">
                <div class="px-4 py-2 border-b border-gray-100">
                  <p class="text-sm font-medium text-gray-900">Kullanıcı</p>
                  <p class="text-xs text-gray-500 truncate">user@example.com</p>
                </div>
                <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil</router-link>
                <router-link to="/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Siparişlerim</router-link>
                <div class="border-t border-gray-100 my-1"></div>
                <button @click="handleLogout" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Çıkış Yap</button>
             </div>
          </div>
        </template>
        <template v-else>
          <router-link 
            to="/login" 
            class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors"
          >
            Giriş Yap
          </router-link>
          <router-link 
            to="/register" 
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow-sm shadow-blue-200 transition-colors"
          >
            Kayıt Ol
          </router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { ShoppingCart, User } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
