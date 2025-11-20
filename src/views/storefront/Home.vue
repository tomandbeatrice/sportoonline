<template>
  <div class="home">
    <h2>Vitrin</h2>
    <ul>
      <li v-for="product in products" :key="product.id">
        <router-link :to="`/product/${product.id}`">{{ product.name }}</router-link>
      </li>
    </ul>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
const products = ref([
  { id: 1, name: 'Laptop' },
  { id: 2, name: 'Telefon' },
  { id: 3, name: 'Kulaklık' }
])
</script>
<style scoped>
.home { max-width: 600px; margin: 2rem auto; }
ul { list-style: none; padding: 0; }
li { margin-bottom: 8px; }
</style>