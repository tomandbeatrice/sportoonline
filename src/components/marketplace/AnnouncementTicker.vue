<template>
  <section class="rounded-3xl border border-slate-100 bg-white/90 p-6 shadow-xl">
    <div class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.35em] text-emerald-600">
      <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
      Canlı duyurular
    </div>
    <Accordion class="mt-4" type="single" :default-value="undefined">
      <AccordionItem
        v-for="announcement in parsedAnnouncements"
        :key="announcement.id"
        :value="announcement.id"
        class="border-slate-100 bg-slate-50/60"
      >
        <div class="p-4">
          <AccordionTrigger class="text-sm">
            {{ announcement.title }}
          </AccordionTrigger>
          <AccordionContent class="text-xs">
            {{ announcement.body }}
          </AccordionContent>
        </div>
      </AccordionItem>
    </Accordion>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from '@/components/ui/accordion'

const props = defineProps<{ items: string[] }>()

const parsedAnnouncements = computed(() =>
  props.items.map((item, index) => {
    const [rawTitle, ...rest] = item.split(':')
    return {
      id: `${index}-${item}`,
      title: rest.length ? rawTitle.trim() : item,
      body: rest.length ? rest.join(':').trim() : item
    }
  })
)
</script>
