<template>
  <section class="dashboard">
    <SummaryPanel />
    <SprintSummary />
  </section>
</template>

<script setup lang="ts">
import SummaryPanel from './SummaryPanel.vue'
import SprintSummary from './SprintSummary.vue'
</script>

<style scoped>
.dashboard {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  padding: 2rem;
  background-color: var(--background);
  border-radius: 16px;
}
@media (max-width: 768px) {
  .dashboard {
    grid-template-columns: 1fr;
  }
}
</style>