<template>
  <div class="segment-manager">
    <h3>Segment Yönetimi</h3>
    
    <div v-if="error" class="error-banner">
      ⚠️ {{ error }}
    </div>
    
    <div v-if="loading" class="loading">
      Yükleniyor...
    </div>
    
    <div v-else>
      <div v-for="segment in segments" :key="segment.id" class="segment-row">
        <input v-model="segment.name" placeholder="Segment Adı" />
        <input v-model="segment.weight" type="number" step="0.1" min="0" max="1" placeholder="Ağırlık" />
      </div>
      <button @click="save" :disabled="loading">Kaydet</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const segments = ref([])
const loading = ref(false)
const error = ref(null)

onMounted(async () => {
  try {
    loading.value = true
    const res = await axios.get('/api/v1/admin/segments')
    segments.value = res.data
  } catch (err) {
    console.warn('Backend not available:', err.message)
    error.value = 'Backend bağlantısı kurulamadı. Lütfen Laravel backend sunucusunu başlatın.'
    // Mock data for development
    segments.value = [
      { id: 1, name: 'Segment A', weight: 0.5 },
      { id: 2, name: 'Segment B', weight: 0.3 },
      { id: 3, name: 'Segment C', weight: 0.2 }
    ]
  } finally {
    loading.value = false
  }
})

const save = async () => {
  try {
    loading.value = true
    await axios.post('/api/v1/admin/segments/update', segments.value)
    alert('Segmentler kaydedildi!')
  } catch (err) {
    console.warn('Save failed:', err.message)
    alert('Kaydetme başarısız. Backend çalışmıyor.')
  } finally {
    loading.value = false
  }
}
</script>