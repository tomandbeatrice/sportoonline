<template>
  <div 
    ref="scrollContainer" 
    class="virtual-scroll-container" 
    :style="{ height: containerHeight }"
    @scroll="handleScroll"
  >
    <div 
      class="virtual-scroll-spacer" 
      :style="{ height: `${totalHeight}px` }"
    >
      <div 
        class="virtual-scroll-content" 
        :style="{ transform: `translateY(${offsetY}px)` }"
      >
        <slot 
          v-for="item in visibleItems" 
          :key="getItemKey(item)"
          :item="item"
        ></slot>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface Props {
  items: any[]
  itemHeight: number
  containerHeight?: string
  buffer?: number
  keyField?: string
}

const props = withDefaults(defineProps<Props>(), {
  containerHeight: '600px',
  buffer: 3,
  keyField: 'id'
})

const scrollContainer = ref<HTMLElement | null>(null)
const scrollTop = ref(0)

const totalHeight = computed(() => props.items.length * props.itemHeight)

const visibleStart = computed(() => {
  return Math.max(0, Math.floor(scrollTop.value / props.itemHeight) - props.buffer)
})

const visibleEnd = computed(() => {
  const container = scrollContainer.value
  if (!container) return props.buffer * 2

  const visibleCount = Math.ceil(container.clientHeight / props.itemHeight)
  return Math.min(
    props.items.length,
    visibleStart.value + visibleCount + props.buffer
  )
})

const visibleItems = computed(() => {
  return props.items.slice(visibleStart.value, visibleEnd.value)
})

const offsetY = computed(() => {
  return visibleStart.value * props.itemHeight
})

const getItemKey = (item: any) => {
  return item[props.keyField] || item.id
}

const handleScroll = (event: Event) => {
  scrollTop.value = (event.target as HTMLElement).scrollTop
}

onMounted(() => {
  if (scrollContainer.value) {
    scrollContainer.value.addEventListener('scroll', handleScroll, { passive: true })
  }
})

onUnmounted(() => {
  if (scrollContainer.value) {
    scrollContainer.value.removeEventListener('scroll', handleScroll)
  }
})
</script>

<style scoped>
.virtual-scroll-container {
  overflow-y: auto;
  position: relative;
}

.virtual-scroll-spacer {
  position: relative;
  width: 100%;
}

.virtual-scroll-content {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  will-change: transform;
}
</style>
