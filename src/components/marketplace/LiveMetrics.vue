<template>
  <section class="grid gap-4 rounded-3xl bg-white/95 p-6 shadow-xl md:grid-cols-2 xl:grid-cols-3">
    <article
      v-for="metric in metrics"
      :key="metric.label"
      class="group flex items-start gap-4 rounded-3xl border border-slate-100 bg-white/90 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
    >
      <div
        class="flex h-12 w-12 items-center justify-center rounded-2xl text-xl"
        :class="toneBadge(metric.tone)"
        aria-hidden="true"
      >
        {{ metric.icon || '📊' }}
      </div>
      <div class="space-y-3">
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">
          {{ metric.label }}
          <span :class="deltaClasses(metric.delta)">
            {{ metric.delta >= 0 ? '+' : '' }}{{ metric.delta }}%
          </span>
        </div>
        <p class="text-3xl font-black text-slate-900">{{ metric.value }}</p>
        <p class="text-xs text-slate-500">{{ metric.caption }}</p>
      </div>
    </article>
  </section>
</template>

<script setup lang="ts">
interface MetricItem {
  label: string
  value: string
  delta: number
  caption: string
  icon?: string
  tone?: 'emerald' | 'orange' | 'sky' | 'rose'
}

const props = defineProps<{ metrics: MetricItem[] }>()

const toneMap: Record<NonNullable<MetricItem['tone']>, string> = {
  emerald: 'bg-emerald-50 text-emerald-600',
  orange: 'bg-orange-50 text-orange-600',
  sky: 'bg-sky-50 text-sky-600',
  rose: 'bg-rose-50 text-rose-600'
}

const toneBadge = (tone?: MetricItem['tone']) => (tone ? toneMap[tone] : 'bg-slate-100 text-slate-600')

const deltaClasses = (delta: number) =>
  `rounded-full border px-2 py-0.5 text-[0.6rem] ${delta >= 0 ? 'border-emerald-200 bg-emerald-50 text-emerald-500' : 'border-rose-200 bg-rose-50 text-rose-500'}`
</script>
