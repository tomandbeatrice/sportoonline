<template>
  <SwipeTabs :tabs="tabs" />
</template>

<script setup lang="ts">
import SwipeTabs from '@/components/SwipeTabs.vue'
import { useMobileTabs } from '@/composables/mobileTabs'

const tabs = useMobileTabs()
</script>