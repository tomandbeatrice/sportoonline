<template>
  <div
    ref="contentEl"
    class="flex snap-x snap-mandatory gap-5 overflow-x-auto pb-4"
  >
    <slot />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { carouselKey } from './tokens'
import { inject } from 'vue'

const contentEl = ref<HTMLDivElement | null>(null)
const carousel = inject(carouselKey)

onMounted(() => {
  carousel?.registerContent(contentEl.value)
})
</script>
