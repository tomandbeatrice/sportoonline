<template>
  <section class="p-4">
    <h2 class="text-lg font-bold text-purple-700">📊 Kampanya Yoğunluk Haritası</h2>
    <div class="grid grid-cols-3 gap-4 mt-4">
      <div
        v-for="day in density"
        :key="day.date"
        class="p-3 rounded text-center shadow-sm"
        :class="{
          'bg-green-200': day.advantage > 0,
          'bg-blue-200': day.total >= 3,
          'bg-gray-100': day.total < 3
        }"
      >
        <strong>{{ day.date }}</strong>
        <br />{{ day.total }} kampanya
        <br v-if="day.advantage > 0" />
        <span v-if="day.advantage > 0" class="inline-flex items-center gap-1"><IconStar cls="w-4 h-4 text-yellow-400" :filled="true" /> Avantajlı: {{ day.advantage }}</span>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import IconStar from '@/components/icons/IconStar.vue'

const density = ref([
  { date: '2025-08-25', total: 4, advantage: 2 },
  { date: '2025-08-26', total: 1, advantage: 0 },
  { date: '2025-08-27', total: 3, advantage: 1 }
])
</script>