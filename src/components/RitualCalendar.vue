<template>
  <div class="calendar">
    <h3>Ekip Ritüel Takvimi</h3>
    <ul>
      <li v-for="ritual in rituals" :key="ritual.day">
        📅 {{ ritual.day }} → {{ ritual.title }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
const rituals = [
  { day: 'Pazartesi', title: 'Roadmap Güncelleme' },
  { day: 'Çarşamba', title: 'Demo Hazırlığı' },
  { day: 'Cuma', title: 'Test Paneli ve Export' }
]
</script>

<style scoped>
.calendar {
  margin-top: 2rem;
  background: #fffbea;
  padding: 16px;
  border-radius: 8px;
}
</style>