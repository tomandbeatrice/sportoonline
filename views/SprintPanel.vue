<template>
  <div>
    <SprintCard
      :demoImage="demoImage"
      :testStatus="testStatus"
      :tarih="tarih"
      @exported="handleExport"
    />

    <transition name="fade">
      <div v-if="showCongrats" class="congrats">
        🎉 Sprint Tamamlandı: {{ tarih }}
      </div>
    </transition>

    <SprintGallery :tamamlananlar="tamamlananlar" />
    <RoadmapProgress :tamamlananSprintSayisi="tamamlananlar.length" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import SprintCard from '@/components/SprintCard.vue'
import SprintGallery from '@/components/SprintGallery.vue'
import RoadmapProgress from '@/components/RoadmapProgress.vue'

const demoImage = '/assets/sprint-demo.png'
const testStatus = 'Yeşil'
const tarih = '2025-08-19'

const tamamlananlar = ref([])
const showCongrats = ref(false)

function handleExport(veri) {
  tamamlananlar.value.push(veri)
  showCongrats.value = true
  setTimeout(() => showCongrats.value = false, 3000)
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.congrats {
  background: #d4edda;
  color: #155724;
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
  font-weight: bold;
  margin-top: 1rem;
}
</style>