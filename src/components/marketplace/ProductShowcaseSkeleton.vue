<template>
  <section class="rounded-3xl bg-white p-8 shadow-xl">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <Skeleton class="h-4 w-32 mb-2" />
        <Skeleton class="h-6 w-48" />
      </div>
      <Skeleton class="h-10 w-32" />
    </div>
    <div class="relative mt-6">
      <div class="flex gap-4 overflow-hidden">
        <div v-for="i in 4" :key="i" class="flex-shrink-0 w-[320px]">
          <div class="flex h-full flex-col rounded-3xl border border-gray-100 bg-gradient-to-b from-gray-50 to-white shadow-sm">
            <Skeleton class="aspect-video rounded-t-3xl" />
            <div class="flex flex-1 flex-col gap-3 p-5">
              <div>
                <Skeleton class="h-5 w-full mb-2" />
                <Skeleton class="h-4 w-3/4 mb-2" />
                <Skeleton class="h-4 w-1/2" />
              </div>
              <div class="flex flex-wrap items-center gap-3 mt-auto">
                <Skeleton class="h-8 w-24" />
                <Skeleton class="h-8 w-32 ml-auto" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'
</script>
