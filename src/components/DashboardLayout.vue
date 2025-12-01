<template>
  <div class="dashboard-layout">
    <header class="dashboard-header">
      <h1>Sprint Cockpit</h1>
      <ThemeSwitcher />
    </header>

    <main class="dashboard-grid">
      <SprintSummary />
      <RoadmapProgress />
      <SprintGauge />
      <ModuleTrend />
      <ModuleRisk />
      <ExportPanel />
      <SprintReport />
      <SprintHistory />
    </main>
  </div>
</template>

<script setup lang="ts">
import ThemeSwitcher from '@/components/ThemeSwitcher.vue'
import SprintSummary from '@/components/SprintSummary.vue'
import RoadmapProgress from '@/components/RoadmapProgress.vue'
import SprintGauge from '@/components/SprintGauge.vue'
import ModuleTrend from '@/components/ModuleTrend.vue'
import ModuleRisk from '@/components/ModuleRisk.vue'
import ExportPanel from '@/components/ExportPanel.vue'
import SprintReport from '@/components/SprintReport.vue'
import SprintHistory from '@/components/SprintHistory.vue'
</script>

<style scoped>
.dashboard-layout {
  background-color: var(--background);
  color: var(--text);
  min-height: 100vh;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  background-color: var(--card);
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

.dashboard-header h1 {
  font-size: 1.6rem;
  font-weight: 700;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  padding: 2rem;
}

@media (min-width: 768px) {
  .dashboard-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1200px) {
  .dashboard-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
</style>