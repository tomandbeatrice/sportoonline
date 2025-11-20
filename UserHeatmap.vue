<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-4">Kullanıcı & Gün Bazlı Yoğunluk</h2>

    <!-- Gün Başlıkları -->
    <div class="flex gap-1 mb-2 ml-24">
      <div
        v-for="date in dates"
        :key="date"
        class="w-8 text-xs text-center font-medium text-gray-600"
      >
        {{ date.slice(5) }}
      </div>
    </div>

    <!-- Kullanıcı Satırları -->
    <div
      v-for="user in users"
      :key="user.userId"
      class="flex items-center gap-1 mb-1"
    >
      <div class="w-24 font-semibold text-sm text-gray-800">
        {{ user.userName }}
      </div>
      <div
        v-for="log in user.logs"
        :key="`${user.userId}-${log.date}`"
        :style="{ backgroundColor: getColor(log.count) }"
        class="w-8 h-8 text-xs flex items-center justify-center rounded cursor-pointer transition hover:scale-105"
        @click="showDetail(user.userId, log.date)"
        :title="`${log.count} log - ${log.date}`"
      >
        {{ log.count }}
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue'
import { fetchUserLogMatrix } from '@/api/logs'
import { getColor } from '@/utils/colorScale'
import { showLogDetailModal } from '@/composables/useLogDetail'

type UserLogData = {
  userId: string
  userName: string
  logs: { date: string; count: number }[]
}

const users = ref<UserLogData[]>([])

onMounted(async () => {
  const data = await fetchUserLogMatrix()
  users.value = data.users
})

const dates = computed(() => {
  return users.value[0]?.logs.map(log => log.date) || []
})

function showDetail(userId: string, date: string) {
  showLogDetailModal(userId, date)
}
</script>

<style scoped>
/* Opsiyonel: responsive grid için media query eklenebilir */
</style>