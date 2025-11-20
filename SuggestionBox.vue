<template>
  <div class="suggestion-box">
    <h4>💡 Sprint Önerileri</h4>
    <ul>
      <li v-for="suggestion in suggestions" :key="suggestion.id">
        {{ suggestion.text }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const suggestions = ref([])

onMounted(async () => {
  const res = await fetch('/api/suggestions')
  suggestions.value = await res.json()
})
</script>

<style scoped>
.suggestion-box {
  background-color: #fff;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}
</style>