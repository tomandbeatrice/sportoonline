<template>
  <section class="demo-wrapper">
    <h2>🔍 Modül Çıktı Önizleme</h2>

    <!-- Ürün Modülü Çıktısı -->
    <ModuleOutput title="Ürün Modülü Çıktısı" :output="productOutput" />

    <!-- Kategori Modülü Çıktısı -->
    <ModuleOutput title="Kategori Modülü Çıktısı" :output="categoryOutput" />

    <!-- Sipariş Modülü Çıktısı -->
    <ModuleOutput title="Sipariş Modülü Çıktısı" :output="orderOutput" />
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ModuleOutput from './ModuleOutput.vue'

const productOutput = ref<string | undefined>()
const categoryOutput = ref<string | undefined>()
const orderOutput = ref<string | undefined>()

onMounted(() => {
  // Mock veri simülasyonu – canlı backend entegrasyonu yerine
  setTimeout(() => {
    productOutput.value = '✅ Ürün modülü başarıyla yüklendi.'
    categoryOutput.value = '📦 Kategori verileri eşlendi.'
    orderOutput.value = '🧾 Sipariş modülü hazır.'
  }, 1000)
})
</script>

<style scoped>
.demo-wrapper {
  max-width: 600px;
  margin: 2rem auto;
  padding: 1rem;
  background: #fafafa;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}
h2 {
  margin-bottom: 1rem;
  font-size: 1.4rem;
  color: #444;
}
</style>