<template>
  <VotePanel :items="suggestions" @vote="handleVote" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import VotePanel from '@/components/VotePanel.vue'
import { fetchSuggestions, submitVote } from '@/services/teamEngine'

const suggestions = ref([])

onMounted(async () => {
  suggestions.value = await fetchSuggestions()
})

function handleVote(voteData) {
  submitVote(voteData)
}
</script>