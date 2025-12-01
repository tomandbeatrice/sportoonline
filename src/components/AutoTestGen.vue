<template>
  <TestCard v-for="t in testCases" :key="t.id" :data="t" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import TestCard from '@/components/TestCard.vue'
import { generateTestCases } from '@/services/testAI'

const testCases = ref([])

onMounted(async () => {
  testCases.value = await generateTestCases()
})
</script>