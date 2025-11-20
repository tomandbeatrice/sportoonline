<template>
  <div>
    <h3>Öneri Doğruluk Skoru</h3>
    <p v-if="score !== null">
      AI öneri motorunun isabet oranı: <strong>{{ score }}%</strong>
    </p>
    <p v-else>Yükleniyor...</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const score = ref(null)

onMounted(async () => {
  try {
    const res = await axios.get('/api/admin/suggestion-accuracy-score')
    score.value = res.data.accuracy_percent
  } catch (error) {
    console.error('Doğruluk skoru alınamadı:', error)
  }
})
</script>