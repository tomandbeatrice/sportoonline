<template>
  <div class="login-form">
    <h2>Giriş Yap</h2>
    <div v-if="error" class="error-message text-red-600 mb-4">{{ error }}</div>
    <form @submit.prevent="handleLogin">
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Şifre" required />
      <button type="submit">Giriş Yap</button>
    </form>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const router = useRouter();
const { login } = useAuth();

const email = ref('');
const password = ref('');
const error = ref('');

async function handleLogin() {
  try {
    await login(email.value, password.value);
    router.push('/');
  } catch (e) {
    error.value = e.response?.data?.message || 'Giriş hatası';
  }
}
</script>
<style scoped>
.login-form { max-width: 400px; margin: auto; }
</style>