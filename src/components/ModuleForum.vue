<template>
  <ForumThread :modulId="activeModul" />
</template>

<script setup lang="ts">
import ForumThread from '@/components/ForumThread.vue'
const activeModul = 'dashboard-analytics'
</script>