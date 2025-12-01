<template>
  <div>
    <h2>Kampanya Başarı Simülasyonu</h2>

    <label>Yanıt Süresi (dk):</label>
    <input type="range" v-model="inputs.response_time" min="5" max="120" />
    <span>{{ inputs.response_time }}</span>

    <label>Yorum Yanıt Oranı (%):</label>
    <input type="range" v-model="inputs.comment_response_rate" min="0" max="100" />
    <span>{{ inputs.comment_response_rate }}</span>

    <label>Ürün Güncelleme Sayısı:</label>
    <input type="number" v-model="inputs.update_count" min="0" />

    <label>Export Kullanımı:</label>
    <input type="number" v-model="inputs.export_usage" min="0" />

    <button @click="simulate">Simülasyonu Hesapla</button>

    <div v-if="result">
      <p><strong>Tahmini Skor:</strong> {{ result.predicted_score }}</p>
      <p><strong>Önerilen Kampanya:</strong> {{ result.suggested_campaign }}</p>
      <p><strong>İndirim Oranı:</strong> %{{ result.suggested_discount }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const inputs = ref({
  response_time: 30,
  comment_response_rate: 60,
  update_count: 5,
  export_usage: 2
})

const result = ref(null)

async function simulate() {
  const res = await axios.post('/api/seller/simulate-campaign-success', inputs.value)
  result.value = res.data
}
</script>