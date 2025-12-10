<template>
  <div v-if="enabled">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { useFeatureToggle } from '@/composables/useFeatureToggle'
const props = defineProps<{ feature: string }>()
const enabled = useFeatureToggle(props.feature)
</script>
