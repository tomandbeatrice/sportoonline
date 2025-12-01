<template>
  <div class="error-trend">
    <h4>⚠️ Hata Trendleri</h4>
    <ErrorChart :data="errorData" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ErrorChart from './ErrorChart.vue'

const errorData = ref([])

onMounted(async () => {
  const res = await fetch('/api/errors')
  errorData.value = await res.json()
})
</script>