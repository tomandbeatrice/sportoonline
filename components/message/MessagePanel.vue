<template>
  <div class="message-panel">
    <h2>Mesajlar</h2>
    <ul>
      <li v-for="msg in messages" :key="msg.id">
        <span>{{ msg.from }}:</span> <span>{{ msg.text }}</span>
      </li>
    </ul>
    <form @submit.prevent="sendMessage">
      <input v-model="newMessage" type="text" placeholder="Mesaj yaz..." />
      <button type="submit">Gönder</button>
    </form>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const messages = ref([]);
const newMessage = ref('');

onMounted(async () => {
  try {
    const res = await axios.get('/api/messages');
    messages.value = res.data;
  } catch (e) {
    console.error('Mesajlar yüklenemedi:', e);
  }
});

async function sendMessage() {
  try {
    const res = await axios.post('/api/messages', {
      to_id: 1, // Hedef kullanıcı ID'si (dinamik olmalı)
      body: newMessage.value
    });
    messages.value.push(res.data);
    newMessage.value = '';
  } catch (e) {
    alert('Mesaj gönderilemedi: ' + e.message);
  }
}
</script>
<style scoped>
.message-panel { max-width: 600px; margin: auto; }
</style>