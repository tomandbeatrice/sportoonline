<template>
  <div class="user-panel-layout">
    <div class="user-sidebar">
      <!-- User Sidebar will go here -->
      <h3>User Panel</h3>
    </div>
    <div class="user-content">
      <router-view />
    </div>
  </div>
</template>

<script setup lang="ts">
// User panel layout
</script>

<style scoped>
.user-panel-layout {
  display: flex;
  min-height: 100vh;
}
.user-sidebar {
  width: 250px;
  background: #3498db;
  color: white;
  padding: 1rem;
}
.user-content {
  flex: 1;
  padding: 1rem;
}
</style>
