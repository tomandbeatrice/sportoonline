<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Top Header -->
    <UnifiedHeader 
      :unreadNotifications="unreadNotifications"
      @toggle-notifications="showNotifications = !showNotifications"
    />

    <div class="container mx-auto px-6 py-8">
      <!-- Quick Stats Overview -->
      <UnifiedQuickStats />

      <!-- Main Control Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Module Hub -->
        <UnifiedModuleHub @open-module="openModuleModal" />

        <!-- Activity Feed -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Son Aktiviteler</h2>
          <div class="space-y-3 max-h-[600px] overflow-y-auto">
            <ActivityItem
              v-for="activity in recentActivities"
              :key="activity.id"
              :activity="activity"
            />
          </div>
        </div>
      </div>

      <!-- Integrated Workflows -->
      <UnifiedWorkflows />

      <!-- Campaign Performance Dashboard -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Aktif Kampanyalar</h2>
          <CampaignList :campaigns="activeCampaigns" @manage="manageCampaign" />
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Performans Özeti</h2>
          <PerformanceChart :data="performanceData" />
        </div>
      </div>

      <!-- Seller & Order Management Integration -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Satıcı Yönetimi</h2>
          <SellerOverview :sellers="topSellers" @viewAll="router.push('/admin/seller-applications')" />
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Bekleyen Siparişler</h2>
          <OrderQueue :orders="pendingOrders" @process="processOrder" />
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <UnifiedQuickActions />

    <!-- Modals -->
    <ModuleModal
      v-if="activeModule"
      :module="activeModule"
      @close="activeModule = null"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUnifiedDashboardStore } from '@/stores/unifiedDashboardStore'

// Components
import UnifiedHeader from '@/components/unified/UnifiedHeader.vue'
import UnifiedQuickStats from '@/components/unified/UnifiedQuickStats.vue'
import UnifiedModuleHub from '@/components/unified/UnifiedModuleHub.vue'
import UnifiedWorkflows from '@/components/unified/UnifiedWorkflows.vue'
import UnifiedQuickActions from '@/components/unified/UnifiedQuickActions.vue'
import ActivityItem from '@/components/unified/ActivityItem.vue'
import CampaignList from '@/components/unified/CampaignList.vue'
import PerformanceChart from '@/components/unified/PerformanceChart.vue'
import SellerOverview from '@/components/unified/SellerOverview.vue'
import OrderQueue from '@/components/unified/OrderQueue.vue'
import ModuleModal from '@/components/unified/ModuleModal.vue'

const router = useRouter()
const store = useUnifiedDashboardStore()

// State
const showNotifications = ref(false)
const activeModule = ref(null)
const unreadNotifications = ref(5)

// Store Data
const recentActivities = computed(() => store.recentActivities)
const activeCampaigns = computed(() => store.activeCampaigns)
const performanceData = computed(() => store.performanceData)
const topSellers = computed(() => store.topSellers)
const pendingOrders = computed(() => store.pendingOrders)

onMounted(() => {
  store.loadDashboardData()
})

function openModuleModal(module: any) {
  activeModule.value = module
}

function manageCampaign(campaign: any) {
  router.push(`/admin/campaigns/${campaign.id}`)
}

function processOrder(order: any) {
  router.push(`/admin/orders/${order.id}`)
}
</script>
