<template>
  <div class="space-y-8">
    <SellerDashboard />
    <SellerPerformance />
    <SellerPerformanceChart />
    <CampaignConversionChart />
    <CampaignConversionExport />
    <FeedbackSummary />
    <CampaignFeedbackSummary />
    <CampaignFeedbackExport />
    <CampaignScoreChart />
    <CampaignRecommendation />
    <CampaignRestart />
    <CampaignRestartLog />
    <CampaignImpactChart />
    <CampaignSuggestionList />
    <CampaignPlanner />
  </div>
</template>

<script setup>
import SellerDashboard from '@/components/seller/SellerDashboard.vue'
import SellerPerformance from '@/components/seller/SellerPerformance.vue'
import SellerPerformanceChart from '@/components/seller/SellerPerformanceChart.vue'
import CampaignConversionChart from '@/components/seller/CampaignConversionChart.vue'
import CampaignConversionExport from '@/components/seller/CampaignConversionExport.vue'
import FeedbackSummary from '@/components/seller/FeedbackSummary.vue'
import CampaignFeedbackSummary from '@/components/seller/CampaignFeedbackSummary.vue'
import CampaignFeedbackExport from '@/components/seller/CampaignFeedbackExport.vue'
import CampaignScoreChart from '@/components/seller/CampaignScoreChart.vue'
import CampaignRecommendation from '@/components/seller/CampaignRecommendation.vue'
import CampaignRestart from '@/components/seller/CampaignRestart.vue'
import CampaignRestartLog from '@/components/seller/CampaignRestartLog.vue'
import CampaignImpactChart from '@/components/seller/CampaignImpactChart.vue'
import CampaignSuggestionList from '@/components/seller/CampaignSuggestionList.vue'
import CampaignPlanner from '@/components/seller/CampaignPlanner.vue'
</script>