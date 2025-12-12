<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-900">
    <!-- Sidebar -->
    <AdminSidebar 
      :is-collapsed="isSidebarCollapsed"
      :is-mobile-open="isMobileSidebarOpen"
      :user-name="userName"
      @close="isMobileSidebarOpen = false"
      @logout="handleLogout"
      @toggle-collapse="isSidebarCollapsed = !isSidebarCollapsed"
    />

    <!-- Mobile Overlay -->
    <div 
      v-if="isMobileSidebarOpen"
      @click="isMobileSidebarOpen = false"
      class="fixed inset-0 bg-black/50 z-30 lg:hidden"
    ></div>

    <!-- Main Content -->
    <div 
      class="transition-all duration-300"
      :class="isSidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64'"
    >
      <!-- Top Header -->
      <AdminTopBar 
        :is-collapsed="isSidebarCollapsed"
        :user-name="userName"
        @toggle-sidebar="isMobileSidebarOpen = !isMobileSidebarOpen"
        @toggle-collapse="isSidebarCollapsed = !isSidebarCollapsed"
        @logout="handleLogout"
      />

      <!-- Page Content -->
      <main class="p-4 md:p-6 lg:p-8">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminSidebar from '@/components/admin/AdminSidebar.vue'
import AdminTopBar from '@/components/admin/AdminTopBar.vue'

const route = useRoute()
const router = useRouter()

const isSidebarCollapsed = ref(false)
const isMobileSidebarOpen = ref(false)
const userName = ref('Admin User')

// Load sidebar state from localStorage
onMounted(() => {
  const savedState = localStorage.getItem('admin-sidebar-collapsed')
  if (savedState !== null) {
    isSidebarCollapsed.value = JSON.parse(savedState)
  }
  
  // Get user name from localStorage
  const user = localStorage.getItem('user')
  if (user) {
    try {
      const userData = JSON.parse(user)
      userName.value = userData.name || 'Admin User'
    } catch (e) {
      console.error('Failed to parse user data:', e)
    }
  }
})

// Save sidebar state
watch(isSidebarCollapsed, (newValue) => {
  localStorage.setItem('admin-sidebar-collapsed', JSON.stringify(newValue))
})

// Auto-close mobile sidebar on route change
watch(() => route.path, () => {
  isMobileSidebarOpen.value = false
})

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  localStorage.removeItem('role')
  router.push('/login')
}
</script>
