<template>
  <div class="product-list">
    <h2>Ürünler</h2>
    <div class="products">
      <div v-for="p in products" :key="p.id" class="product-card">
        <img :src="p.image" alt="Ürün görseli" />
        <h3>{{ p.name }}</h3>
        <p>{{ p.price }}₺</p>
        <button @click="viewDetail(p.id)">Detay</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const products = ref([
  { id: 1, name: 'Laptop', price: 12000, image: '/assets/laptop.jpg' },
  { id: 2, name: 'Telefon', price: 8000, image: '/assets/phone.jpg' }
])

function viewDetail(id: number) {
  router.push(`/product/${id}`)
}
</script>

<style scoped>
.products { display: flex; gap: 1rem; flex-wrap: wrap; }
.product-card { background: #fff; border-radius: 8px; padding: 1rem; width: 180px; text-align: center; }
.product-card img { width: 100%; height: 100px; object-fit: cover; }
</style>