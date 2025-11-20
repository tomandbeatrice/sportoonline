<template>
  <div class="suggestions">
    <SuggestionCard v-for="s in suggestions" :key="s.id" :data="s" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import SuggestionCard from '@/components/SuggestionCard.vue'
import { getTechSuggestions } from '@/services/riskEngine'

const suggestions = ref([])

onMounted(async () => {
  suggestions.value = await getTechSuggestions()
})
</script>