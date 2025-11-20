<template>
  <div class="verify">
    <h2>Yayın Erişimi İçin Parola Gir</h2>
    <input v-model="input" placeholder="Parola..." />
    <button @click="checkPassword">Doğrula</button>
    <p v-if="error" class="error">Parola hatalı</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const input = ref('')
const error = ref(false)
const router = useRouter()

function checkPassword() {
  if (input.value === 'Yusuf') {
    router.push('/')
  } else {
    error.value = true
  }
}
</script>

<style scoped>
.verify {
  padding: 2rem;
}
.error {
  color: red;
  margin-top: 1rem;
}
</style>