<template>
  <div class="theme-settings">
    <h2>🎨 Tema Ayarları</h2>

    <label>Birincil Renk:</label>
    <input type="color" v-model="theme.primaryColor" />

    <label>Yazı Tipi:</label>
    <select v-model="theme.font">
      <option value="Inter">Inter</option>
      <option value="Roboto">Roboto</option>
      <option value="Open Sans">Open Sans</option>
    </select>

    <label>Layout:</label>
    <select v-model="theme.layout">
      <option value="boxed">Kutulu</option>
      <option value="full">Tam Genişlik</option>
    </select>

    <button @click="saveTheme">Kaydet</button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const theme = ref({
  primaryColor: '#2E8B57',
  font: 'Inter',
  layout: 'full'
})

async function saveTheme() {
  try {
    await axios.post('/api/theme', theme.value)
    alert('Tema başarıyla güncellendi')
  } catch (err) {
    console.error('Tema kaydı hatası:', err)
  }
}
</script>

<style scoped>
.theme-settings {
  padding: 2rem;
  background-color: var(--card);
  border-radius: 8px;
  max-width: 500px;
  margin: auto;
}

h2 {
  color: var(--accent);
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-top: 1rem;
  font-weight: 600;
}

input[type="color"],
select {
  margin-top: 0.5rem;
  width: 100%;
  padding: 0.4rem;
  border-radius: 4px;
  border: 1px solid #ccc;
}

button {
  margin-top: 2rem;
  background-color: var(--accent);
  color: white;
  border: none;
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
}

button:hover {
  background-color: #2e8b57;
}
</style>