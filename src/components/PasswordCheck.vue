<template>
  <div class="password-check">
    <input v-model="input" placeholder="Parolayı girin" />
    <button @click="verify">Doğrula</button>
    <p v-if="status === 'ok'">✅ Parola doğru</p>
    <p v-else-if="status === 'fail'">❌ Parola hatalı</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const input = ref('')
const status = ref('')

const correctPassword = '1yus1'

function verify() {
  status.value = input.value === correctPassword ? 'ok' : 'fail'
}
</script>

<style scoped>
.password-check {
  margin-top: 2rem;
  text-align: center;
}
input {
  padding: 0.5rem;
  font-size: 1rem;
}
button {
  margin-left: 1rem;
  padding: 0.5rem 1rem;
}
</style>