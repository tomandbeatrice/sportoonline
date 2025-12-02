<template>
  <div class="admin-panel-layout">
    <div class="admin-sidebar">
      <!-- Admin Sidebar will go here -->
      <h3>Admin Panel</h3>
    </div>
    <div class="admin-content">
      <router-view />
    </div>
  </div>
</template>

<script setup lang="ts">
// Admin panel layout
</script>

<style scoped>
.admin-panel-layout {
  display: flex;
  min-height: 100vh;
}
.admin-sidebar {
  width: 250px;
  background: #2c3e50;
  color: white;
  padding: 1rem;
}
.admin-content {
  flex: 1;
  padding: 1rem;
}
</style>
