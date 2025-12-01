<template>
  <div class="suggestion-card" v-if="suggestion">
    <h4>{{ modul.name }} – Teknik Öneri</h4>
    <p>{{ suggestion }}</p>
    <span class="risk-score">Risk Skoru: {{ modul.riskScore }}</span>
    <Badge :score="modul.riskScore" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Badge from '@/components/ui/Badge.vue'

defineProps<{
  modul: {
    name: string
    progress: number
    successRate: number
    riskScore: number
    uiDropRate: number
  }
}>()

const props = defineProps<{
  modul: {
    name: string
    progress: number
    riskScore: number
    uiDropRate: number
  }
}>()

const suggestion = computed(() => {
  if (props.modul.riskScore > 70 && props.modul.progress < 60) {
    return 'Refactor önerisi: coverage artırılmalı'
  }
  if (props.modul.uiDropRate > 30) {
    return 'UI sadeleştirme önerisi: kullanıcı davranışı düşmüş'
  }
  if (modul.successRate < 50) {
    return 'Test kapsamı düşük, modül başarısı kritik seviyede'
  }
  return null
})
</script>

<style scoped>
.suggestion-card {
  background-color: var(--cardSoft);
  padding: 1rem;
  border-radius: 8px;
  margin-top: 0.5rem;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.suggestion-card h4 {
  font-size: 1rem;
  font-weight: 600;
  color: var(--textStrong);
}

.suggestion-card p {
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--text);
}

.risk-score {
  font-size: 0.85rem;
  color: var(--accent);
  font-weight: 500;
}
</style>