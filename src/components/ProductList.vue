<template>
  <div class="product-list">
    <ProductCard 
      v-for="item in products" 
      :key="item.id" 
      :product="item"
      @add-to-cart="$emit('add-to-cart', $event)"
    />
  </div>
</template>

<script setup lang="ts">
import ProductCard from './ProductCard.vue'
defineProps<{ products: Array<any> }>()
defineEmits(['add-to-cart'])
</script>

<style scoped>
.product-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
  padding: 2rem;
}

@media (max-width: 768px) {
  .product-list {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
  }
}
</style>