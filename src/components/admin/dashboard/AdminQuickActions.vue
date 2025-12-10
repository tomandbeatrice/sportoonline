<template>
  <section class="grid gap-6 lg:grid-cols-3">
    <!-- Entegrasyonlar -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-900">🔗 Entegrasyon Sağlığı</h3>
        <p class="text-sm text-slate-500">Üçüncü parti servislerin son durumları</p>
      </div>
      <div class="p-6 space-y-3">
        <div 
          v-for="integration in integrations" 
          :key="integration.id" 
          class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/50 p-4 hover:bg-slate-50 transition"
        >
          <div>
            <p class="font-medium text-slate-900">{{ integration.name }}</p>
            <p class="text-xs text-slate-500">{{ integration.description }}</p>
          </div>
          <span 
            class="px-3 py-1 rounded-full text-xs font-semibold"
            :class="integration.status === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-700'"
          >
            {{ integration.status }}
          </span>
        </div>
      </div>
    </div>

    <!-- Hızlı Aksiyonlar -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-900">⚡ Hızlı Aksiyon</h3>
        <p class="text-sm text-slate-500">Kritik makrolar</p>
      </div>
      <div class="p-6 space-y-2">
        <button
          v-for="action in actions"
          :key="action.id"
          @click="$emit('action', action)"
          class="flex w-full items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-left text-sm font-medium text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50"
        >
          <span>{{ action.label }}</span>
          <span class="text-xs font-normal text-slate-400">{{ action.meta }}</span>
        </button>
      </div>
    </div>
  </section>

  <!-- Aktivite Feed -->
  <section class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mt-6">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
      <div>
        <h3 class="font-bold text-slate-900">📡 Gerçek Zamanlı Aktivite</h3>
        <p class="text-sm text-slate-500">Son işlemler ve bildirimler</p>
      </div>
      <button class="text-sm text-indigo-600 hover:underline">Tümünü Gör</button>
    </div>
    <div class="divide-y divide-slate-100">
      <div 
        v-for="item in activities" 
        :key="item.id" 
        class="p-4 flex items-start gap-4 hover:bg-slate-50 transition"
      >
        <span 
          class="mt-1 h-3 w-3 rounded-full flex-shrink-0" 
          :class="item.accent" 
        />
        <div class="flex-1 min-w-0">
          <p class="font-medium text-slate-900 truncate">{{ item.title }}</p>
          <p class="text-sm text-slate-500 truncate">{{ item.detail }}</p>
        </div>
        <span class="text-xs text-slate-400 flex-shrink-0">{{ item.time }}</span>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
export interface Integration {
  id: string
  name: string
  description: string
  status: 'Aktif' | 'Gecikmeli' | 'Hata'
}

export interface QuickAction {
  id: string
  label: string
  meta: string
}

export interface ActivityItem {
  id: string
  title: string
  detail: string
  time: string
  accent: string
}

defineProps<{
  integrations: Integration[]
  actions: QuickAction[]
  activities: ActivityItem[]
}>()

defineEmits<{
  action: [action: QuickAction]
}>()
</script>
