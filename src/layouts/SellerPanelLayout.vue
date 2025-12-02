<template>
  <div class="seller-panel-layout">
    <div class="seller-sidebar">
      <!-- Seller Sidebar will go here -->
      <h3>Seller Panel</h3>
    </div>
    <div class="seller-content">
      <router-view />
    </div>
  </div>
</template>

<script setup lang="ts">
// Seller panel layout
</script>

<style scoped>
.seller-panel-layout {
  display: flex;
  min-height: 100vh;
}
.seller-sidebar {
  width: 250px;
  background: #34495e;
  color: white;
  padding: 1rem;
}
.seller-content {
  flex: 1;
  padding: 1rem;
}
</style>
