<template>
  <div class="seller-login">
    <h2>Satıcı Girişi</h2>
    <form @submit.prevent="login">
      <input v-model="email" type="email" placeholder="E-posta" required />
      <input v-model="password" type="password" placeholder="Şifre" required />
      <button type="submit">Giriş</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const email = ref('')
const password = ref('')

function login() {
  localStorage.setItem('role', 'seller')
  window.location.href = '/seller/dashboard'
}
</script>

<style scoped>
.seller-login { max-width: 320px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 8px; }
input { display: block; margin-bottom: 1rem; width: 100%; padding: 0.5rem; }
button { width: 100%; }
</style>