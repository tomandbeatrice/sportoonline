<template>
  <section class="rounded-3xl bg-white p-8 shadow-xl">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-blue-600">Keşfet</p>
        <h2 class="text-2xl font-black text-gray-900">Popüler Kategoriler</h2>
      </div>
      <button
        class="self-start rounded-2xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:border-gray-300"
        @click="$emit('reset')"
      >
        Tümü
      </button>
    </div>
    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4 xl:grid-cols-6">
      <Card
        v-for="category in categories"
        :key="category.id"
        as="button"
        class="group flex flex-col gap-3 text-left"
        :class="accentClasses(category.name)"
        @click="$emit('select', category.id)"
      >
        <span class="text-3xl">{{ category.icon || '🏷️' }}</span>
        <div>
          <p class="font-semibold text-gray-900">{{ category.name }}</p>
          <p class="text-sm text-gray-500">{{ category.count }} ürün</p>
        </div>
      </Card>
    </div>
  </section>
</template>

<script setup lang="ts">
import { Card } from '@/components/ui/card'
interface CategoryItem {
  id: number | string
  name: string
  count: number
  icon?: string
}

defineProps<{ categories: CategoryItem[] }>()

const accentStrategies = [
  { regex: /(outdoor|trail|kamp|dağ|hiking)/i, classes: 'border-emerald-100 bg-emerald-50/80 hover:border-emerald-200 hover:bg-emerald-50/90 text-emerald-900' },
  { regex: /(elektronik|electronics|tech|teknoloji)/i, classes: 'border-sky-100 bg-sky-50/80 hover:border-sky-200 hover:bg-sky-50/90 text-sky-900' },
  { regex: /(giyim|fashion|fit|spor)/i, classes: 'border-orange-100 bg-orange-50/80 hover:border-orange-200 hover:bg-orange-50/90 text-orange-900' }
]

const baseClasses = 'border-gray-100 bg-gray-50/80 hover:border-gray-200 hover:bg-gray-50/90 text-gray-900'

function accentClasses(name: string) {
  const match = accentStrategies.find(strategy => strategy.regex.test(name))
  return match ? match.classes : baseClasses
}
</script>
