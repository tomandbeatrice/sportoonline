<template>
  <div class="suggestions">
    <h3>AI Önerileri</h3>
    <ul>
      <li v-for="item in suggestions" :key="item.id">
        ✅ {{ item.text }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
const suggestions = [
  { id: 1, text: 'Sprint sonunda otomatik test raporu PDF’e eklensin.' },
  { id: 2, text: 'Katkı skorları haftalık ortalamaya göre normalize edilsin.' },
  { id: 3, text: 'Roadmap güncellemeleri ekip geri bildirimine göre sıralansın.' }
]
</script>

<style scoped>
.suggestions {
  margin-top: 2rem;
  background: #eef6f8;
  padding: 16px;
  border-radius: 8px;
}
</style>