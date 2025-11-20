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

    <div class="tamamlananlar">
      <h3>Tamamlanan Sprintler</h3>
      <ul>
        <li v-for="(sprint, index) in tamamlananlar" :key="index">
          {{ sprint.tarih }} - Test Durumu: {{ sprint.test }}
        </li>
      </ul>
      <progress :value="ilerlemeYuzdesi" max="100"></progress>
      <p>{{ ilerlemeYuzdesi }}% tamamlandı</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import SprintCard from './SprintCard.vue'

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

const toplamSprint = 10
const ilerlemeYuzdesi = computed(() =>
  Math.round((tamamlananlar.value.length / toplamSprint) * 100)
)
</script>

<style scoped>
.tamamlananlar {
  margin-top: 2rem;
  background: #f9f9f9;
  padding: 1rem;
  border-radius: 8px;
}
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