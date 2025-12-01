<template>
  <nav class="space-y-2">
    <router-link
      to="/admin/dashboard"
      class="block px-4 py-2 rounded hover:bg-slate-50 text-slate-700 font-medium flex items-center gap-2"
    >
      <BadgeIcon name="home" cls="w-5 h-5" /> Ana Panel
    </router-link>

    <router-link
      to="/admin/campaign-ai"
      class="block px-4 py-2 rounded hover:bg-violet-50 text-violet-700 font-medium flex items-center gap-2"
    >
      <BadgeIcon name="cpu" cls="w-5 h-5" /> AI Kampanya Yönetimi
    </router-link>

    <router-link
      to="/admin/strategy-cockpit"
      class="block px-4 py-2 rounded hover:bg-indigo-50 text-indigo-700 font-medium flex items-center gap-2"
    >
      <BadgeIcon name="bar-chart" cls="w-5 h-5" /> Strateji Cockpit
    </router-link>

    <router-link
      to="/admin/invitation-analytics"
      class="block px-4 py-2 rounded hover:bg-purple-50 text-purple-700 font-medium flex items-center gap-2"
    >
      <BadgeIcon name="link" cls="w-5 h-5" /> Davet Zinciri Analizi
    </router-link>
  </nav>
</template>

<script setup>
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
</script>