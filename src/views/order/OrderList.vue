<template>
  <div class="order-list">
    <h2>Siparişlerim</h2>
    <table>
      <thead>
        <tr>
          <th>Tarih</th>
          <th>Durum</th>
          <th>Tutar</th>
          <th>İşlem</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td>{{ order.date }}</td>
          <td>{{ order.status }}</td>
          <td>{{ order.total }}₺</td>
          <td><button @click="view(order.id)">Detayları Gör</button></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const orders = ref([
  { id: 1, date: '2024-06-01', status: 'Teslim Edildi', total: 12000 },
  { id: 2, date: '2024-06-03', status: 'Hazırlanıyor', total: 8000 }
])

function view(id: number) {
  // Sipariş detayına yönlendirme işlemi
}
</script>

<style scoped>
.order-list { padding: 2rem; }
table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
th, td { padding: 0.5rem; border: 1px solid #eee; }
</style>