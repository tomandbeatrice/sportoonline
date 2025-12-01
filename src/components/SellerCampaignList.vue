<template>
  <section class="p-4 space-y-4">
    <h2 class="text-lg font-bold text-indigo-700 flex items-center gap-2">
      <BadgeIcon name="light-bulb" cls="w-5 h-5" /> Planlanmış Kampanyalar
    </h2>
    <ul class="space-y-3 text-sm">
      <li v-for="c in campaigns" :key="c.code" class="border p-3 rounded">
        <strong>{{ c.title }}</strong>  
        <br />%{{ c.discount_rate }} indirim, %{{ c.commission_drop }} komisyon avantajı

        <button
          @click="join(c.code)"
          class="mt-2 bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
        >
          Katıl
        </button>

        <p v-if="joined[c.code]" class="text-green-700 mt-1 flex items-center gap-2">
          <BadgeIcon name="check" cls="w-4 h-4" /> Katıldınız! Yeni komisyon oranınız: %{{ joined[c.code] }}
        </p>
      </li>
    </ul>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const campaigns = ref([])
const joined = ref({})

onMounted(async () => {
  const res = await axios.get('/api/planned-campaigns')
  campaigns.value = res.data
})

const join = async (code) => {
  const user = await axios.get('/api/user')
  const sellerId = user.data.id

  const res = await axios.post(`/api/seller/${sellerId}/join-discount-campaign`, {
    code,
    discount: 15
  })

  joined.value[code] = res.data.newCommissionRate
}
</script>