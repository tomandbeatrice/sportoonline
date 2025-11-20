<template>
  <div class="banner">
    <img :src="image" alt="Sprint Görseli" />
    <h1>{{ title }}</h1>
    <p>Test Paneli: {{ panel }}</p>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  image: String,
  panel: String
})
</script>

<style scoped>
.banner {
  text-align: center;
  padding: 2rem;
}
.banner img {
  max-width: 100%;
  border-radius: 12px;
}
</style>