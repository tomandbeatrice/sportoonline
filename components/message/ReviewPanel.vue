<template>
  <div class="review-panel">
    <h2>Değerlendirmeler</h2>
    <ul>
      <li v-for="review in reviews" :key="review.id">
        <span>{{ review.user }}:</span> <span>{{ review.comment }}</span> <span>({{ review.rating }}/5)</span>
      </li>
    </ul>
    <form @submit.prevent="addReview">
      <input v-model="newComment" type="text" placeholder="Yorum yaz..." />
      <input v-model="newRating" type="number" min="1" max="5" placeholder="Puan" />
      <button type="submit">Ekle</button>
    </form>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const reviews = ref([]);
const newComment = ref('');
const newRating = ref(5);
const productId = ref(1); // Dinamik olarak props veya route'dan gelmeli

onMounted(async () => {
  try {
    const res = await axios.get('/api/reviews', { params: { product_id: productId.value } });
    reviews.value = res.data;
  } catch (e) {
    console.error('Değerlendirmeler yüklenemedi:', e);
  }
});

async function addReview() {
  try {
    const res = await axios.post('/api/reviews', {
      product_id: productId.value,
      rating: newRating.value,
      comment: newComment.value
    });
    reviews.value.push(res.data);
    newComment.value = '';
    newRating.value = 5;
  } catch (e) {
    alert('Yorum eklenemedi: ' + e.message);
  }
}
</script>
<style scoped>
.review-panel { max-width: 600px; margin: auto; }
</style>