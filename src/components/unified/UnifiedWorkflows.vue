<template>
  <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Hızlı İş Akışları</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <WorkflowCard
        v-for="workflow in workflows"
        :key="workflow.id"
        :workflow="workflow"
        @execute="executeWorkflow(workflow)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUnifiedDashboardStore } from '@/stores/unifiedDashboardStore'
import WorkflowCard from '@/components/unified/WorkflowCard.vue'

const router = useRouter()
const store = useUnifiedDashboardStore()
const workflows = computed(() => store.workflows)

function executeWorkflow(workflow: any) {
  router.push({
    name: 'WorkflowExecutor',
    params: { workflowId: workflow.id }
  })
}
</script>
