<template>
  <section class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-br from-white via-slate-50 to-slate-100 p-6 shadow-xl">
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute -left-6 top-0 h-32 w-32 rounded-full bg-blue-500/20 blur-3xl"></div>
      <div class="absolute -right-8 bottom-0 h-36 w-36 rounded-full bg-fuchsia-400/20 blur-3xl"></div>
    </div>

    <div class="relative flex flex-wrap items-center justify-between gap-4">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Partner ağımız</p>
        <h3 class="text-lg font-black text-slate-800">{{ title }}</h3>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <span class="badge-soft hidden md:inline-flex">{{ subtitle }}</span>
        <div class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-500">
          <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
          72K+ satıcı güvende
        </div>
      </div>
    </div>

    <div v-if="stats?.length" class="relative mt-6 grid gap-3 sm:grid-cols-3">
      <div
        v-for="item in stats"
        :key="item.label"
        class="rounded-2xl border border-white/60 bg-white/90 p-4 text-sm text-slate-600"
      >
        <p class="text-xs uppercase tracking-wide text-slate-400">{{ item.label }}</p>
        <p class="text-lg font-semibold text-slate-900">{{ item.value }}</p>
        <p v-if="item.delta" class="text-xs" :class="item.delta.includes('+') ? 'text-emerald-500' : 'text-slate-400'">
          {{ item.delta }}
        </p>
      </div>
    </div>

    <div class="relative mt-6 overflow-hidden">
      <div class="marquee-track" role="list">
        <div
          v-for="brand in brands"
          :key="brand.name"
          class="flex min-w-[220px] items-center gap-3 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm"
        >
          <span class="text-2xl">{{ brand.emoji }}</span>
          <div>
            <p class="text-sm font-semibold text-slate-700">{{ brand.name }}</p>
            <p class="text-xs text-slate-400">{{ brand.tagline }}</p>
            <div v-if="brand.sla" class="mt-1 text-[0.65rem] text-emerald-500">{{ brand.sla }}</div>
          </div>
        </div>
        <div
          v-for="brand in brands"
          :key="brand.name + '-clone'"
          class="flex min-w-[220px] items-center gap-3 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-sm"
        >
          <span class="text-2xl">{{ brand.emoji }}</span>
          <div>
            <p class="text-sm font-semibold text-slate-700">{{ brand.name }}</p>
            <p class="text-xs text-slate-400">{{ brand.tagline }}</p>
            <div v-if="brand.sla" class="mt-1 text-[0.65rem] text-emerald-500">{{ brand.sla }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="relative mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-[1.15fr_0.85fr]">
      <div class="rounded-3xl border border-slate-100 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6 text-white shadow-xl sm:p-7">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300/90">{{ cta?.eyebrow ?? 'Partner Ol' }}</p>
        <h4 class="mt-2 text-lg font-black">{{ cta?.title ?? 'Express ağına 12 günde katıl' }}</h4>
        <p class="mt-3 text-sm text-slate-200">{{ cta?.description ?? 'SLA verisi, stok paylaşımı ve hazır fulfillment ağıyla teslimatı hızlandırın.' }}</p>
        <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-slate-300">
          <span class="inline-flex items-center gap-1 rounded-full border border-white/20 px-3 py-1">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
            {{ cta?.meta ?? 'Ücretsiz onboarding' }}
          </span>
          <span class="inline-flex items-center gap-1 rounded-full border border-white/20 px-3 py-1">
            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3" />
            </svg>
            7/24 SLA desteği
          </span>
        </div>
        <button class="mt-5 inline-flex items-center justify-center gap-2 rounded-2xl bg-white/95 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-900 shadow-lg shadow-black/20">
          {{ cta?.button ?? 'Başvuru formu' }}
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
      <div class="rounded-3xl border border-slate-100 bg-white/90 p-5 sm:p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Partner güven rozetleri</p>
        <div class="mt-4 flex flex-wrap gap-3">
          <div
            v-for="badge in badges"
            :key="badge.label"
            class="inline-flex min-w-[120px] flex-col rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3"
          >
            <span class="text-sm font-semibold text-slate-800">{{ badge.label }}</span>
            <span v-if="badge.description" class="text-[0.7rem] text-slate-500">{{ badge.description }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="relative mt-6 flex flex-wrap items-center gap-3 rounded-2xl border border-dashed border-slate-200 bg-white/80 px-4 py-3 text-xs text-slate-500">
      <span class="inline-flex items-center gap-2 font-semibold text-slate-700">
        <svg class="h-4 w-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        Teslimat SLA ortalaması 1.6 gün
      </span>
      <span>·</span>
      <span>48 şehirde yerel mikro fulfillment</span>
      <span>·</span>
      <span>Express ağında 320 partner</span>
    </div>
  </section>
</template>

<script setup lang="ts">
interface BrandItem {
  name: string
  tagline: string
  emoji: string
  sla?: string
}

interface BadgeItem {
  label: string
  description?: string
}

interface CtaContent {
  eyebrow?: string
  title?: string
  description?: string
  button?: string
  meta?: string
}

withDefaults(
  defineProps<{
    title?: string
    subtitle?: string
    brands: BrandItem[]
    stats?: Array<{ label: string; value: string; delta?: string }>
    badges?: BadgeItem[]
    cta?: CtaContent
  }>(),
  {
    badges: () => [
      { label: 'ISO 27001', description: 'Veri güvenliği sertifikası' },
      { label: 'Same-day ready', description: 'T+0 fulfillment kapasitesi' },
      { label: 'Cold chain pro', description: '0-4°C teslimat SLA' }
    ],
    cta: () => ({
      eyebrow: 'Partner Ol',
      title: 'Express ağına 12 günde katıl',
      description: 'SLA verisi, stok paylaşımı ve hazır fulfillment ağıyla teslimatı hızlandırın.',
      button: 'Başvuru formu',
      meta: 'Ücretsiz onboarding'
    })
  }
)
</script>
