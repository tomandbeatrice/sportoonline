<template>
  <div class="detail" v-if="campaign">
    <h2>{{ campaign.title }}</h2>
    <img :src="campaign.image" alt="Kampanya Görseli" />
    <p>{{ campaign.description }}</p>
    <button @click="goBack">Geri Dön</button>
  </div>
  <div v-else class="text-center text-gray-500 mt-10">Kampanya verisi bulunamadı.</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const campaign = ref(null)

const goBack = () => {
  router.push('/campaigns')
}

onMounted(async () => {
  try {
    const res = await fetch('/panel.json') // ✅ düzeltildi: .jso → .json
    const data = await res.json()
    campaign.value = data.find(item => item.id == route.params.id)
  } catch (err) {
    console.error('Veri alınamadı:', err)
  }
})
</script>

<style scoped>
.detail {
  max-width: 600px;
  margin: 50px auto;
  text-align: center;
}
img {
  max-width: 100%;
  margin: 20px 0;
}
button {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  cursor: pointer;
}
</style>