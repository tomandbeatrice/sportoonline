<template>
  <div class="register-form">
    <h2>Kayıt Ol</h2>
    <div v-if="error" class="error-message text-red-600 mb-4">{{ error }}</div>
    <form @submit.prevent="handleRegister">
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Şifre" required />
      <input v-model="name" type="text" placeholder="Ad Soyad" required />
      <button type="submit">Kayıt Ol</button>
    </form>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const router = useRouter();
const { register } = useAuth();

const email = ref('');
const password = ref('');
const name = ref('');
const error = ref('');

async function handleRegister() {
  try {
    await register(name.value, email.value, password.value);
    router.push('/');
  } catch (e) {
    error.value = e.response?.data?.message || 'Kayıt hatası';
  }
}
</script>
<style scoped>
.register-form { max-width: 400px; margin: auto; }
</style>