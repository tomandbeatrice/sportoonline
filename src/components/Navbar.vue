<template>
  <nav class="navbar">
    <RouterLink to="/">Cockpit</RouterLink>
    <RouterLink to="/products">Ürünler</RouterLink>
    <RouterLink to="/cart">Sepet</RouterLink>
    <RouterLink to="/checkout">Ödeme</RouterLink>
    <RouterLink to="/order-status">Sipariş Durumu</RouterLink>
    <RouterLink to="/admin">Admin Paneli</RouterLink>
  </nav>
</template>

<script setup>
// Vue Router aktif olduğu için ek import gerekmez
</script>

<style scoped>
.navbar {
  display: flex;
  gap: 20px;
  padding: 10px;
  background-color: #f4f4f4;
}
</style>