<template>
  <div class="relative" :class="props.class">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { provide, ref } from 'vue'
import { carouselKey } from './tokens'

const props = withDefaults(defineProps<{ class?: string }>(), { class: '' })

const contentRef = ref<HTMLDivElement | null>(null)
const activeIndex = ref(0)

function registerContent(el: HTMLDivElement | null) {
  contentRef.value = el
}

function slideBy(direction: 'next' | 'prev') {
  const node = contentRef.value
  if (!node) return
  const width = node.firstElementChild?.clientWidth ?? 320
  const offset = direction === 'next' ? width : -width
  node.scrollBy({ left: offset, behavior: 'smooth' })
  const children = node.children.length
  if (!children) return
  const step = direction === 'next' ? 1 : -1
  activeIndex.value = (activeIndex.value + step + children) % children
}

provide(carouselKey, { registerContent, slideBy, activeIndex })
</script>
