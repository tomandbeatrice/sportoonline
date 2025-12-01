<template>
  <div class="profile-form">
    <h2>Profilim</h2>
    <form @submit.prevent="updateProfile">
      <input v-model="name" type="text" placeholder="Ad Soyad" />
      <input v-model="email" type="email" placeholder="Email" />
      <input v-model="phone" type="text" placeholder="Telefon" />
      <button type="submit">Güncelle</button>
    </form>
  </div>
</template>
<script setup>
import { ref } from 'vue';
const name = ref('');
const email = ref('');
const phone = ref('');
function updateProfile() {
  // Profil güncelleme işlemi
}
</script>
<style scoped>
.profile-form { max-width: 400px; margin: auto; }
</style>