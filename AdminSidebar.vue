<template>
  <nav class="space-y-2">
    <router-link
      to="/admin/dashboard"
      class="block px-4 py-2 rounded hover:bg-slate-50 text-slate-700 font-medium"
    >
      🏠 Ana Panel
    </router-link>

    <router-link
      to="/admin/campaign-ai"
      class="block px-4 py-2 rounded hover:bg-violet-50 text-violet-700 font-medium"
    >
      🤖 AI Kampanya Yönetimi
    </router-link>

    <router-link
      to="/admin/strategy-cockpit"
      class="block px-4 py-2 rounded hover:bg-indigo-50 text-indigo-700 font-medium"
    >
      📊 Strateji Cockpit
    </router-link>

    <router-link
      to="/admin/invitation-analytics"
      class="block px-4 py-2 rounded hover:bg-purple-50 text-purple-700 font-medium"
    >
      🔗 Davet Zinciri Analizi
    </router-link>
  </nav>
</template>