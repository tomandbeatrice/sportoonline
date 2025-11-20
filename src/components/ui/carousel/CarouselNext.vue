<template>
  <button
    type="button"
    class="hidden h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-500 transition hover:border-gray-300 hover:text-gray-900 lg:flex"
    :class="props.class"
    @click="carousel?.slideBy('next')"
  >
    ›
  </button>
</template>

<script setup lang="ts">
import { inject } from 'vue'
import { carouselKey } from './tokens'

const props = withDefaults(defineProps<{ class?: string }>(), { class: '' })
const carousel = inject(carouselKey)
</script>
