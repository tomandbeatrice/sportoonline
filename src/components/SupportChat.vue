<template>
  <div>
    <h3>Canlı Destek</h3>
    <ul>
      <li v-for="msg in messages" :key="msg.id">{{ msg.user.name }}: {{ msg.text }}</li>
    </ul>
    <input v-model="text" @keyup.enter="send" placeholder="Mesaj yaz..." />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const messages = ref([])
const text = ref('')

function send() {
  axios.post('/api/support/send', { message: text.value })
  text.value = ''
}
</script>