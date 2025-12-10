<template>
  <header class="bg-white shadow-sm border-b sticky top-0 z-50">
    <div class="container mx-auto px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <h1 class="text-2xl font-bold text-gray-900">SportoOnline Merkezi Kontrol Paneli</h1>
          <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
            {{ userRole }}
          </span>
        </div>
        <div class="flex items-center gap-4">
          <button @click="$emit('toggle-notifications')" class="relative p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span v-if="unreadNotifications > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
              {{ unreadNotifications }}
            </span>
          </button>
          <div class="flex items-center gap-2">
            <img :src="userAvatar" class="w-10 h-10 rounded-full" />
            <div>
              <p class="text-sm font-semibold">{{ userName }}</p>
              <p class="text-xs text-gray-500">{{ userEmail }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  unreadNotifications: number
}>()

defineEmits(['toggle-notifications'])

// User Info
const user = JSON.parse(localStorage.getItem('user') || '{}')
const userName = ref(user.name || 'Admin User')
const userEmail = ref(user.email || 'admin@sportoonline.com')
const userRole = ref(user.role || 'admin')
const userAvatar = ref(user.avatar || 'https://ui-avatars.com/api/?name=' + userName.value)
</script>
