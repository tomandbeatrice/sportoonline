<template>
  <section class="p-6 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Kampanya Bazlı Yorum Özeti</h2>

    <div v-for="item in summary" :key="item.campaign" class="border p-4 rounded shadow-sm">
      <h3 class="text-lg font-semibold text-indigo-700">
        {{ item.campaign }} (<IconStar cls="w-4 h-4 text-yellow-400 inline-block mr-1" :filled="true" /> {{ item.avgRating }})
      </h3>

      <ul class="mt-2 space-y-1 text-sm text-gray-700">
        <li v-for="c in item.comments" :key="c.comment">
          <span class="font-medium inline-flex items-center gap-1"><IconStar cls="w-4 h-4 text-yellow-400" :filled="true" /> <span>{{ c.rating }}</span></span> — {{ c.comment }}
        </li>
      </ul>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import IconStar from '@/components/icons/IconStar.vue'

const summary = ref([])

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  const res = await axios.get(`/api/seller/${sellerId}/campaign-feedback-summary`)
  summary.value = res.data
})
</script>