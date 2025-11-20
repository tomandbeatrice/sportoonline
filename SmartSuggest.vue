<template>
  <div class="smart-suggest">
    <SuggestionCard v-for="s in suggestions" :key="s.id" :data="s" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import SuggestionCard from '@/components/SuggestionCard.vue'
import { getAISuggestions } from '@/services/aiEngine'

const suggestions = ref([])

onMounted(async () => {
  suggestions.value = await getAISuggestions()
})
</script>