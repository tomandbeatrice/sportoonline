<script setup lang="ts">
import { useRegisterSW } from 'virtual:pwa-register/vue'

const {
  offlineReady,
  needRefresh,
  updateServiceWorker,
} = useRegisterSW()

const close = async () => {
  offlineReady.value = false
  needRefresh.value = false
}
</script>

<template>
  <div
    v-if="offlineReady || needRefresh"
    class="pwa-toast fixed bottom-4 right-4 z-50 p-4 bg-white rounded-lg shadow-xl border border-slate-200 max-w-sm"
    role="alert"
  >
    <div class="mb-2 text-sm text-slate-600">
      <span v-if="offlineReady">
        Uygulama çevrimdışı çalışmaya hazır
      </span>
      <span v-else>
        Yeni içerik mevcut, güncellemek için tıklayın
      </span>
    </div>
    <div class="flex gap-2">
      <button
        v-if="needRefresh"
        @click="updateServiceWorker()"
        class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Yenile
      </button>
      <button
        @click="close"
        class="px-3 py-1.5 text-sm font-medium text-slate-700 bg-slate-100 rounded-md hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500"
      >
        Kapat
      </button>
    </div>
  </div>
</template>
