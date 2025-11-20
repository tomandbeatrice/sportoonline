<template>
  <div v-if="banner" class="dynamic-banner">
    <img :src="banner" alt="Dinamik Banner" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const banner = ref('')

onMounted(() => {
  banner.value = localStorage.getItem('banner') || ''
})
</script>

<style scoped>
.dynamic-banner img {
  width: 100%;
  height: auto;
  margin-top: 2rem;
}
</style>