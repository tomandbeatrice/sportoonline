<template>
  <div class="live-order">
    <h2>Canlı Sipariş Paneli</h2>
    <div v-if="orders.length">
      <div v-for="order in orders" :key="order.id" class="order-card">
        <p><strong>Ad:</strong> {{ order.name }}</p>
        <p><strong>Durum:</strong> {{ order.status }}</p>
        <p><strong>Tutar:</strong> {{ order.total_price }}₺</p>
      </div>
    </div>
    <div v-else>
      <p>Henüz sipariş yok.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LiveOrder',
  data() {
    return {
      orders: []
    }
  },
  mounted() {
    fetch('/api/orders/live')
      .then(res => res.json())
      .then(data => {
        this.orders = data
      })
      .catch(() => {
        this.orders = []
      })
  }
}
</script>

<style scoped>
.live-order {
  padding: 20px;
  background: #f4f4f4;
}
.order-card {
  margin-bottom: 15px;
  padding: 10px;
  background: white;
  border: 1px solid #ccc;
}
</style>