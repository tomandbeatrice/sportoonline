<template>
  <div class="card bg-white shadow-md p-4 mb-6">
    <h2 class="text-lg font-semibold mb-2">🔍 Log Detayları</h2>

    <div class="flex gap-4 mb-4">
      <select v-model="selectedType" class="select select-bordered">
        <option value="">Tüm Türler</option>
        <option value="success">Success</option>
        <option value="error">Error</option>
        <option value="warning">Warning</option>
      </select>

      <input
        v-model="searchUser"
        type="number"
        placeholder="Kullanıcı ID"
        class="input input-bordered"
      />
    </div>

    <table class="table table-zebra w-full">
      <thead>
        <tr>
          <th>#</th>
          <th>Type</th>
          <th>User</th>
          <th>Status</th>
          <th>Mesaj</th>
          <th>Tarih</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(log, i) in filteredLogs" :key="i">
          <td>{{ i + 1 }}</td>
          <td>{{ log.type }}</td>
          <td>{{ log.user_id }}</td>
          <td>{{ log.status }}</td>
          <td>{{ log.message }}</td>
          <td>{{ formatDate(log.created_at) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
defineProps<{ logs: Array<{ type: string; user_id: number; status: string; message: string; created_at: string }> }>()

const selectedType = ref('')
const searchUser = ref('')

const formatDate = (str: string) =>
  new Date(str).toLocaleString('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })

const filteredLogs = computed(() => {
  return logs.filter(log => {
    const typeMatch = selectedType.value ? log.type === selectedType.value : true
    const userMatch = searchUser.value ? log.user_id === Number(searchUser.value) : true
    return typeMatch && userMatch
  })
})
</script>
<tr
  v-for="(log, i) in filteredLogs"
  :key="i"
  :class="{
    'bg-green-100': log.status === 'success',
    'bg-red-100': log.status === 'error',
    'bg-yellow-100': log.status === 'warning'
  }"
>
  <td>{{ i + 1 }}</td>
  <td>{{ log.type }}</td>
  <td>{{ log.user_id }}</td>
  <td>{{ log.status }}</td>
  <td>{{ log.message }}</td>
  <td>{{ formatDate(log.created_at) }}</td>
</tr>