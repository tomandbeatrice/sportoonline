<template>
  <div class="cockpit-view">
    <h2>Sprint Kartları</h2>
    <SprintCard
      v-for="(item, index) in demoItems"
      :key="index"
      :demoImage="item.src"
      :testStatus="item.test"
      :tarih="item.tarih"
    />
  </div>
</template>

<script setup>
import SprintCard from '@/components/SprintCard.vue'

const demoItems = [
  { src: '/images/sprint1.png', test: 'Yeşil', tarih: '2025-08-01' },
  { src: '/images/sprint2.webp', test: 'Sarı', tarih: '2025-08-08' },
  { src: '/images/sprint3.png', test: 'Kırmızı', tarih: '2025-08-15' }
]
</script>

<style scoped>
.cockpit-view {
  padding: 2rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}
</style>