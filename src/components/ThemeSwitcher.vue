<template>
  <div class="theme-switcher">
    <label class="switch-label">
      <input type="checkbox" v-model="isDark" @change="toggleTheme" />
      <span>{{ isDark ? '🌙 Koyu Mod' : '☀️ Açık Mod' }}</span>
    </label>

    <select v-model="accentColor" @change="applyAccent">
      <option value="#3b82f6">🔵 Mavi</option>
      <option value="#10b981">🟢 Yeşil</option>
      <option value="#f59e0b">🟠 Turuncu</option>
      <option value="#ef4444">🔴 Kırmızı</option>
    </select>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const isDark = ref(false)
const accentColor = ref('#3b82f6')

function toggleTheme() {
  const theme = isDark.value ? 'dark' : 'light'
  document.documentElement.setAttribute('data-theme', theme)
  localStorage.setItem('theme', theme)
}

function applyAccent() {
  document.documentElement.style.setProperty('--accent', accentColor.value)
  localStorage.setItem('accentColor', accentColor.value)
}

onMounted(() => {
  const savedTheme = localStorage.getItem('theme')
  const savedAccent = localStorage.getItem('accentColor')

  if (savedTheme) {
    isDark.value = savedTheme === 'dark'
    document.documentElement.setAttribute('data-theme', savedTheme)
  }

  if (savedAccent) {
    accentColor.value = savedAccent
    document.documentElement.style.setProperty('--accent', savedAccent)
  }
})
</script>

<style scoped>
.theme-switcher {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--background);
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.switch-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  cursor: pointer;
}
select {
  padding: 0.4rem;
  border-radius: 6px;
  border: 1px solid var(--border);
  background: var(--background);
  color: var(--text);
  cursor: pointer;
}
</style>