<template>
  <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
    <article
      v-for="promo in promos"
      :key="promo.title"
      class="relative overflow-hidden rounded-3xl p-6 text-white shadow-xl"
      :class="promo.bg"
    >
      <div class="relative z-10 space-y-4">
        <p class="text-sm uppercase tracking-[0.25em] text-white/70">{{ promo.badge }}</p>
        <h3 class="text-2xl font-black">{{ promo.title }}</h3>
        <p class="text-sm text-white/80">{{ promo.copy }}</p>
        <RouterLink
          :to="promo.to"
          class="inline-flex items-center gap-2 rounded-full bg-white/15 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/25"
        >
          {{ promo.cta }}
          <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7 7 7-7 7" />
          </svg>
        </RouterLink>
      </div>
      <div class="absolute -right-10 bottom-0 h-32 w-32 rounded-full bg-white/20 blur-3xl"></div>
    </article>
  </section>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'

interface PromoCard {
  badge: string
  title: string
  copy: string
  cta: string
  to: string
  bg: string
}

defineProps<{ promos: PromoCard[] }>()
</script>
