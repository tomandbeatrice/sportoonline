<template>
  <div class="admin">
    <h2>Admin Paneli</h2>
    <input v-model="panelName" placeholder="Panel adı girin" />
    <button @click="updatePanel">Güncelle</button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const panelName = ref('Yusuf')

function updatePanel() {
  console.log('Yeni panel:', panelName.value)
  // İleride: API ile storage’a yazılabilir
}
</script>