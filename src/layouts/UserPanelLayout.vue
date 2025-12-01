<template>
  <div class="user-panel-layout">
    <header class="user-header">
      <h1>User Panel</h1>
    </header>
    <nav class="user-nav">
      <router-link to="/user">Dashboard</router-link>
      <router-link to="/user/orders">Orders</router-link>
      <router-link to="/user/favorites">Favorites</router-link>
      <router-link to="/user/profile">Profile</router-link>
      <router-link to="/user/settings">Settings</router-link>
    </nav>
    <main class="user-content">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
// User panel layout
</script>

<style scoped>
.user-panel-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.user-header {
  padding: 1rem;
  background: #3b82f6;
  color: white;
}

.user-nav {
  padding: 1rem;
  background: #60a5fa;
  display: flex;
  gap: 1rem;
}

.user-nav a {
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
}

.user-nav a:hover {
  background: #93c5fd;
}

.user-content {
  flex: 1;
  padding: 2rem;
}
</style>
