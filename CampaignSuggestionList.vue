<template>
  <section class="p-6 space-y-6">
    <h2 class="text-xl font-bold text-green-700">Tekrar Edilmeye Değer Kampanyalar</h2>

    <ul class="space-y-2 text-sm text-gray-800">
      <li v-for="item in suggestions" :key="item.campaign" class="border p-3 rounded">
        <strong>{{ item.campaign }}</strong>  
        <br />Skor: {{ item.score }}  
        <br />Dönüşüm: {{ item.conversionRate }}%  
        <br />Puan: {{ item.avgRating }}  
        <br />Son tekrar: {{ item.lastRestart }}
        <p class="text-xs text-gray-500 mt-1">
          Skor: {{ item.score }} → (%50 dönüşüm + %30 yorum + %20 tekrar sonrası etki)
        </p>
      </li>
    </ul>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const suggestions = ref([])

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  const res = await axios.get(`/api/seller/${sellerId}/campaign-suggestion-list`)
  suggestions.value = res.data
})
</script>
<template>
  <div>
    <h2>Kampanya Önerileri</h2>
    <ul>
      <li v-for="item in suggestions" :key="item.category_id">
        Kategori {{ item.category_id }} için <strong>{{ item.campaign_type }}</strong> önerisi: %{{ item.suggested_discount }} indirim
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const suggestions = ref([])

onMounted(async () => {
  const sellerId = 45 // test satıcı ID
  const res = await axios.get(`/api/seller/${sellerId}/campaign-suggestion-list`)
  suggestions.value = res.data
})
</script>