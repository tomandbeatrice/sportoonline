<template>
  <div class="product-form">
    <h2>Ürün Ekle/Düzenle</h2>
    <form @submit.prevent="saveProduct">
      <input v-model="title" type="text" placeholder="Ürün Adı" required />
      <textarea v-model="description" placeholder="Açıklama"></textarea>
      <input v-model="price" type="number" placeholder="Fiyat" required />
      <input v-model="category" type="text" placeholder="Kategori" />
      <input v-model="stock" type="number" placeholder="Stok" />
      <button type="submit">Kaydet</button>
    </form>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import axios from 'axios';

const title = ref('');
const description = ref('');
const price = ref(0);
const category = ref('');
const stock = ref(0);

async function saveProduct() {
  try {
    const res = await axios.post('/api/products', {
      name: title.value,
      description: description.value,
      price: price.value,
      category_id: category.value,
      stock: stock.value
    });
    alert('Ürün kaydedildi!');
    // Formu temizle
    title.value = '';
    description.value = '';
    price.value = 0;
    category.value = '';
    stock.value = 0;
  } catch (e) {
    alert('Hata: ' + e.message);
  }
}
</script>
<style scoped>
.product-form { max-width: 500px; margin: auto; }
</style>