<template>
  <div class="user-profile">
    <h2>Profilim</h2>
    <form @submit.prevent="save">
      <label>Ad: <input v-model="profile.name" /></label>
      <label>E-posta: <input v-model="profile.email" type="email" /></label>
      <label>Rol: <input v-model="profile.role" disabled /></label>
      <button type="submit">Kaydet</button>
    </form>
    <section>
      <h3>Şifre Değiştir</h3>
      <input v-model="newPassword" type="password" placeholder="Yeni şifre" />
      <button @click="changePassword">Değiştir</button>
    </section>
    <button @click="logout">Oturumu Kapat</button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const profile = ref({ name: 'Ali', email: 'ali@example.com', role: 'customer' })
const newPassword = ref('')

function save() {
  // Profil güncelleme işlemi
}
function changePassword() {
  // Şifre değiştirme işlemi
}
function logout() {
  // Oturumu kapatma işlemi
}
</script>

<style scoped>
.user-profile { padding: 2rem; max-width: 400px; margin: auto; }
form label { display: block; margin-bottom: 0.5rem; }
form input { margin-left: 0.5rem; }
button { margin-top: 1rem; }
</style>