<template>
  <div class="cockpit">
    <header class="header">
      <h1>🚀 Sprint Cockpit</h1>
      <ThemeSwitcher />
    </header>

    <section class="metrics">
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
import ThemeSwitcher from '@/components/ThemeSwitcher.vue'
import MotivationPulse from '@/components/MotivationPulse.vue'
import SprintRadar from '@/components/SprintRadar.vue'
import ModuleCard from '@/components/ModuleCard.vue'
import CelebrationEffect from '@/components/CelebrationEffect.vue'
</script>

<style scoped>
.cockpit {
  background-color: #0B1D3A;
  color: #E0E0E0;
  padding: 2rem;
  font-family: 'Inter', sans-serif;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.metrics {
  display: flex;
  gap: 2rem;
  margin-top: 1rem;
}
.modules {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}
</style>