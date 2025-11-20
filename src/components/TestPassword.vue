<template>
  <div class="test-password" v-if="password">
    <p>Test Parola: {{ password }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const password = ref('')

onMounted(async () => {
  try {
    const res = await fetch('/api/test-password')
    const data = await res.json()
    password.value = data.password
  } catch (err) {
    console.error('Parola alınamadı:', err)
  }
})
</script>

<style scoped>
.test-password {
  font-weight: bold;
  font-size: 1.2rem;
  margin-top: 1rem;
}
</style>