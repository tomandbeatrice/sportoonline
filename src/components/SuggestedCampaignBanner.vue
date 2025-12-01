<template>
  <div v-if="suggested" class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-4">
    <p class="text-sm text-indigo-700">
      📢 Bu hafta önerilen kampanya türü: <strong>{{ suggested }}</strong>. Katılmak ister misiniz?
    </p>
    <button @click="join" class="mt-2 px-3 py-1 bg-indigo-600 text-white text-sm rounded">Katıl</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const suggested = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/seller/suggested-campaign-tag')
  suggested.value = res.data.tag
})

function join() {
  axios.post('/api/seller/join-suggested-campaign')
}
</script>