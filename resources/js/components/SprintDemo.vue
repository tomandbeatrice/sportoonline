<template>
  <div class="sprint-demo">
    <h2>✅ Sprint Demo Aktif</h2>
    <img :src="imageUrl" alt="Sprint Demo" class="demo-image" />
    <p class="status">Test Paneli: <span class="green">Yeşil</span></p>
    <button @click="exportSprint" class="export-btn">Export Et</button>
  </div>
</template>

<script setup>
const imageUrl = '/images/risingsportoonline.png'

const exportSprint = () => {
  alert('Sprint export edildi! 🎉')
}
</script>

<style scoped>
.sprint-demo {
  text-align: center;
  padding: 2rem;
  background-color: #fefefe;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  max-width: 600px;
  margin: 0 auto;
}

.demo-image {
  max-width: 100%;
  height: auto;
  border: 2px solid #4CAF50;
  border-radius: 8px;
  margin-top: 1rem;
  transition: transform 0.3s ease;
}
.demo-image:hover {
  transform: scale(1.03);
}

.status {
  margin-top: 1rem;
  font-size: 1.2rem;
}

.green {
  color: #4CAF50;
  font-weight: 600;
}

.export-btn {
  margin-top: 1.5rem;
  padding: 0.6rem 1.2rem;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease;
}
.export-btn:hover {
  background-color: #45a049;
}
</style>