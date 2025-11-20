<template>
  <div class="celebration">
    🎉 Tebrikler! Sprint başarı oranı %90'ı geçti!
  </div>
</template>

<style scoped>
.celebration {
  background: #00C48C;
  color: #fff;
  padding: 1rem;
  margin-top: 2rem;
  text-align: center;
  border-radius: 8px;
  font-weight: bold;
  animation: pulse 1s infinite alternate;
}

@keyframes pulse {
  from { transform: scale(1); }
  to { transform: scale(1.05); }
}
</style>