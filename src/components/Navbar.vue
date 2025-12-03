<template>
  <nav class="navbar">
    <RouterLink to="/products">Ürünler</RouterLink>
    <RouterLink to="/cart">Sepet</RouterLink>
    <RouterLink to="/checkout">Ödeme</RouterLink>
  </nav>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.navbar {
  display: flex;
  gap: 1rem;
  padding: 1rem 2rem;
  background-color: var(--card);
  border-bottom: 1px solid #ddd;
}
a {
  text-decoration: none;
  color: var(--text);
  font-weight: 500;
}
a.router-link-active {
  color: var(--accent);
}
</style>