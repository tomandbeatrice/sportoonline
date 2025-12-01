<template>
  <div class="admin-panel-layout">
    <header class="admin-header">
      <h1>Admin Panel</h1>
    </header>
    <nav class="admin-nav">
      <router-link to="/admin">Dashboard</router-link>
      <router-link to="/admin/orders">Orders</router-link>
      <router-link to="/admin/products">Products</router-link>
      <router-link to="/admin/sellers">Sellers</router-link>
      <router-link to="/admin/customers">Customers</router-link>
      <router-link to="/admin/categories">Categories</router-link>
      <router-link to="/admin/settings">Settings</router-link>
    </nav>
    <main class="admin-content">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
// Admin panel layout
</script>

<style scoped>
.admin-panel-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.admin-header {
  padding: 1rem;
  background: #1f2937;
  color: white;
}

.admin-nav {
  padding: 1rem;
  background: #374151;
  display: flex;
  gap: 1rem;
}

.admin-nav a {
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
}

.admin-nav a:hover {
  background: #4b5563;
}

.admin-content {
  flex: 1;
  padding: 2rem;
}
</style>
