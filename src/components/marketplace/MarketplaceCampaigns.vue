<template>
  <section 
    v-if="campaigns.length > 0"
    class="campaign-slider relative"
    @mouseenter="pauseCampaignAutoPlay"
    @mouseleave="resumeCampaignAutoPlay"
    @touchstart="handleCampaignTouchStart"
    @touchend="handleCampaignTouchEnd"
  >
    <div 
      class="relative overflow-hidden"
      :style="currentCampaignGradient"
    >
      <!-- Campaign Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <!-- Campaign Info -->
          <div class="flex-1 text-white">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 rounded-full text-sm mb-3">
              <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
              {{ t('campaign.live') }}
            </span>
            <h2 class="text-2xl md:text-3xl font-bold mb-2">
              🎉 {{ currentCampaign.title }}
            </h2>
            <p class="text-white/90 text-sm md:text-base max-w-xl">
              {{ currentCampaign.description }}
            </p>
          </div>

          <!-- Countdown Timer -->
          <div v-if="campaignCountdown" class="flex items-center gap-3 md:gap-4">
            <div 
              v-for="(unit, key) in countdownUnits" 
              :key="key"
              class="text-center"
            >
              <div class="bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 md:px-4 md:py-3 min-w-[60px]">
                <span class="text-2xl md:text-3xl font-bold text-white">
                  {{ String(campaignCountdown[key]).padStart(2, '0') }}
                </span>
              </div>
              <span class="text-xs text-white/70 mt-1 block">{{ unit }}</span>
            </div>
          </div>
        </div>

        <!-- CTA Button -->
        <div class="mt-4">
          <button 
            @click="goToCampaign(currentCampaign)"
            class="inline-flex items-center gap-2 px-6 py-3 bg-white text-indigo-600 font-semibold rounded-lg shadow-lg hover:bg-slate-50 transition-all"
          >
            {{ t('campaign.shopNow') }}
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Progress Bar -->
      <div class="absolute bottom-0 left-0 w-full h-1 bg-black/20">
        <div 
          class="h-full bg-white/60 transition-all duration-100 ease-linear"
          :style="{ width: campaignProgress + '%' }"
        ></div>
      </div>

      <!-- Navigation Arrows -->
      <template v-if="campaigns.length > 1">
        <button 
          @click="prevCampaign"
          class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors"
          :aria-label="t('common.previous')"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <button 
          @click="nextCampaign"
          class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors"
          :aria-label="t('common.next')"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </template>

      <!-- Dot Indicators -->
      <div v-if="campaigns.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
        <button
          v-for="(_, idx) in campaigns"
          :key="idx"
          @click="goToCampaignSlide(idx)"
          class="w-2.5 h-2.5 rounded-full transition-all"
          :class="idx === currentCampaignIndex 
            ? 'bg-white w-6' 
            : 'bg-white/40 hover:bg-white/60'"
          :aria-label="`Kampanya ${idx + 1}`"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

const router = useRouter()
const { t } = useI18n()

interface Campaign {
  id: number
  title: string
  description: string
  endDate?: string
  colorFrom?: string
  colorTo?: string
  link?: string
}

const campaigns = ref<Campaign[]>([])
const currentCampaignIndex = ref(0)
const campaignProgress = ref(0)
const campaignCountdown = ref<any>(null)
let campaignAutoPlayInterval: any = null
let campaignProgressInterval: any = null
let campaignCountdownInterval: any = null
let campaignTouchStartX = 0

const currentCampaign = computed(() => campaigns.value[currentCampaignIndex.value] || {})
const currentCampaignGradient = computed(() => {
  const c = currentCampaign.value
  return `background: linear-gradient(135deg, ${c.colorFrom || '#6366f1'}, ${c.colorTo || '#8b5cf6'})`
})

const countdownUnits = {
  days: 'GÜN',
  hours: 'SAAT',
  minutes: 'DAKİKA',
  seconds: 'SANİYE'
}

// Methods
const loadCampaigns = async () => {
  try {
    const response = await fetch('/api/marketplace/campaigns')
    campaigns.value = response.ok ? await response.json() : []
    
    // Mock data if empty
    if (campaigns.value.length === 0) {
      campaigns.value = [
        {
          id: 1,
          title: 'Yaz İndirimleri Başladı!',
          description: 'Seçili ürünlerde %50\'ye varan indirimler sizi bekliyor.',
          endDate: new Date(Date.now() + 86400000 * 3).toISOString(), // 3 days later
          colorFrom: '#f59e0b',
          colorTo: '#ef4444',
          link: '/campaigns/summer-sale'
        },
        {
          id: 2,
          title: 'Yeni Sezon Ürünleri',
          description: 'En trend spor kıyafetleri ve ekipmanları stoklarda.',
          endDate: new Date(Date.now() + 86400000 * 7).toISOString(),
          colorFrom: '#3b82f6',
          colorTo: '#8b5cf6',
          link: '/campaigns/new-season'
        }
      ]
    }

    if (campaigns.value.length > 0) {
      updateCampaignCountdown()
      startCampaignAutoPlay()
    }
  } catch (error) {
    console.error('Kampanyalar yüklenemedi:', error)
  }
}

const startCampaignAutoPlay = () => {
  if (campaigns.value.length <= 1) return
  
  pauseCampaignAutoPlay()
  campaignProgress.value = 0

  campaignAutoPlayInterval = setInterval(() => {
    nextCampaign()
  }, 5000)

  campaignProgressInterval = setInterval(() => {
    if (campaignProgress.value < 100) {
      campaignProgress.value += 2
    }
  }, 100)
}

const pauseCampaignAutoPlay = () => {
  if (campaignAutoPlayInterval) clearInterval(campaignAutoPlayInterval)
  if (campaignProgressInterval) clearInterval(campaignProgressInterval)
}

const resumeCampaignAutoPlay = () => {
  if (campaigns.value.length > 1) {
    startCampaignAutoPlay()
  }
}

const nextCampaign = () => {
  currentCampaignIndex.value = (currentCampaignIndex.value + 1) % campaigns.value.length
  campaignProgress.value = 0
  updateCampaignCountdown()
}

const prevCampaign = () => {
  currentCampaignIndex.value = (currentCampaignIndex.value - 1 + campaigns.value.length) % campaigns.value.length
  campaignProgress.value = 0
  updateCampaignCountdown()
}

const goToCampaignSlide = (index: number) => {
  currentCampaignIndex.value = index
  campaignProgress.value = 0
  updateCampaignCountdown()
}

const goToCampaign = (campaign: Campaign) => {
  if (campaign.link) {
    router.push(campaign.link)
  } else {
    router.push(`/campaigns/${campaign.id}`)
  }
}

const updateCampaignCountdown = () => {
  if (campaignCountdownInterval) clearInterval(campaignCountdownInterval)
  
  const campaign = currentCampaign.value
  if (!campaign.endDate) {
    campaignCountdown.value = null
    return
  }

  const endDate = new Date(campaign.endDate)
  
  campaignCountdownInterval = setInterval(() => {
    const now = new Date()
    const diff = endDate.getTime() - now.getTime()
    
    if (diff <= 0) {
      campaignCountdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
      if (campaignCountdownInterval) clearInterval(campaignCountdownInterval)
      return
    }
    
    campaignCountdown.value = {
      days: Math.floor(diff / (1000 * 60 * 60 * 24)),
      hours: Math.floor((diff / (1000 * 60 * 60)) % 24),
      minutes: Math.floor((diff / (1000 * 60)) % 60),
      seconds: Math.floor((diff / 1000) % 60)
    }
  }, 1000)
}

const handleCampaignTouchStart = (e: TouchEvent) => {
  campaignTouchStartX = e.changedTouches[0].clientX
}

const handleCampaignTouchEnd = (e: TouchEvent) => {
  const touchEndX = e.changedTouches[0].clientX
  const diff = touchEndX - campaignTouchStartX
  
  if (diff < -50) nextCampaign()
  else if (diff > 50) prevCampaign()
}

onMounted(() => {
  loadCampaigns()
})

onUnmounted(() => {
  pauseCampaignAutoPlay()
  if (campaignCountdownInterval) clearInterval(campaignCountdownInterval)
})
</script>
