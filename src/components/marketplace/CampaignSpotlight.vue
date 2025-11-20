<template>
  <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 p-8 text-white">
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute -left-8 top-4 h-40 w-40 rounded-full bg-emerald-500/15 blur-3xl"></div>
      <div class="absolute -right-10 top-10 h-44 w-44 rounded-full bg-orange-400/15 blur-3xl"></div>
      <div class="absolute inset-x-10 bottom-6 h-12 rounded-full bg-white/10 blur-2xl"></div>
    </div>

    <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.4em] text-white/60">Kampanya Akışı</p>
        <h2 class="mt-2 text-3xl font-black">Canlı & Planlanan Kampanyalar</h2>
        <p class="mt-2 text-sm text-white/70">Shadcn Tabs + Accordion ile tüm kampanya detaylarını tek bakışta yönet.</p>
      </div>
      <RouterLink
        to="/campaigns"
        class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white hover:bg-white/10"
      >
        Tüm kampanyalar
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7 7 7-7 7" />
        </svg>
      </RouterLink>
    </div>

    <Tabs class="relative mt-8 space-y-6" :default-value="defaultTab">
      <TabsList class="flex w-full rounded-2xl border border-white/10 bg-white/5 p-1">
        <TabsTrigger
          value="live"
          :active-class="'bg-gradient-to-r from-emerald-400 to-lime-400 text-slate-900 shadow-md'
          "
          inactive-class="text-white/70"
        >
          Canlı Kampanyalar ({{ liveCampaigns.length }})
        </TabsTrigger>
        <TabsTrigger
          value="planned"
          :active-class="'bg-gradient-to-r from-orange-400 to-amber-400 text-slate-900 shadow-md'
          "
          inactive-class="'text-white/70'"
        >
          Planlanan Kampanyalar ({{ plannedCampaigns.length }})
        </TabsTrigger>
      </TabsList>

      <TabsContent value="live">
        <Accordion type="single" class="space-y-4" :default-value="undefined">
          <AccordionItem
            v-for="(campaign, index) in liveCampaigns"
            :key="campaign.title + index"
            :value="campaign.title + index"
            class="border-white/15 bg-white/5 text-white"
          >
            <div class="p-5">
              <AccordionTrigger class="text-left">
                <div class="flex flex-col gap-1">
                  <div class="flex flex-wrap items-center gap-2 text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-white/70">
                    <span class="rounded-full bg-white/10 px-3 py-1 text-white">{{ campaign.badge }}</span>
                    <span :class="statusToneClass(campaign.statusTone)">{{ campaign.status || 'Canlı' }}</span>
                    <span :class="liftToneClass(campaign.lift)">{{ campaign.lift || '+0%' }}</span>
                  </div>
                  <h3 class="text-xl font-black">{{ campaign.title }}</h3>
                  <p class="text-xs text-white/70">{{ campaign.window }} · {{ campaign.channel || 'Marketplace' }}</p>
                </div>
              </AccordionTrigger>
              <AccordionContent>
                <div class="space-y-4 text-sm text-white/80">
                  <p>{{ campaign.copy }}</p>
                  <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                      <p class="text-xs uppercase tracking-[0.35em] text-white/50">İndirim</p>
                      <p class="text-lg font-semibold text-white">{{ campaign.discount }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                      <p class="text-xs uppercase tracking-[0.35em] text-white/50">Kitle</p>
                      <p class="text-lg font-semibold text-white">{{ campaign.reach || '—' }}</p>
                    </div>
                  </div>
                  <div v-if="campaign.metrics?.length" class="grid gap-3 md:grid-cols-2">
                    <div
                      v-for="metric in campaign.metrics"
                      :key="metric.label"
                      class="rounded-2xl border border-white/10 bg-white/5 p-4"
                    >
                      <p class="text-xs uppercase tracking-[0.35em] text-white/50">{{ metric.label }}</p>
                      <p class="text-lg font-semibold text-white">{{ metric.value }}</p>
                      <p v-if="metric.trend" class="text-xs text-emerald-300">{{ metric.trend }}</p>
                    </div>
                  </div>
                  <RouterLink
                    v-if="campaign.cta"
                    :to="campaign.cta.to"
                    class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-2 text-xs font-black uppercase tracking-[0.35em] text-slate-900"
                  >
                    {{ campaign.cta.label }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7 7 7-7 7" />
                    </svg>
                  </RouterLink>
                </div>
              </AccordionContent>
            </div>
          </AccordionItem>
        </Accordion>
        <p v-if="!liveCampaigns.length" class="mt-4 text-sm text-white/70">Şu anda canlı kampanya bulunmuyor.</p>
      </TabsContent>

      <TabsContent value="planned">
        <Accordion type="single" class="space-y-4" :default-value="undefined">
          <AccordionItem
            v-for="(campaign, index) in plannedCampaigns"
            :key="campaign.title + index"
            :value="campaign.title + '-plan-' + index"
            class="border-white/15 bg-white/5 text-white"
          >
            <div class="p-5">
              <AccordionTrigger class="text-left">
                <div class="flex flex-col gap-1">
                  <div class="text-xs uppercase tracking-[0.35em] text-white/60">{{ campaign.badge }} · {{ campaign.window }}</div>
                  <h3 class="text-xl font-black">{{ campaign.title }}</h3>
                  <p class="text-xs text-white/70">{{ campaign.channel || 'Marketplace' }}</p>
                </div>
              </AccordionTrigger>
              <AccordionContent>
                <div class="space-y-4 text-sm text-white/80">
                  <p>{{ campaign.copy }}</p>
                  <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                      <p class="text-xs uppercase tracking-[0.35em] text-white/50">İletişim</p>
                      <p class="text-sm text-lime-200">{{ campaign.discount }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                      <p class="text-xs uppercase tracking-[0.35em] text-white/50">Durum</p>
                      <p>{{ campaign.status || 'Planlandı' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                      <p class="text-xs uppercase tracking-[0.35em] text-white/50">Hazırlık</p>
                      <p>{{ progressLabel(campaign.progress) }}</p>
                      <div class="mt-2 h-1.5 rounded-full bg-white/10">
                        <div
                          class="h-full rounded-full bg-gradient-to-r from-slate-200 via-slate-100 to-white"
                          :style="{ width: progressWidth(campaign.progress) }"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>
              </AccordionContent>
            </div>
          </AccordionItem>
        </Accordion>
        <p v-if="!plannedCampaigns.length" class="mt-4 text-sm text-white/70">Yeni planlanan kampanya bulunmuyor.</p>
      </TabsContent>
    </Tabs>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from '@/components/ui/accordion'
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs'

interface CampaignMetric {
  label: string
  value: string
  trend?: string
}

interface CampaignCTA {
  label: string
  to: string
}

type CampaignPhase = 'live' | 'planned'
type StatusTone = 'success' | 'alert' | 'neutral'

interface CampaignCard {
  badge: string
  title: string
  copy: string
  window: string
  discount: string
  status?: string
  reach?: string
  lift?: string
  progress?: number
  channel?: string
  metrics?: CampaignMetric[]
  cta?: CampaignCTA
  phase?: CampaignPhase
  statusTone?: StatusTone
}

const props = defineProps<{ campaigns: CampaignCard[] }>()

const isLiveCampaign = (campaign: CampaignCard) => {
  if (campaign.phase) return campaign.phase === 'live'
  return (campaign.status || '').toLowerCase().includes('canlı')
}

const liveCampaigns = computed(() => props.campaigns.filter(isLiveCampaign))
const plannedCampaigns = computed(() => props.campaigns.filter(campaign => !isLiveCampaign(campaign)))

const defaultTab = computed(() => (liveCampaigns.value.length ? 'live' : 'planned'))

const statusToneMap: Record<StatusTone, string> = {
  success: 'rounded-full bg-emerald-400/20 px-3 py-1 text-emerald-200',
  alert: 'rounded-full bg-rose-500/20 px-3 py-1 text-rose-200',
  neutral: 'rounded-full bg-white/10 px-3 py-1 text-white/70'
}

const statusToneClass = (tone?: StatusTone) => statusToneMap[tone ?? 'success']

const liftToneClass = (lift?: string) =>
  lift && lift.includes('-')
    ? 'rounded-full bg-rose-500/20 px-3 py-1 text-rose-200'
    : 'rounded-full bg-emerald-400/20 px-3 py-1 text-emerald-200'

const progressWidth = (value?: number) => `${Math.min(Math.max(value ?? 65, 0), 100)}%`
const progressLabel = (value?: number) => `${Math.min(Math.max(value ?? 65, 0), 100)}% hazır`
</script>
