<template>
  <div v-show="isActive" :class="props.class">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { computed, inject } from 'vue'
import { tabsKey } from './tokens'

const props = withDefaults(
  defineProps<{ value: string; class?: string }>(),
  { class: '' }
)

const tabs = inject(tabsKey)
const isActive = computed(() => tabs?.activeValue.value === props.value)
</script>
