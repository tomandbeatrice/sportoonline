<template>
  <div class="category-list">
    <h2>Kategoriler</h2>
    <ul>
      <li v-for="cat in categories" :key="cat.id">
        {{ cat.name }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const categories = ref([
  { id: 1, name: 'Elektronik' },
  { id: 2, name: 'Giyim' },
  { id: 3, name: 'Ev & Yaşam' }
])
</script>

<style scoped>
.category-list { max-width: 320px; margin: 2rem auto; background: #fff; border-radius: 8px; padding: 2rem; }
ul { list-style: none; padding: 0; }
li { padding: 0.5rem 0; border-bottom: 1px solid #eee; }
</style>