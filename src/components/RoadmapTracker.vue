<template>
  <div class="roadmap-tracker">
    <h2>📌 Modül İlerleme Takibi</h2>

    <!-- Genel Tamamlanma Yüzdesi -->
    <TamamlanmaYüzdesi :yüzde="tamamlanmaYüzdesi" />

    <!-- Genel Progress Bar -->
    <v-progress-linear
      :value="tamamlanmaYüzdesi"
      color="purple"
      height="10"
      rounded
      class="genel-progress"
    />

    <!-- Modül Durumları -->
    <MilestoneList :modüller="modüller" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoadmapData } from '@/composables/useRoadmapData'
import TamamlanmaYüzdesi from '@/components/Roadmap/TamamlanmaYüzdesi.vue'
import MilestoneList from '@/components/Roadmap/MilestoneList.vue'

const { modüller } = useRoadmapData()

const tamamlanmaYüzdesi = computed(() => {
  const toplam = modüller.value.length
  const tamamlanan = modüller.value.filter(m => m.yüzde === 100).length
  return Math.round((tamamlanan / toplam) * 100)
})
</script>

<style scoped>
.roadmap-tracker {
  max-width: 600px;
  margin: auto;
  padding: 1rem;
}

.genel-progress {
  margin-bottom: 30px;
}
</style>