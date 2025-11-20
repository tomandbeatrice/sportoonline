<template>
  <section class="access-denied">
    <h1>🚫 Erişim Engellendi</h1>
    <p>Bu sayfaya erişim yetkiniz bulunmamaktadır.</p>
    <router-link to="/home">🏠 Ana Sayfaya Dön</router-link>
  </section>
</template>

<script setup lang="ts"></script>

<style scoped>
.access-denied {
  text-align: center;
  margin-top: 100px;
}
h1 {
  font-size: 2rem;
  color: #c00;
}
p {
  margin: 1rem 0;
}
a {
  color: #007bff;
  text-decoration: underline;
}
</style>