<template>
  <section class="p-4 bg-yellow-50 rounded">
    <h2 class="text-lg font-bold text-yellow-700">📢 Bugün Önerilen Kampanya</h2>
    <div v-if="suggestion">
      <strong>{{ suggestion.suggested_campaign }}</strong>  
      <br />Tür: {{ suggestion.type }}  
      <br />İndirim: %{{ suggestion.discount }}  
      <br />Komisyon Avantajı: %{{ suggestion.commission_drop }}

      <button
        @click="accept"
        class="mt-2 bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700"
      >
        Katıl
      </button>
    </div>
    <p v-else class="text-gray-500">Bugün için önerilen kampanya bulunamadı.</p>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const suggestion = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/seller/today-suggestion')
  suggestion.value = res.data
})

const accept = async () => {
  const user = await axios.get('/api/user')
  await axios.post(`/api/seller/${user.data.id}/join-discount-campaign`, {
    code: suggestion.value.suggested_campaign,
    discount: suggestion.value.discount
  })
}
</script>