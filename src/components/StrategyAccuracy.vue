<template>
  <div>
    <h3>Öneri Doğruluk Oranı</h3>
    <p v-if="accuracy !== null">Doğruluk: <strong>{{ accuracy }}%</strong></p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const accuracy = ref(null)

onMounted(async () => {
  try {
    const res = await axios.get('/api/admin/strategy-accuracy')
    accuracy.value = res.data.accuracy_percent
  } catch (error) {
    accuracy.value = 'Veri alınamadı'
    console.error('Doğruluk oranı alınırken hata:', error)
  }
})
</script>