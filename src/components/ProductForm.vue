<template>
  <form @submit.prevent="submit">
    <input v-model="product.name" placeholder="Ürün Adı" required />
    <textarea v-model="product.description" placeholder="Açıklama"></textarea>
    <input v-model="product.price" type="number" placeholder="Fiyat" required />
    <input v-model="product.stock" type="number" placeholder="Stok" required />
    <input v-model="product.category" placeholder="Kategori" />
    
    <!-- Görsel URL veya Dosya Yükleme -->
    <input v-model="product.imageUrl" placeholder="Görsel URL (isteğe bağlı)" />
    <input type="file" @change="handleImage" />

    <button type="submit">Ürünü Kaydet</button>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const product = ref({
  name: '',
  description: '',
  price: 0,
  stock: 0,
  category: '',
  imageUrl: ''
})

const imageFile = ref(null)

function handleImage(e) {
  imageFile.value = e.target.files[0]
}

async function submit() {
  const formData = new FormData()
  for (const key in product.value) {
    formData.append(key, product.value[key])
  }
  if (imageFile.value) {
    formData.append('image', imageFile.value)
  }

  try {
    await axios.post('/api/products', formData)
    alert('Ürün başarıyla eklendi')
  } catch (err) {
    console.error('Hata:', err)
  }
}
</script>