<template>
  <div class="dashboard-container space-y-8">
    <!-- Sprint Özet Paneli -->
    <SprintSummary />

    <!-- Roadmap İlerleme Grafiği -->
    <RoadmapProgress :modules="sprint.modules" />

    <!-- Geçmiş Sprintler -->
    <SprintHistory />

    <!-- PDF Raporlama -->
    <SprintReport />

    <!-- Sekmeli Modül Analizi -->
    <TabGroup :tabs="tabs" v-model="activeTab" />

    <div v-if="activeTab === 'Kullanıcı Yoğunluğu'">
      <UserHeatmap :data="tabData['Kullanıcı Yoğunluğu']" />
    </div>

    <div v-else-if="activeTab === 'Modül Trendleri'">
      <ModulTrend :data="tabData['Modül Trendleri']" />
    </div>

    <div v-else-if="activeTab === 'Log Detayı'" class="space-y-8">
      <ModulAnaliz :logs="tabData['Log Detayı']" />
      <LogHeatmap :logs="tabData['Log Detayı']" @select="handleLogSelection" />
      <LogDetail v-if="selectedLogs.length" :logs="selectedLogs" />
    </div>

    <div class="export-buttons">
      <ExportButton format="pdf" :data="tabData[activeTab]" />
      <ExportButton format="csv" :data="tabData[activeTab]" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import TabGroup from '@/components/TabGroup.vue'
import UserHeatmap from '@/components/UserHeatmap.vue'
import ModulTrend from '@/components/ModulTrend.vue'
import ModulAnaliz from '@/components/ModulAnaliz.vue'
import LogHeatmap from '@/components/LogHeatmap.vue'
import LogDetail from '@/components/LogDetail.vue'
import ExportButton from '@/components/ExportButton.vue'
import SprintSummary from '@/components/SprintSummary.vue'
import RoadmapProgress from '@/components/RoadmapProgress.vue'
import SprintHistory from '@/components/SprintHistory.vue'
import SprintReport from '@/components/SprintReport.vue'
import { useSprintStore } from '@/stores/sprint'

const sprint = useSprintStore()

const tabs = ['Kullanıcı Yoğunluğu', 'Modül Trendleri', 'Log Detayı']
const activeTab = ref(tabs[0])

const tabData = {
  'Kullanıcı Yoğunluğu': [],
  'Modül Trendleri': [],
  'Log Detayı': []
}

const selectedLogs = ref<Array<any>>([])
function handleLogSelection(logs: Array<any>) {
  selectedLogs.value = logs
}
</script>

<style scoped>
.dashboard-container {
  padding: 1rem;
}
.export-buttons {
  margin-top: 1rem;
  display: flex;
  gap: 0.5rem;
}
</style>