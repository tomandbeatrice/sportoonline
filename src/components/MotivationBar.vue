<template>
  <div class="motivation-bar">
    <label>🚀 Ekip Motivasyonu</label>
    <progress :value="motivation" max="100"></progress>
    <span>{{ motivation }}%</span>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'

const motivation = ref(0)

onMounted(async () => {
  const res = await fetch('/api/motivation')
  motivation.value = (await res.json()).value
})

watch(motivation, (val) => {
  if (val > 90) {
    // triggerCelebrationEffect()
  }
})
</script>