<template>
  <section class="p-6 bg-white rounded shadow">
    <h3 class="text-xl font-bold text-blue-700 mb-4">🛣️ Roadmap Güncelleyici</h3>

    <p class="text-sm text-gray-600 mb-4">Son güncelleme: {{ lastUpdated || 'Henüz güncellenmedi' }}</p>
    <button @click="triggerUpdate" class="mb-6 px-4 py-2 bg-blue-600 text-white rounded">
      🔄 Güncelle
    </button>

    <div class="space-y-4">
      <div v-for="item in roadmap" :key="item.id" class="p-4 border rounded">
        <h4 class="font-semibold text-gray-800">{{ item.title }}</h4>
        <p class="text-sm text-gray-600">Durum: {{ item.status }}</p>
        <select v-model="item.status" @change="updateStatus(item)" class="mt-2 px-2 py-1 border rounded">
          <option value="bekliyor">Bekliyor</option>
          <option value="aktif">Aktif</option>
          <option value="tamamlandı">Tamamlandı</option>
        </select>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const roadmap = ref([])
const lastUpdated = ref('')

onMounted(async () => {
  const res = await axios.get('/api/sprint/roadmap')
  roadmap.value = res.data
})

async function updateStatus(item: { id: number; status: string }) {
  await axios.post('/api/sprint/update-status', {
    id: item.id,
    status: item.status
  })
}

async function triggerUpdate() {
  const res = await fetch('/api/roadmap/update', { method: 'POST' })
  const data = await res.json()
  lastUpdated.value = data.timestamp
}
</script>

<style scoped>
button {
  background-color: #0078d4;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
}
</style>