<template>
  <section
    class="hero relative overflow-hidden rounded-[32px] text-white shadow-2xl"
    :style="backgroundStyle"
  >
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950/95 via-slate-900/80 to-slate-900/50"></div>
    <div class="absolute -right-32 top-0 hidden h-80 w-80 rounded-full bg-emerald-400/30 blur-3xl lg:block"></div>
    <div class="relative z-10 flex flex-col gap-10 p-8 lg:flex-row lg:items-end lg:justify-between lg:p-12">
      <div class="space-y-6 text-left">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.4em] text-lime-200/90">
          <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
          {{ content.badge }}
        </p>
        <div class="space-y-4">
          <h1 class="text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
            <span class="block text-white/80 text-lg uppercase tracking-[0.4em]">2 Saatte Kapında</span>
            <span class="mt-2 inline-flex flex-wrap gap-2 text-white">
              {{ content.title }}
            </span>
          </h1>
          <p class="max-w-2xl text-base text-white/80 sm:text-lg">
            {{ content.subtitle }}
          </p>
        </div>
        <div class="flex flex-wrap gap-4">
          <RouterLink
            :to="content.primaryCta.to"
            class="flex items-center gap-2 rounded-2xl bg-gradient-to-r from-orange-400 via-amber-400 to-lime-400 px-7 py-3 text-xs font-black uppercase tracking-widest text-slate-900 shadow-xl shadow-black/20 transition hover:-translate-y-0.5"
          >
            {{ content.primaryCta.label }}
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7 7 7-7 7" />
            </svg>
          </RouterLink>
          <RouterLink
            :to="content.secondaryCta.to"
            class="flex items-center gap-2 rounded-2xl border border-white/40 px-7 py-3 text-xs font-semibold uppercase tracking-widest text-white/90 transition hover:bg-white/10"
          >
            {{ content.secondaryCta.label }}
          </RouterLink>
        </div>
        <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/80">
          {{ content.dailyDeal }}
        </div>
      </div>
      <div class="w-full max-w-md space-y-4 rounded-3xl border border-white/10 bg-white/10 p-6 backdrop-blur">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/60">Canlı metrikler</p>
        <div class="grid gap-3 sm:grid-cols-2">
          <div
            v-for="stat in content.stats"
            :key="stat.label"
            class="rounded-2xl bg-white/10 p-4 backdrop-blur-sm"
          >
            <p class="text-3xl font-black text-white">{{ stat.value }}</p>
            <p class="text-xs uppercase tracking-[0.35em] text-white/70">{{ stat.label }}</p>
          </div>
        </div>
        <button
          class="w-full rounded-2xl border border-white/40 bg-white/10 px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.35em] text-white/80"
          @click="$router.push('/apply-seller')"
        >
          Satıcı cockpit’ine geç →
        </button>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'

interface HeroContent {
  badge: string
  title: string
  subtitle: string
  primaryCta: { label: string; to: string }
  secondaryCta: { label: string; to: string }
  stats: Array<{ label: string; value: string }>
  dailyDeal: string
  backgroundImage?: string
}

const props = defineProps<{ content: HeroContent }>()

const backgroundStyle = computed(() => {
  if (!props.content.backgroundImage) {
    return {
      backgroundImage: 'linear-gradient(135deg, #0f172a, #0a1a2f)'
    }
  }

  return {
    backgroundImage: `linear-gradient(135deg, rgba(4,7,18,0.92), rgba(6,18,34,0.65)), url('${props.content.backgroundImage}')`,
    backgroundSize: 'cover',
    backgroundPosition: 'center'
  }
})
</script>
