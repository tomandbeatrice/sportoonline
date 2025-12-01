<template>
  <div class="product-filter mb-6">
    <div class="grid grid-cols-4 gap-4">
      <input 
        v-model="filters.search" 
        type="text" 
        placeholder="Ürün ara..." 
        class="border px-4 py-2 rounded"
        @input="applyFilters"
      />
      <select v-model="filters.category" class="border px-4 py-2 rounded" @change="applyFilters">
        <option value="">Tüm Kategoriler</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
      <select v-model="filters.sort" class="border px-4 py-2 rounded" @change="applyFilters">
        <option value="">Sıralama</option>
        <option value="newest">En Yeni</option>
        <option value="price_asc">Fiyat: Düşük → Yüksek</option>
        <option value="price_desc">Fiyat: Yüksek → Düşük</option>
      </select>
      <div class="flex gap-2">
        <input 
          v-model="filters.min_price" 
          type="number" 
          placeholder="Min Fiyat" 
          class="border px-2 py-2 rounded w-1/2"
          @input="applyFilters"
        />
        <input 
          v-model="filters.max_price" 
          type="number" 
          placeholder="Max Fiyat" 
          class="border px-2 py-2 rounded w-1/2"
          @input="applyFilters"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue';

const emit = defineEmits(['filter']);

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  }
});

const filters = ref({
  search: '',
  category: '',
  sort: '',
  min_price: '',
  max_price: ''
});

function applyFilters() {
  emit('filter', filters.value);
}
</script>
