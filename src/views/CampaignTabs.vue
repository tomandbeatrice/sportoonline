<template>
  <div class="tabs mb-6">
    <button
      v-for="tab in tabs"
      :key="tab"
      class="tab tab-bordered"
      :class="{ 'tab-active': activeTab === tab }"
      @click="activeTab = tab"
    >
      {{ tab }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

const tabs = ['Tümü', 'Aktif', 'Pasif'] as const
type TabType = typeof tabs[number]

const activeTab = ref<TabType>('Tümü')

const emit = defineEmits<{
  (e: 'filter', value: TabType): void
}>()

watch(activeTab, (val) => {
  emit('filter', val)
})
</script>

<style scoped>
.tabs {
  display: flex;
  gap: 1rem;
}
.tab {
  padding: 0.5rem 1rem;
  border: 1px solid #ccc;
  background-color: white;
  cursor: pointer;
}
.tab-active {
  border-bottom: 2px solid #3b82f6;
  font-weight: bold;
  color: #3b82f6;
}
</style>