<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <StatCard
      v-for="stat in quickStats"
      :key="stat.id"
      :title="stat.title"
      :value="stat.value"
      :change="stat.change"
      :trend="stat.trend"
      :icon="stat.icon"
      :color="stat.color"
      @click="handleStatClick(stat.id)"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUnifiedDashboardStore } from '@/stores/unifiedDashboardStore'
import StatCard from '@/components/unified/StatCard.vue'

const router = useRouter()
const store = useUnifiedDashboardStore()
const quickStats = computed(() => store.quickStats)

function handleStatClick(statId: string) {
  const routes: Record<string, string> = {
    revenue: '/admin/analytics/revenue',
    orders: '/admin/orders',
    campaigns: '/admin/campaigns',
    sellers: '/admin/seller-applications'
  }
  
  if (routes[statId]) {
    router.push(routes[statId])
  }
}
</script>
