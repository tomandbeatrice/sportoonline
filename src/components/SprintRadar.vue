<template>
  <div class="radar">
    <h3>Modül Başarı Radar</h3>
    <ul>
      <li v-for="mod in modules" :key="mod.id">
        <strong>{{ mod.name }}</strong>: {{ mod.successRate }}%
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
defineProps<{ modules: { id: string; name: string; successRate: number }[] }>()
</script>

<style scoped>
.radar {
  background: #132A4D;
  padding: 1rem;
  border-radius: 8px;
  width: 250px;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  margin-bottom: 0.5rem;
}
</style>