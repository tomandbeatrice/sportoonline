<template>
  <div class="gallery">
    <h3>Demo Galerisi</h3>
    <div class="grid">
      <div v-for="(sprint, index) in tamamlananlar" :key="index" class="card">
        <img :src="sprint.demoImage" alt="Demo Görseli" />
        <p>{{ sprint.tarih }} - {{ sprint.test }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  tamamlananlar: Array
})
</script>

<style scoped>
.gallery {
  margin-top: 2rem;
}
.grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}
.card {
  width: 150px;
  text-align: center;
}
.card img {
  width: 100%;
  border-radius: 4px;
}
</style>