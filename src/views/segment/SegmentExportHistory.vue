<template>
  <div class="segment-export-history">
    <h3>Dışa Aktarım Geçmişi</h3>
    <table>
      <thead>
        <tr>
          <th>Kullanıcı</th>
          <th>Tarih</th>
          <th>Segment Tipi</th>
          <th>İşlem</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in history" :key="item.id">
          <td>{{ item.user }}</td>
          <td>{{ item.date }}</td>
          <td>{{ item.segment }}</td>
          <td>
            <button @click="download(item.id)">Tekrar İndir</button>
            <button @click="remove(item.id)">Sil</button>
          </td>
        </tr>
        <tr v-if="history.length === 0">
          <td colspan="4" class="empty">Kayıt bulunamadı.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const history = ref([
  { id: 1, user: 'admin', date: '2024-06-01', segment: 'A' },
  { id: 2, user: 'seller', date: '2024-06-02', segment: 'B' }
])

function download(id: number) {
  // Dosya indirme işlemi (örnek)
  alert(`ID ${id} için dosya indirildi!`)
}
function remove(id: number) {
  history.value = history.value.filter(item => item.id !== id)
}
</script>

<style scoped>
.segment-export-history {
  padding: 2rem;
  max-width: 600px;
  margin: 2rem auto;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}
th, td {
  padding: 0.5rem;
  border: 1px solid #eee;
  text-align: left;
}
button {
  margin-right: 0.5rem;
  background: #4A90E2;
  color: #fff;
  border: none;
  padding: 0.3rem 0.7rem;
  border-radius: 4px;
  cursor: pointer;
}
button:last-child {
  background: #e53e3e;
}
.empty {
  text-align: center;
  color: #888;
  font-style: italic;
}
</style>