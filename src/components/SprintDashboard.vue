<template>
  <div class="cockpit-view">
    <h2>Sprint Görselleri</h2>
    <SprintDemo
      v-for="(img, index) in demoImages"
      :key="index"
      :demoImage="img"
    />
  </div>
</template>

<script setup>
import SprintDemo from '@/components/SprintDemo.vue'

const demoImages = [
  '/images/sprint1.png',
  '/images/sprint2.webp',
  '/images/sprint3.png'
]
</script>