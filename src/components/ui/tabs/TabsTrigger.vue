<template>
  <button
    type="button"
    class="rounded-2xl px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] transition"
    :class="isActive ? activeClass : inactiveClass"
    @click="activate"
  >
    <slot />
  </button>
</template>

<script setup lang="ts">
import { computed, inject } from 'vue'
import { tabsKey } from './tokens'

const props = withDefaults(
  defineProps<{
    value: string
    activeClass?: string
    inactiveClass?: string
  }>(),
  {
    activeClass: 'bg-emerald-500 text-white shadow-lg',
    inactiveClass: 'text-white/70'
  }
)

const tabs = inject(tabsKey)

const isActive = computed(() => tabs?.activeValue.value === props.value)

function activate() {
  tabs?.setValue(props.value)
}
</script>
