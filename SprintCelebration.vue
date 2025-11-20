<template>
  <transition name="fade">
    <div v-if="showCelebration" class="celebration">
      🎉 Sprint Başarısı! Ekip enerjisi %{{ motivation }} ile zirvede!
    </div>
  </transition>
</template>

<script setup lang="ts">
import { watch, ref } from 'vue'
const props = defineProps<{ motivation: number }>()

const showCelebration = ref(false)

watch(() => props.motivation, (val) => {
  if (val >= 90) {
    showCelebration.value = true
    setTimeout(() => showCelebration.value = false, 3000)
  }
})
</script>

<style scoped>
.celebration {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  background: linear-gradient(90deg, #ffcc00, #ff66cc);
  color: white;
  padding: 1rem 2rem;
  border-radius: 12px;
  font-weight: bold;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  z-index: 999;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>