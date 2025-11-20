<template>
  <div class="banner-manager">
    <h2>📢 Banner Yönetimi</h2>

    <input type="file" @change="handleFile" />
    
    <label>Konum Seç:</label>
    <select v-model="position">
      <option value="top">Üst</option>
      <option value="sidebar">Yan</option>
      <option value="footer">Alt</option>
    </select>

    <button @click="saveBanner">Kaydet</button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const imageFile = ref(null)
const position = ref('top')

function handleFile(e) {
  imageFile.value = e.target.files[0]
}

async function saveBanner() {
  const formData = new FormData()
  formData.append('image', imageFile.value)
  formData.append('position', position.value)

  try {
    await axios.post('/api/banners', formData)
    alert('Banner başarıyla eklendi')
  } catch (err) {
    console.error('Banner hatası:', err)
  }
}
</script>

<style scoped>
.banner-manager {
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

input[type="file"],
select {
  margin-top: 1rem;
  width: 100%;
  padding: 0.5rem;
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