<template>
  <div class="sprint-demo">
    <h2>Sprint Demo Görseli</h2>
    <img src="/images/risingsportoonline.png" alt="Sprint Demo" class="demo-image" />
    <p class="status">Test Paneli: <span class="green">Yeşil</span></p>
  </div>
</template>

<script setup>
// Ek logic istersen buraya ekleyebiliriz
</script>

<style scoped>
.sprint-demo {
  text-align: center;
  padding: 2rem;
  background-color: #f9f9f9;
  border-radius: 12px;
  box-shadow: 0 0 12px rgba(0,0,0,0.1);
}
.demo-image {
  max-width: 100%;
  height: auto;
  border: 2px solid #4CAF50;
  border-radius: 8px;
}
.status {
  margin-top: 1rem;
  font-size: 1.2rem;
}
.green {
  color: #4CAF50;
  font-weight: bold;
}
</style>