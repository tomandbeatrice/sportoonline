<template>
  <div class="seller-panel-layout">
    <header class="seller-header">
      <h1>Seller Panel</h1>
    </header>
    <nav class="seller-nav">
      <router-link to="/seller">Dashboard</router-link>
      <router-link to="/seller/products">Products</router-link>
      <router-link to="/seller/orders">Orders</router-link>
      <router-link to="/seller/analytics">Analytics</router-link>
      <router-link to="/seller/settings">Settings</router-link>
    </nav>
    <main class="seller-content">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
// Seller panel layout
</script>

<style scoped>
.seller-panel-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.seller-header {
  padding: 1rem;
  background: #059669;
  color: white;
}

.seller-nav {
  padding: 1rem;
  background: #10b981;
  display: flex;
  gap: 1rem;
}

.seller-nav a {
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
}

.seller-nav a:hover {
  background: #34d399;
}

.seller-content {
  flex: 1;
  padding: 2rem;
}
</style>
