<template>
  <div class="analiz-ozeti">
    <div class="ozet-kutu success">
      <h2>%{{ successRate }}</h2>
      <p>Başarı Oranı</p>
    </div>
    <div class="ozet-kutu error">
      <h2>{{ errorCount }}</h2>
      <p>Toplam Hata</p>
    </div>
    <div class="ozet-kutu time">
      <h2>{{ totalDuration }} dk</h2>
      <p>Toplam Süre</p>
    </div>
  </div>
</template>

<script setup lang="ts">
const successRate = 85 // API'den dinamik alınabilir
const errorCount = 12
const totalDuration = 340
</script>

<style scoped>
.analiz-ozeti {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 2rem;
}

.ozet-kutu {
  flex: 1;
  background: #fff;
  border-radius: 8px;
  padding: 1rem;
  text-align: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.success h2 {
  color: #2ecc71;
}

.error h2 {
  color: #e74c3c;
}

.time h2 {
  color: #3498db;
}

p {
  margin-top: 0.5rem;
  font-weight: 500;
  color: #555;
}
</style>