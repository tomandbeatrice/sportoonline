<template>
  <div>
    <h2>Kampanya Öneri Zaman Çizelgesi</h2>
    <ul>
      <li v-for="item in timeline" :key="item.timestamp">
        <strong>{{ item.event }}</strong> — {{ item.type }} (%{{ item.discount }})
        <br />
        <small>{{ item.timestamp }} | Süre: {{ item.start }} → {{ item.end }}</small>
        <p v-if="item.feedback">Geri Bildirim: {{ item.feedback }}</p>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const timeline = ref([])
const sellerId = 45

onMounted(async () => {
  const res = await axios.get(`/api/seller/${sellerId}/campaign-timeline`)
  timeline.value = res.data
})
</script>