<template>
  <SimplifyCard v-for="s in suggestions" :key="s.id" :data="s" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import SimplifyCard from '@/components/SimplifyCard.vue'
import { getUXSuggestions } from '@/services/uiAnalyzer'

type Suggestion = { id: number; [key: string]: any }
const suggestions = ref<Suggestion[]>([])

onMounted(async () => {
  suggestions.value = await getUXSuggestions()
})
</script>