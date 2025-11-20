<template>
  <div class="home">
    <SprintBanner
      v-if="data"
      :title="data.title"
      :image="data.image"
      :panels="data.panels"
    />
    <TurboMode />
    <PasswordCheck />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SprintBanner from '@/components/SprintBanner.vue'
import TurboMode from '@/components/home/TurboMode.vue'
import PasswordCheck from '@/components/PasswordCheck.vue'

const data = ref(null)

onMounted(async () => {
  const res = await fetch('/data/panel.json')
  data.value = await res.json()
})
</script>