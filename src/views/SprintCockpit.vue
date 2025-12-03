<template>
  <div class="cockpit-container">
    <h2>🏁 Sprint Cockpit - Kampanyalı Ürünler</h2>

    <section class="products-section">
      <h3>Ürünler</h3>
      <ProductList :products="products" @add-to-cart="handleAddToCart" />
      <div v-if="loading" class="loading">Yükleniyor...</div>
      <div v-if="!loading && products.length === 0" class="no-products">
        Henüz ürün bulunmuyor.
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ProductList from '@/components/ProductList.vue'
import axios from 'axios'

const products = ref([])
const loading = ref(false)

const loadProducts = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/products')
    products.value = data.products?.data || data.products || []
  } catch (error) {
    console.error('Failed to load products:', error)
  } finally {
    loading.value = false
  }
}

const handleAddToCart = async (product: any) => {
  try {
    await axios.post('/api/cart', {
      product_id: product.id,
      quantity: 1
    })
    alert(`${product.name} sepete eklendi!`)
  } catch (error) {
    console.error('Failed to add to cart:', error)
    alert('Sepete eklenirken bir hata oluştu')
  }
}

onMounted(() => {
  loadProducts()
})
</script>

<style scoped>
.cockpit-container {
  padding: 2rem;
  background: var(--card, #ffffff);
  border-radius: 8px;
  max-width: 1400px;
  margin: 0 auto;
}

.cockpit-container h2 {
  font-size: 1.75rem;
  font-weight: bold;
  color: #111827;
  margin-bottom: 2rem;
  text-align: center;
}

.products-section {
  margin-top: 2rem;
}

.products-section h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.loading, .no-products {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1.125rem;
}

.categories, .products {
  margin-top: 2rem;
}

.product-card {
  display: inline-block;
  width: 150px;
  margin: 1rem;
  text-align: center;
}

.product-card img {
  width: 100%;
  border-radius: 6px;
}
</style>