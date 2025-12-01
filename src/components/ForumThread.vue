<template>
  <div class="forum-thread">
    <h4>Modül Öneri Alanı</h4>
    <CommentBox :modulId="modul.id" />
    <SuggestionList :modulId="modul.id" />
  </div>
</template>

<script setup lang="ts">
defineProps<{ modul: { id: string } }>()
import CommentBox from '@/components/CommentBox.vue'
import SuggestionList from '@/components/SuggestionList.vue'
</script>

<style scoped>
.forum-thread {
  background-color: var(--cardSoft);
  padding: 1rem;
  border-radius: 8px;
  margin-top: 0.5rem;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
}
</style>