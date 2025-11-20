<template>
  <div class="kargo-paneli">
    <h3>🚚 Kargo Durumu</h3>
    <div
      v-for="gonderi in gonderiler"
      :key="gonderi.id"
      class="kargo-kutu"
      :class="durumRenk(gonderi.durum)"
    >
      <strong>{{ gonderi.kod }}</strong>
      <p>{{ gonderi.durum }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
const gonderiler = [
  { id: 1, kod: 'GLV12345', durum: 'Hazırlanıyor' },
  { id: 2, kod: 'GLV67890', durum: 'Yolda' },
  { id: 3, kod: 'GLV54321', durum: 'Teslim Edildi' }
]

const durumRenk = (durum: string) => {
  if (durum === 'Teslim Edildi') return 'teslim';
  if (durum === 'Yolda') return 'yolda';
  return 'hazir';
}
</script>

<style scoped>
.kargo-paneli {
  padding: 1rem;
  background-color: var(--renk-arkaplan);
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.kargo-kutu {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 6px;
  color: white;
}

.teslim {
  background-color: var(--renk-başarı);
}

.yolda {
  background-color: var(--renk-uyarı);
}

.hazir {
  background-color: var(--renk-bilgi);
}
</style>