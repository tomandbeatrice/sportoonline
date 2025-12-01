<template>
  <div class="export-filter">
    <DateRangePicker v-model="dateRange" />
    <ModuleSelector v-model="selectedModules" />
    <RiskSlider v-model="riskThreshold" />
    <ExportButton :filters="{ dateRange, selectedModules, riskThreshold }" />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import DateRangePicker from '@/components/DateRangePicker.vue'
import ModuleSelector from '@/components/ModuleSelector.vue'
import RiskSlider from '@/components/RiskSlider.vue'
import ExportButton from '@/components/ExportButton.vue'

const dateRange = ref({ start: '', end: '' })
const selectedModules = ref([])
const riskThreshold = ref(50)
</script>