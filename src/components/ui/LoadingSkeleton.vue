<template>
  <div :class="containerClass" role="status" aria-live="polite">
    <template v-if="variant === 'list'">
      <div class="space-y-3">
        <div v-for="i in lines" :key="i" class="h-4 bg-slate-100 rounded-md animate-pulse" :style="{ width: `${80 - i * 6}%`, height: lineHeight }"></div>
      </div>
    </template>

    <template v-else-if="variant === 'card'">
      <div class="h-24 rounded-lg bg-slate-100 animate-pulse" />
    </template>

    <template v-else>
      <div class="h-48 rounded-lg bg-slate-100 animate-pulse" />
      <span class="sr-only">Yükleniyor</span>
    </template>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  variant: { type: String as () => 'list' | 'card' | 'chart', default: 'list' },
  lines: { type: Number, default: 4 },
  lineHeight: { type: String, default: '1rem' }
})

const containerClass = 'w-full flex items-center justify-center'
</script>
