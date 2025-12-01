<template>
  <section class="py-16 bg-gradient-to-br from-green-100 to-white text-center">
    <div class="max-w-3xl mx-auto px-4">
      <h1 class="text-4xl font-bold text-green-800 mb-4">İlk 100 Satıcıya Özel</h1>
      <p class="text-lg text-gray-700 mb-6">
        Sportoonline’da mağazanı aç, %0 komisyonla satışa başla. Sayaç dolmadan katıl!
      </p>

      <div class="text-3xl font-bold text-green-700 mb-6">
        {{ remaining }} yer kaldı
      </div>

      <router-link
        :to="`/register?seller=true&code=${inviteCode}`"
        class="inline-block bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition"
      >
        Davet Koduyla Mağaza Aç
      </router-link>

      <p class="mt-4 text-sm text-gray-500">Davet kodun: <strong>{{ inviteCode }}</strong></p>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const remaining = ref(100)
const inviteCode = 'SPORTOON100'

onMounted(async () => {
  try {
    const res = await axios.get('/api/admin/stats')
    remaining.value = Math.max(0, 100 - res.data.registeredSellers)
  } catch (error) {
    console.error('Sayaç verisi alınamadı:', error)
  }
})
</script>