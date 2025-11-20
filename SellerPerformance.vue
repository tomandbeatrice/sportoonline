<template>
  <section class="p-6 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Ürün Performansı</h2>

    <table class="w-full text-left border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2">Ürün</th>
          <th class="p-2">Görüntüleme</th>
          <th class="p-2">Sipariş</th>
          <th class="p-2">Dönüşüm %</th>
          <th class="p-2">Yorum</th>
          <th class="p-2">Puan</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in performance" :key="item.product" class="border-t">
          <td class="p-2">{{ item.product }}</td>
          <td class="p-2">{{ item.views }}</td>
          <td class="p-2">{{ item.orders }}</td>
          <td class="p-2">{{ item.conversionRate }}%</td>
          <td class="p-2">{{ item.comments }}</td>
          <td class="p-2">{{ item.avgRating }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const performance = ref([])

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id

  const res = await axios.get(`/api/seller/${sellerId}/performance`)
  performance.value = res.data
})
</script>