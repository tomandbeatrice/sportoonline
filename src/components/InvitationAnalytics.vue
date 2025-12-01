<template>
  <section class="p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold text-purple-700 flex items-center gap-2">
      <BadgeIcon name="link" cls="w-6 h-6" /> Davet Zinciri Analizi
    </h2>
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
          <td>
            <BadgeIcon :name="log.is_used ? 'check' : 'x'" :cls="log.is_used ? 'w-5 h-5 text-green-600' : 'w-5 h-5 text-red-600'" />
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const logs = ref([])

onMounted(async () => {
  const res = await axios.get('/admin/invitation-analytics')
  logs.value = res.data
})
</script>