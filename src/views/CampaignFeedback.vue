<!-- src/views/CampaignFeedback.vue -->
<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">💬 Kampanya Geri Bildirimleri</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedType" class="select select-bordered">
        <option value="">Tüm Tipler</option>
        <option value="positive">Olumlu</option>
        <option value="negative">Olumsuz</option>
        <option value="neutral">Nötr</option>
      </select>
      <button @click="applyFilter" class="btn btn-primary">Filtrele</button>
    </div>

    <table class="table table-zebra w-full text-sm">
      <thead>
        <tr>
          <th>Tarih</th>
          <th>Kullanıcı</th>
          <th>Yorum</th>
          <th>Tip</th>
          <th>Kampanya</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filteredFeedback" :key="item.id">
          <td>{{ item.date }}</td>
          <td>{{ item.user }}</td>
          <td>{{ item.comment }}</td>
          <td>
            <span :class="typeClass(item.type)">
              {{ item.type }}
            </span>
          </td>
          <td>{{ item.campaign }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'

type FeedbackItem = {
  id: number
  date: string
  user: string
  comment: string
  type: 'positive' | 'negative' | 'neutral'
  campaign: string
}

const feedback = ref<FeedbackItem[]>([])
const filteredFeedback = ref<FeedbackItem[]>([])
const selectedType = ref('')

const fetchFeedback = async () => {
  try {
    const { data } = await axios.get('/admin/campaign-feedback')
    feedback.value = data
    filteredFeedback.value = data
  } catch (err) {
    console.error('Geri bildirim verisi alınamadı:', err)
  }
}

const applyFilter = () => {
  filteredFeedback.value = selectedType.value
    ? feedback.value.filter(f => f.type === selectedType.value)
    : feedback.value
}

const typeClass = (type: FeedbackItem['type']) => {
  return {
    positive: 'text-green-600 font-semibold',
    negative: 'text-red-600 font-semibold',
    neutral: 'text-gray-600 font-semibold'
  }[type]
}

onMounted(fetchFeedback)
</script>

<style scoped>
.table td {
  vertical-align: top;
}
</style>