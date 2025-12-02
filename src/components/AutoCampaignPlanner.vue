<template>
  <div>
    <h2>Otomatik Kampanya Planı</h2>
    <div v-if="plan">
      <p><strong>Satıcı:</strong> {{ plan.seller_id }}</p>
      <p><strong>Tip:</strong> {{ plan.campaign_type }}</p>
      <p><strong>İndirim:</strong> %{{ plan.suggested_discount }}</p>
      <p><strong>Süre:</strong> {{ plan.start_date }} → {{ plan.end_date }}</p>
      <p><strong>Gerekçe:</strong> {{ plan.reasoning }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const plan = ref(null)
const sellerId = 45

onMounted(async () => {
  const res = await axios.get(`/api/seller/${sellerId}/generate-campaign-plan`)
  plan.value = res.data
})
</script>