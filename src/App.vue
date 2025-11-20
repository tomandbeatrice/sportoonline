<template>
  <div id="app" class="flex flex-col min-h-screen">
    <Navbar v-if="showNavbar" />
    <main class="flex-1">
      <ErrorBoundary>
        <router-view />
      </ErrorBoundary>
    </main>
    <Footer v-if="showFooter" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/layout/Navbar.vue'
import Footer from '@/components/layout/Footer.vue'
import ErrorBoundary from '@/components/ErrorBoundary.vue'

const route = useRoute()

// Admin ve seller sayfalarında navbar/footer gizle
const showNavbar = computed(() => {
  return !route.path.startsWith('/admin') && !route.path.startsWith('/seller')
})

const showFooter = computed(() => {
  return !route.path.startsWith('/admin') && !route.path.startsWith('/seller')
})
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
</style>
