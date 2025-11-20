<template>
  <section class="p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold text-purple-700">🔗 Davet Zinciri Analizi</h2>
    <table class="mt-4 w-full text-sm">
      <thead>
        <tr>
          <th>Kod</th>
          <th>Davet Eden</th>
          <th>Kullanan</th>
          <th>Kullanım Zamanı</th>
          <th>Durum</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.code">
          <td>{{ log.code }}</td>
          <td>{{ log.inviter }}</td>
          <td>{{ log.used_by }}</td>
          <td>{{ log.used_at }}</td>
          <td>{{ log.is_used ? '✅' : '❌' }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const logs = ref([])

onMounted(async () => {
  const res = await axios.get('/admin/invitation-analytics')
  logs.value = res.data
})
</script>