<template>
  <div :class="props.class">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { provide, ref, watch } from 'vue'
import { tabsKey } from './tokens'

const props = withDefaults(
  defineProps<{ defaultValue: string; class?: string; modelValue?: string }>(),
  { class: '', modelValue: undefined }
)

const internalValue = ref(props.modelValue ?? props.defaultValue)

watch(
  () => props.modelValue,
  value => {
    if (value) internalValue.value = value
  }
)

function setValue(value: string) {
  internalValue.value = value
}

provide(tabsKey, { activeValue: internalValue, setValue })
</script>
