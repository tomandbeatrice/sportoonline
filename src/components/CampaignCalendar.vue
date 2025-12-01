<template>
  <section class="p-4 space-y-6">
    <!-- Satıcıya özel kampanya takvimi -->
    <div>
      <h2 class="text-lg font-bold text-indigo-700 flex items-center gap-2">
        <BadgeIcon name="calendar" cls="w-5 h-5" /> Kişisel Kampanya Takvimi
      </h2>
      <ul class="space-y-3 text-sm">
        <li v-for="item in plan" :key="item.campaign" class="border p-3 rounded">
          {{ item.campaign }} ({{ item.type }})  
          → Tahmini: {{ item.expectedConversion }}%
          <button
            @click="restart(item.campaign)"
            class="mt-2 bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
          >
            Başlat
          </button>
          <span v-if="msg[item.campaign]" class="text-green-700 mt-1 block">
            {{ msg[item.campaign] }}
          </span>
        </li>
      </ul>
    </div>

    <!-- Gelecek avantajlı kampanyalar -->
    <div>
      <h2 class="text-lg font-bold text-green-700 flex items-center gap-2">
        <BadgeIcon name="trending-up" cls="w-5 h-5" /> Gelecek Avantajlı Kampanyalar
      </h2>
      <ul class="space-y-3 text-sm">
        <li v-for="c in upcoming" :key="c.code" class="border p-3 rounded bg-green-50">
          <strong>{{ c.title }}</strong>  
          <br />İndirim: %{{ c.discount_rate }}  
          <br />Komisyon Avantajı: %{{ c.commission_drop }}  
          <br />Başlangıç: {{ c.start_date }}  
          <br />Bitiş: {{ c.end_date }}

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
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const plan = ref([])
const msg = ref({})
const campaigns = ref([])
const joined = ref({})

onMounted(async () => {
  const user = await axios.get('/api/user')
  const sellerId = user.data.id

  const plannerRes = await axios.get(`/api/seller/${sellerId}/campaign-planner`)
  plan.value = plannerRes.data

  const campaignRes = await axios.get('/api/planned-campaigns')
  campaigns.value = campaignRes.data
})

const restart = async (campaign) => {
  const user = await axios.get('/api/user')
  const sellerId = user.data.id

  const res = await axios.post(`/api/seller/${sellerId}/restart-campaign`, { campaign })
  msg.value[campaign] = res.data.message
}

const join = async (code) => {
  const user = await axios.get('/api/user')
  const sellerId = user.data.id

  const res = await axios.post(`/api/seller/${sellerId}/join-discount-campaign`, {
    code,
    discount: 15
  })

  joined.value[code] = res.data.newCommissionRate
}

const upcoming = computed(() =>
  campaigns.value.filter(c => new Date(c.start_date) >= new Date())
)
</script>