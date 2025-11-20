<template>
  <nav class="navbar">
    <router-link :to="'/home'" :class="{ active: $route.path === '/home' }">Ana Sayfa</router-link>
    <router-link :to="'/admin/panel'" :class="{ active: $route.path === '/admin/panel' }">Panel</router-link>
    <router-link :to="'/analiz/export'" :class="{ active: $route.path === '/analiz/export' }">Log Analiz</router-link>
    <router-link :to="'/testing'" :class="{ active: $route.path === '/testing' }" class="test-link">Test Dashboard</router-link>
    <span class="user">{{ role }}</span>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const role = computed(() => localStorage.getItem('role') || 'misafir')
</script>

<style scoped>
.navbar {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  padding: 1rem 2rem;
  background-color: var(--card);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  align-items: center;
}

.router-link-active,
.active {
  font-weight: bold;
  color: var(--accent);
  text-decoration: underline;
}

.user {
  margin-left: auto;
  font-size: 0.9rem;
  color: var(--text);
}

/* Mobil uyum */
@media (max-width: 768px) {
  .navbar {
    flex-direction: column;
    align-items: flex-start;
  }

  .user {
    margin-left: 0;
    margin-top: 0.5rem;
  }
}
</style>