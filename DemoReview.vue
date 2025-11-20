<template>
  <div class="bg-white p-4 rounded shadow">
    <h3 class="text-lg font-bold mb-2">Yorumlar</h3>
      <div v-for="review in reviews" :key="review.id" class="border-b py-2">
      <p class="text-sm text-gray-700 inline-flex items-center gap-2"><IconStar cls="w-4 h-4 text-yellow-400" :filled="true" /> <span>{{ review.rating }}</span> – {{ review.comment }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import IconStar from '@/components/icons/IconStar.vue'

const reviews = ref([])

onMounted(async () => {
  const res = await axios.get('/api/demo/reviews')
  reviews.value = res.data
})
</script>