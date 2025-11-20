<template>
  <div class="admin-user-list">
    <h2>Kullanıcılar</h2>
    <ul>
      <li v-for="user in users" :key="user.id">
        <span>{{ user.name }}</span> - <span>{{ user.email }}</span>
      </li>
    </ul>
  </div>
</template>
<script setup>
import { ref } from 'vue';
const users = ref([
  { id: 1, name: 'Ali', email: 'ali@example.com' },
  { id: 2, name: 'Veli', email: 'veli@example.com' }
]);
</script>
<style scoped>
.admin-user-list { max-width: 600px; margin: auto; }
</style>