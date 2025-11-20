<template>
  <div class="dashboard">
    <section class="summary">
      <MotivationPulse :value="overallSuccess" />
      <SprintRadar :modules="modules" />
    </section>

    <section class="modules">
      <ModuleCard
        v-for="mod in modules"
        :key="mod.id"
        v-bind="mod"
      />
    </section>

    <CelebrationEffect v-if="overallSuccess >= 90" />
  </div>
</template>

<script setup lang="ts">
import { modules, overallSuccess } from '@/mock/sprintData'
import MotivationPulse from '@/components/MotivationPulse.vue'
import SprintRadar from '@/components/SprintRadar.vue'
import ModuleCard from '@/components/ModuleCard.vue'
import CelebrationEffect from '@/components/CelebrationEffect.vue'
</script>

<style scoped>
.dashboard {
  padding: 2rem;
  background-color: var(--bg);
  color: var(--text);
}
.summary {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
  margin-bottom: 2rem;
}
.modules {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}
</style>