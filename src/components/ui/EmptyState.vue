<template>
  <div class="flex flex-col items-center justify-center py-12 text-center" role="status" aria-live="polite">
    <div class="mb-4">
      <slot name="icon">
        <span class="text-5xl">🗒️</span>
      </slot>
    </div>
    <h2 class="text-lg font-semibold text-slate-700 mb-2">
      <slot name="title">Kayıt bulunamadı</slot>
    </h2>
    <p class="text-sm text-slate-500 mb-4">
      <slot name="description">Henüz veri yok veya arama kriterlerine uygun sonuç bulunamadı.</slot>
    </p>
    <slot name="action" />
  </div>
</template>

<script setup lang="ts">
// No props, all content via slots
</script>
