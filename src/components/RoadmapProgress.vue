<template>
  <div class="roadmap">
    <h2>Roadmap İlerlemesi</h2>
    <progress :value="ilerlemeYuzdesi" max="100"></progress>
    <p>{{ ilerlemeYuzdesi }}% tamamlandı</p>

    <div class="katki">
      <h3>Ekip Katkısı</h3>
      <ul>
        <li v-for="(oran, ekip) in ekipKatkisi" :key="ekip">
          {{ ekip }}: {{ oran }}
        </li>
      </ul>
    </div>

    <div class="mesaj">
      <p>{{ motivasyonMesaji }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const toplamSprint = 10
const tamamlananSprint = 7 // Bu veri dışarıdan prop olarak da alınabilir

const ekipKatkisi = {
  frontend: '40%',
  backend: '35%',
  QA: '25%'
}

const ilerlemeYuzdesi = computed(() =>
  Math.round((tamamlananSprint / toplamSprint) * 100)
)

const motivasyonMesaji = computed(() => {
  if (ilerlemeYuzdesi.value >= 90) return '🚀 Final sprint yaklaşıyor, son adımda birlikteyiz!'
  if (ilerlemeYuzdesi.value >= 60) return '🔧 Teknik netlik ve ekip katkısı harika gidiyor!'
  if (ilerlemeYuzdesi.value >= 30) return '📈 Sprint ritüeli oturdu, cockpit kültürü oluşuyor!'
  return '🛠️ İlk adımlar atıldı, her export bir iz bırakıyor.'
})
</script>

<style scoped>
.roadmap {
  margin-top: 2rem;
  padding: 1rem;
  background: #eef2f7;
  border-radius: 8px;
}
.katki {
  margin-top: 1rem;
}
.mesaj {
  margin-top: 1rem;
  font-weight: bold;
  color: #2c3e50;
}
</style>