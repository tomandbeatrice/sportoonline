<template>
  <div ref="root" class="lazy-section">
    <slot v-if="isVisible"></slot>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const root = ref<HTMLElement | null>(null)
const isVisible = ref(false)
let observer: IntersectionObserver | null = null

onMounted(() => {
  if (!root.value) return
  // If IntersectionObserver is not available, render immediately
  if (!('IntersectionObserver' in window)) {
    isVisible.value = true
    return
  }
  observer = new IntersectionObserver((entries) => {
    const entry = entries[0]
    if (entry && entry.isIntersecting) {
      isVisible.value = true
      if (observer && root.value) {
        observer.unobserve(root.value)
        observer.disconnect()
        observer = null
      }
    }
  }, { rootMargin: '200px 0px', threshold: 0.01 })
  observer.observe(root.value)
})

onUnmounted(() => {
  if (observer) {
    observer.disconnect()
    observer = null
  }
})
</script>

<style scoped>
.lazy-section { min-height: 24px; }
</style>
