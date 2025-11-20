<template>
  <div class="product-detail" v-if="product">
    <h2>{{ product.name }}</h2>
    <img :src="product.image" alt="Ürün görseli" class="product-image" />
    <p>{{ product.description }}</p>
    <div>Stok: {{ product.stock }}</div>
    <div>Fiyat: {{ product.price }}₺</div>
    <button @click="addToCart(product.id)">Sepete Ekle</button>
  </div>
  <div v-else>
    <p>Ürün bulunamadı.</p>
  </div>
</template>

<script setup lang="ts">
// filepath: c:\Users\sport\Desktop\sportoonline\src\views\product\ProductDetail.vue
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const product = ref<any>(null)

onMounted(async () => {
  // Örnek API isteği (gerçek API ile değiştirilebilir)
  // const res = await fetch(`/api/products/${route.params.id}`)
  // product.value = await res.json()

  // Demo veri (API yoksa)
  if (route.params.id === '1') {
    product.value = {
      id: 1,
      name: 'Laptop',
      image: '/assets/laptop.jpg',
      description: 'Yüksek performanslı laptop.',
      stock: 5,
      price: 12000
    }
  } else {
    product.value = null
  }
})

function addToCart(id: number) {
  // Sepete ekleme işlemi
  alert(`Ürün sepete eklendi: ${id}`)
}
</script>

<style scoped>
.product-detail { padding: 2rem; }
.product-image { width: 200px; height: auto; margin-bottom: 1rem; }
</style>