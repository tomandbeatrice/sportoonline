<template>
  <div class="settings-panel">
    <h2>Teşvik Kampanyası Ayarları</h2>

    <form @submit.prevent="saveSettings">
      <label>Minimum İndirim Oranı (%):</label>
      <input type="number" v-model="settings.min_discount" min="0" max="100" />

      <label>Outlet Kategori ID:</label>
      <input type="number" v-model="settings.outlet_category_id" />

      <label>Komisyon İndirimi (%):</label>
      <input type="number" v-model="settings.commission_discount" min="0" max="100" />

      <label>Kampanya Aktif mi?</label>
      <select v-model="settings.active">
        <option :value="true">Evet</option>
        <option :value="false">Hayır</option>
      </select>

      <button type="submit">Kaydet</button>
    </form>

    <p v-if="status">{{ status }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const settings = ref({
  min_discount: 25,
  outlet_category_id: 7,
  commission_discount: 70,
  active: true
})

const status = ref('')

onMounted(async () => {
  const res = await axios.get('/api/admin/incentive-settings')
  settings.value = res.data
})

async function saveSettings() {
  try {
    await axios.put('/api/admin/incentive-settings', settings.value)
    status.value = 'Ayarlar başarıyla güncellendi ✅'
  } catch (err) {
    status.value = 'Hata oluştu ❌'
  }
}
</script>

<style scoped>
.settings-panel {
  max-width: 500px;
  margin: auto;
  padding: 20px;
}
label {
  display: block;
  margin-top: 10px;
}
input, select {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
}
button {
  margin-top: 15px;
  padding: 10px 20px;
}
</style>