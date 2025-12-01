<template>
  <div class="motivation-pulse">
    <h4>🏁 Sprint Nabzı</h4>
    <div class="pulse-bar">
      <div class="pulse-fill" :style="{ width: motivation + '%' }"></div>
    </div>
    <p>Motivasyon: %{{ motivation }}</p>
    <CelebrationEffect v-if="motivation >= 90" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useMotivationPulse } from '@/composables/useMotivation'
import CelebrationEffect from './CelebrationEffect.vue'

const props = defineProps<{ modulStats: ModulStats[] }>()

const { motivation, calculate } = useMotivationPulse(props.modulStats)

onMounted(() => calculate())
</script>

<style scoped>
.motivation-pulse {
  margin-top: 1rem;
  padding: 1rem;
  background-color: var(--cardSoft);
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

.pulse-bar {
  height: 12px;
  background-color: var(--bgSoft);
  border-radius: 6px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.pulse-fill {
  height: 100%;
  background-color: var(--primary);
  transition: width 0.5s ease;
}
</style>