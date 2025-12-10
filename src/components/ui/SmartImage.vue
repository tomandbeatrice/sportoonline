<template>
  <div class="relative overflow-hidden bg-slate-100" :class="containerClass">
    <!-- Placeholder / Skeleton -->
    <div 
      v-if="loading" 
      class="absolute inset-0 animate-pulse bg-slate-200"
    ></div>

    <!-- Actual Image -->
    <img
      v-show="!error"
      ref="imageRef"
      :src="src"
      :alt="alt"
      :class="[
        'h-full w-full object-cover transition-opacity duration-300',
        loading ? 'opacity-0' : 'opacity-100',
        imageClass
      ]"
      loading="lazy"
      @load="onLoad"
      @error="onError"
    />

    <!-- Error State -->
    <div 
      v-if="error" 
      class="absolute inset-0 flex items-center justify-center bg-slate-50 text-slate-400"
    >
      <span class="text-2xl">🖼️</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  src?: string
  alt?: string
  containerClass?: string
  imageClass?: string
  fallbackSrc?: string
}>()

const loading = ref(true)
const error = ref(false)
const imageRef = ref<HTMLImageElement | null>(null)

const onLoad = () => {
  loading.value = false
}

const onError = () => {
  loading.value = false
  error.value = true
  if (props.fallbackSrc && imageRef.value) {
    imageRef.value.src = props.fallbackSrc
    error.value = false // Reset error if fallback loads
  }
}

// Reset state if src changes
watch(() => props.src, () => {
  loading.value = true
  error.value = false
})
</script>
