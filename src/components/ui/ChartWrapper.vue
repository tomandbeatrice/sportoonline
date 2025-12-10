<template>
  <Suspense>
    <template #default>
      <component :is="asyncComp" v-bind="componentProps" />
    </template>
    <template #fallback>
      <LoadingSkeleton :variant="variantFallback" />
    </template>
  </Suspense>
</template>

<script setup lang="ts">
import { defineAsyncComponent, computed } from 'vue'
import LoadingSkeleton from '@/components/ui/LoadingSkeleton.vue'

const props = defineProps({
  loader: { type: Function as unknown as () => Promise<any>, required: true },
  componentProps: { type: Object as () => Record<string, any>, default: () => ({}) },
  variantFallback: { type: String as () => 'chart' | 'card' | 'list', default: 'chart' }
})

const asyncComp = defineAsyncComponent(() => props.loader())
</script>
