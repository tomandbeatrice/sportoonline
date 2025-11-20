<template>
  <div v-if="role === 'admin'" class="f1-banner">
    <img src="https://cdn.sooyemek.com.tr/assets/f1.jpg" alt="Sprint Görseli" />
    <h2>LAZARON FULL SPRINT TEAM RADIO BELGIUM</h2>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const role = ref('guest')

onMounted(() => {
  role.value = localStorage.getItem('role') || 'guest'
})
</script>

<style scoped>
.f1-banner {
  text-align: center;
  margin-top: 2rem;
}
.f1-banner img {
  max-width: 100%;
  height: auto;
}
</style>