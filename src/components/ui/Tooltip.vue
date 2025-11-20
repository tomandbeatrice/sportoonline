<template>
  <div class="relative inline-block" ref="triggerRef">
    <div
      @mouseenter="show"
      @mouseleave="hide"
      @focus="show"
      @blur="hide"
    >
      <slot></slot>
    </div>

    <Teleport to="body">
      <Transition name="tooltip">
        <div
          v-if="visible"
          ref="tooltipRef"
          :class="tooltipClasses"
          :style="tooltipStyle"
          role="tooltip"
        >
          <slot name="content">{{ content }}</slot>
          <div :class="arrowClasses" :style="arrowStyle"></div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface Props {
  content?: string
  placement?: 'top' | 'bottom' | 'left' | 'right'
  delay?: number
  offset?: number
}

const props = withDefaults(defineProps<Props>(), {
  placement: 'top',
  delay: 200,
  offset: 8
})

const visible = ref(false)
const triggerRef = ref<HTMLElement | null>(null)
const tooltipRef = ref<HTMLElement | null>(null)
const position = ref({ top: 0, left: 0 })

let showTimeout: ReturnType<typeof setTimeout> | null = null

const tooltipClasses = computed(() => {
  return [
    'absolute z-50 px-3 py-2 text-sm text-white bg-slate-900 rounded-lg shadow-lg',
    'max-w-xs break-words pointer-events-none'
  ].join(' ')
})

const arrowClasses = computed(() => {
  const base = 'absolute w-2 h-2 bg-slate-900 transform rotate-45'
  const placements = {
    top: 'bottom-[-4px] left-1/2 -translate-x-1/2',
    bottom: 'top-[-4px] left-1/2 -translate-x-1/2',
    left: 'right-[-4px] top-1/2 -translate-y-1/2',
    right: 'left-[-4px] top-1/2 -translate-y-1/2'
  }
  return [base, placements[props.placement]].join(' ')
})

const tooltipStyle = computed(() => {
  return {
    top: `${position.value.top}px`,
    left: `${position.value.left}px`
  }
})

const arrowStyle = computed(() => {
  return {}
})

const updatePosition = () => {
  if (!triggerRef.value || !tooltipRef.value) return

  const triggerRect = triggerRef.value.getBoundingClientRect()
  const tooltipRect = tooltipRef.value.getBoundingClientRect()

  let top = 0
  let left = 0

  switch (props.placement) {
    case 'top':
      top = triggerRect.top - tooltipRect.height - props.offset
      left = triggerRect.left + (triggerRect.width - tooltipRect.width) / 2
      break
    case 'bottom':
      top = triggerRect.bottom + props.offset
      left = triggerRect.left + (triggerRect.width - tooltipRect.width) / 2
      break
    case 'left':
      top = triggerRect.top + (triggerRect.height - tooltipRect.height) / 2
      left = triggerRect.left - tooltipRect.width - props.offset
      break
    case 'right':
      top = triggerRect.top + (triggerRect.height - tooltipRect.height) / 2
      left = triggerRect.right + props.offset
      break
  }

  position.value = { top, left }
}

const show = () => {
  if (showTimeout) clearTimeout(showTimeout)
  
  showTimeout = setTimeout(() => {
    visible.value = true
    setTimeout(updatePosition, 0)
  }, props.delay)
}

const hide = () => {
  if (showTimeout) {
    clearTimeout(showTimeout)
    showTimeout = null
  }
  visible.value = false
}

onMounted(() => {
  window.addEventListener('scroll', updatePosition, { passive: true })
  window.addEventListener('resize', updatePosition)
})

onUnmounted(() => {
  window.removeEventListener('scroll', updatePosition)
  window.removeEventListener('resize', updatePosition)
  if (showTimeout) clearTimeout(showTimeout)
})
</script>

<style scoped>
.tooltip-enter-active,
.tooltip-leave-active {
  transition: opacity 0.2s ease;
}

.tooltip-enter-from,
.tooltip-leave-to {
  opacity: 0;
}
</style>
