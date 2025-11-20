<script setup lang="ts">
import { ref, onMounted } from 'vue'

const gonderiler = ref([])

const durumRenk = (durum: string) => {
  if (durum.includes('Teslim')) return 'bg-green-100'
  if (durum.includes('Yolda')) return 'bg-yellow-100'
  return 'bg-gray-100'
}

onMounted(async () => {
  const res = await fetch('/api/kargo')
  gonderiler.value = await res.json()
})
</script>

<template>
  <div class="kargo-paneli">
    <h3 class="text-lg font-bold mb-2">🚚 Kargo Durumu</h3>
    <div
      v-for="g in gonderiler"
      :key="g.id"
      class="kargo-kutu p-4 rounded mb-2"
      :class="durumRenk(g.durum)"
    >
      <strong class="block">{{ g.kod }}</strong>
      <span>{{ g.alici }}</span>
      <p class="text-sm text-gray-600">{{ g.durum }}</p>
    </div>
  </div>
</template>

<style scoped>
.kargo-paneli {
  max-width: 500px;
}
</style>