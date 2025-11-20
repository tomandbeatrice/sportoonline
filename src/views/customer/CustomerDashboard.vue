<template>
  <div class="customer-dashboard">
    <h2>Müşteri Paneli</h2>
    <router-link to="/customer/orders">Siparişlerim</router-link>
    <router-link to="/customer/profile">Profilim</router-link>
  </div>
</template>
<script setup lang="ts"></script>
<style scoped>
.customer-dashboard { max-width: 600px; margin: 2rem auto; }
</style>