<template>
  <section class="rounded-3xl bg-white p-8 shadow-xl">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.4em] text-emerald-600">Trend Radar</p>
        <h2 class="text-2xl font-black text-gray-900">Çok satanlar</h2>
      </div>
      <div class="flex items-center gap-2">
        <RouterLink
          to="/products"
          class="rounded-2xl border border-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-gray-600 transition hover:border-gray-300"
        >
          Tüm ürünler
        </RouterLink>
      </div>
    </div>
    <div class="relative mt-6">
      <Carousel class="pr-16">
        <CarouselContent>
          <CarouselItem v-for="product in products" :key="product.id" class="md:min-w-[320px]">
            <article class="flex h-full flex-col rounded-3xl border border-gray-100 bg-gradient-to-b from-gray-50 to-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
              <div class="relative aspect-video overflow-hidden rounded-t-3xl bg-gray-100">
                <img
                  v-if="product.image"
                  class="h-full w-full object-cover"
                  :src="product.image"
                  :alt="product.name"
                />
                <div v-else class="flex h-full items-center justify-center text-gray-400">Görsel yok</div>
                <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-gray-900">
                  {{ product.category }}
                </span>
              </div>
              <div class="flex flex-1 flex-col gap-3 p-5">
                <div>
                  <h3 class="text-lg font-bold text-gray-900 line-clamp-2">{{ product.name }}</h3>
                  <p class="text-sm text-gray-500 line-clamp-2">{{ product.description }}</p>
                  <div v-if="product.badges?.length" class="mt-3 flex flex-wrap gap-2">
                    <span
                      v-for="badge in product.badges"
                      :key="`${product.id}-${badge.code}-${badge.label}`"
                      class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-[0.7rem] font-semibold"
                      :class="badgeClasses[badge.tone]"
                      :title="badgeDebug ? formatBadgeDebugTitle(badge) : undefined"
                    >
                      <BadgeIcon v-if="badge.icon" :name="badge.icon" cls="w-3 h-3" />
                      {{ badge.label }}
                    </span>
                  </div>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                  <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1">{{ product.seller }}</span>
                  <span class="inline-flex items-center gap-1 text-emerald-600">
                    <IconBolt cls="w-4 h-4" /> Hızlı teslimat
                  </span>
                </div>
                <div class="mt-auto flex items-center justify-between">
                  <div>
                    <p class="text-2xl font-black text-emerald-600">{{ product.priceLabel }}</p>
                  </div>
                  <button
                    class="rounded-2xl bg-slate-900 px-5 py-2 text-xs font-black uppercase tracking-[0.3em] text-white transition hover:-translate-y-0.5"
                    @click="$emit('add-to-cart', product.id)"
                  >
                    Sepete Ekle
                  </button>
                </div>
              </div>
            </article>
          </CarouselItem>
        </CarouselContent>
        <div class="absolute right-0 top-1/2 hidden -translate-y-1/2 flex-col gap-2 lg:flex">
          <CarouselPrevious />
          <CarouselNext />
        </div>
      </Carousel>
    </div>
  </section>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import IconBolt from '@/components/icons/IconBolt.vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

import { badgeToneClasses, type NormalizedBadge, isBadgeDebugEnabled } from '@/utils/badgeMapper'
import { Carousel, CarouselContent, CarouselItem, CarouselPrevious, CarouselNext } from '@/components/ui/carousel'

const badgeClasses = badgeToneClasses
const badgeDebug = isBadgeDebugEnabled()

function formatBadgeDebugTitle(badge: NormalizedBadge) {
  return `${badge.code} | ${badge.tone}${badge.label ? ` | ${badge.label}` : ''}`
}

defineProps<{
  products: Array<{
    id: number | string
    name: string
    description: string
    priceLabel: string
    seller: string
    category: string
    image?: string
    badges?: NormalizedBadge[]
  }>
}>()
</script>
