<template>
  <section class="p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold text-purple-700 mb-6">📊 Strateji Cockpit</h2>

    <!-- Üst Modüller: Karar ve Roadmap -->
    <div class="strategy-section mb-8">
      <div class="module-card">
        <DecisionSimulator />
      </div>
      <div class="module-card">
        <RoadmapUpdater />
      </div>
    </div>

    <!-- Ana Analiz Grid -->
    <div class="grid grid-cols-2 gap-6">
      <CampaignTrendChart />
      <SuggestionSuccessChart />
      <InvitationAnalytics />
      <SellerBehaviorRadar />
    </div>

    <!-- Dönüşüm Hunisi -->
    <div class="mt-8">
      <ConversionFunnel />
    </div>
  </section>
</template>

<script setup lang="ts">
import DecisionSimulator from '@/components/DecisionSimulator.vue'
import RoadmapUpdater from '@/components/RoadmapUpdater.vue'
import CampaignTrendChart from '@/components/admin/CampaignTrendChart.vue'
import SuggestionSuccessChart from '@/components/admin/SuggestionSuccessChart.vue'
import InvitationAnalytics from '@/views/admin/InvitationAnalytics.vue'
import SellerBehaviorRadar from '@/components/admin/SellerBehaviorRadar.vue'
import ConversionFunnel from '@/components/admin/ConversionFunnel.vue'
</script>

<style scoped>
.strategy-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 1.5rem;
  padding: 1rem;
  background: #f4f6f8;
  border-radius: 12px;
}

.module-card {
  background: #fff;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  transition: box-shadow 0.3s ease;
}

.module-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
</style>