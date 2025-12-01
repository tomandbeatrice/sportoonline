<template>
  <section class="p-6 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Kampanya Dönüşüm Verisi</h2>

    <button
      @click="downloadCSV"
      class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
    >
      Kampanya Dönüşüm Verisini CSV Olarak İndir
    </button>
  </section>
</template>

<script setup>
import axios from 'axios'

const downloadCSV = async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  window.open(`/api/seller/${sellerId}/export-campaign-conversion`, '_blank')
}
</script>