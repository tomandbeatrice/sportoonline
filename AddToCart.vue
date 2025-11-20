<template>
  <div>
    <select v-model="selectedVariant">
      <option v-for="variant in product.variants" :value="variant.id">
        {{ variant.name }} - {{ variant.price }}₺
      </option>
    </select>
    <button @click="addToCart">Sepete Ekle</button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const selectedVariant = ref(null)
const product = defineProps(['product'])

const addToCart = async () => {
  await axios.post('/api/cart', {
    variant_id: selectedVariant.value,
    quantity: 1
  })
}
</script>