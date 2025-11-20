<template>
  <div>
    <h3>Segment Yönetimi</h3>
    <div v-for="segment in segments" :key="segment.id">
      <input v-model="segment.name" />
      <input v-model="segment.weight" type="number" />
    </div>
    <button @click="save">Kaydet</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const segments = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/segments')
  segments.value = res.data
})

const save = async () => {
  await axios.post('/api/admin/segments/update', segments.value)
}
</script>