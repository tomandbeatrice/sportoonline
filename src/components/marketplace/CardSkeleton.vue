<template>
  <div class="space-y-4">
    <div v-for="i in 3" :key="i" class="rounded-3xl bg-white p-8 shadow-xl">
      <div class="flex items-start justify-between mb-6">
        <div class="flex-1">
          <Skeleton class="h-6 w-48 mb-2" />
          <Skeleton class="h-4 w-32" />
        </div>
        <Skeleton class="h-10 w-10 rounded-full" />
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div v-for="j in 4" :key="j">
          <Skeleton class="h-4 w-24 mb-2" />
          <Skeleton class="h-8 w-32" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'
</script>
