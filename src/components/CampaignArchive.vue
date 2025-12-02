<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
      <BadgeIcon name="archive" cls="w-6 h-6 text-blue-600" /> Kampanya Arşivleri
    </h2>

    <div class="flex gap-4 mb-6">
      <input v-model="search" type="text" placeholder="Kampanya adı ara..." class="input input-bordered w-full max-w-xs" />
      <button @click="applySearch" class="btn btn-primary">Filtrele</button>
    </div>

    <table class="table table-zebra w-full text-sm">
      <thead>
        <tr>
          <th>Ad</th>
          <th>Bitiş Tarihi</th>
          <th>Skor</th>
          <th>Dönüşüm %</th>
          <th>Geri Bildirim</th>
          <th>Log Sayısı</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filteredArchive" :key="item.id">
          <td>{{ item.name }}</td>
          <td>{{ item.end_date }}</td>
          <td>{{ item.score }}</td>
          <td>{{ item.conversion_rate }}%</td>
          <td>{{ item.feedback_count }}</td>
          <td>{{ item.log_count }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

type ArchiveItem = {
  id: number
  name: string
  end_date: string
  score: number
  conversion_rate: number
  feedback_count: number
  log_count: number
}

const archive = ref<ArchiveItem[]>([])
const filteredArchive = ref<ArchiveItem[]>([])
const search = ref('')

const fetchArchive = async () => {
  const { data } = await axios.get('/admin/campaign-archive')
  archive.value = data
  filteredArchive.value = data
}

const applySearch = () => {
  const keyword = search.value.toLowerCase()
  filteredArchive.value = archive.value.filter(item =>
    item.name.toLowerCase().includes(keyword)
  )
}

onMounted(fetchArchive)
</script>

<style scoped>
.input {
  min-width: 200px;
}
</style>