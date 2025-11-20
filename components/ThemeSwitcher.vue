<template>
  <button @click="toggleTheme" class="theme-btn">
    {{ theme === 'dark' ? '🌙 Dark' : '☀️ Light' }}
  </button>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
const theme = ref('dark')

const toggleTheme = () => {
  theme.value = theme.value === 'dark' ? 'light' : 'dark'
  document.body.className = theme.value
}

onMounted(() => {
  document.body.className = theme.value
})
</script>

<style scoped>
.theme-btn {
  background: transparent;
  border: 1px solid #E0E0E0;
  color: #E0E0E0;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
}
</style>