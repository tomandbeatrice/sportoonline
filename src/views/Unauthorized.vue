<template>
  <div class="unauthorized">
    <h2>Erişim Engellendi</h2>
    <p>Bu sayfaya erişim yetkiniz yok.</p>
  </div>
</template>

<script setup>
console.warn('Yetkisiz erişim')
</script>

<style scoped>
.unauthorized {
  padding: 2rem;
  color: red;
}
</style>