<template>
  <div class="admin-dashboard">
    <h1>🛠️ Admin Panel</h1>

    <nav class="tab-nav">
      <button :class="{ active: activeTab === 'export' }" @click="activeTab = 'export'">
        Export Analizi
      </button>
      <button :class="{ active: activeTab === 'overview' }" @click="activeTab = 'overview'">
        Genel Bakış
      </button>
      <button :class="{ active: activeTab === 'settings' }" @click="activeTab = 'settings'">
        Ayarlar
      </button>
    </nav>

    <div class="tab-content">
      <div v-if="activeTab === 'export'">
        <ExportLogChart />
      </div>
      <div v-else-if="activeTab === 'overview'">
        <p>📊 Genel sistem durumu burada gösterilecek.</p>
      </div>
      <div v-else-if="activeTab === 'settings'">
        <p>⚙️ Ayar bileşenleri burada yer alacak.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ExportLogChart from '@/components/ExportLogChart.vue'

const activeTab = ref('export')
</script>

<style scoped>
.admin-dashboard {
  max-width: 1200px;
  margin: auto;
  padding: 2rem;
}
h1 {
  font-size: 1.8rem;
  margin-bottom: 1rem;
}
.tab-nav {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}
.tab-nav button {
  padding: 0.6rem 1.2rem;
  background-color: #f0f0f0;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
}
.tab-nav button.active {
  background-color: #007bff;
  color: white;
}
.tab-content {
  background-color: #fff;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}
</style>