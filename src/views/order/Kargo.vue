<template>
  <div class="kargo-kapsayici">
    <h3>🚚 Kargo Durumu</h3>
    <div v-if="loading">Yükleniyor...</div>
    <div v-else>
      <div
        v-for="gonderi in gonderiler"
        :key="gonderi.id"
        :class="durumRenk(gonderi.durum)"
        class="kargo-item"
      >
        <p>{{ gonderi.kod }} - {{ gonderi.durum }}</p>
      </div>
      <div v-if="gonderiler.length === 0">Kargo kaydı bulunamadı.</div>
    </div>
    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const gonderiler = ref<{ id: number; kod: string; durum: string }[]>([])
const loading = ref(true)
const error = ref('')

const durumRenk = (durum: string) => {
  if (durum === 'Teslim Edildi') return 'teslim'
  if (durum === 'Yolda') return 'yolda'
  return 'hazir'
}

onMounted(async () => {
  try {
    // API'den veri çekmek için örnek:
    // const res = await fetch('/api/kargo')
    // gonderiler.value = await res.json()

    // Şimdilik statik veri:
    gonderiler.value = [
      { id: 1, kod: 'GLV12345', durum: 'Hazırlanıyor' },
      { id: 2, kod: 'GLV67890', durum: 'Yolda' },
      { id: 3, kod: 'GLV54321', durum: 'Teslim Edildi' }
    ]
  } catch (e: any) {
    error.value = 'Kargo verisi alınamadı.'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.kargo-kapsayici {
  padding: 1rem;
  border-radius: 8px;
  background: var(--card, #fff);
  max-width: 400px;
  margin: 2rem auto;
}
.kargo-item {
  margin-bottom: 0.5rem;
  padding: 0.5rem;
  border-radius: 4px;
  color: #fff;
}
.teslim {
  background-color: #4caf50;
}
.yolda {
  background-color: #ff9800;
}
.hazir {
  background-color: #2196f3;
}
.error {
  color: #e53e3e;
  margin-top: 1rem;
}
</style>