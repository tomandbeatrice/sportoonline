<template>
  <section class="sprint-archive">
    <h3>📁 Sprint Arşivi</h3>
    <ul>
      <li v-for="sprint in archivedSprints" :key="sprint.id" class="sprint-card">
        <header>
          <strong>{{ sprint.name }}</strong>
          <span>{{ sprint.date }}</span>
        </header>
        <p>Başarı: %{{ sprint.successRate }} / Motivasyon: %{{ sprint.motivation }}</p>
        <ul class="module-list">
          <li v-for="modul in sprint.modules" :key="modul.id">
            ✅ {{ modul.name }} – %{{ modul.progress }}
          </li>
        </ul>
      </li>
    </ul>
  </section>
</template>

<script setup lang="ts">
const props = defineProps<{
  archivedSprints: Array<{
    id: string
    name: string
    date: string
    successRate: number
    motivation: number
    modules: Array<{ id: string; name: string; progress: number }>
  }>
}>()
</script>

<style scoped>
.sprint-archive {
  padding: 1rem;
  background-color: var(--bgSoft);
  border-radius: 12px;
}
.sprint-card {
  margin-bottom: 1rem;
  padding: 1rem;
  background-color: var(--cardSoft);
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.module-list {
  margin-top: 0.5rem;
  padding-left: 1rem;
}
</style>