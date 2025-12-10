<template>
  <div>
    <!-- Floating Action Button -->
    <button 
      @click="showQuickActions = !showQuickActions"
      class="fixed bottom-8 right-8 w-16 h-16 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-2xl flex items-center justify-center transition-transform hover:scale-110 z-50"
    >
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
    </button>

    <!-- Quick Actions Menu -->
    <transition name="slide-up">
      <div v-if="showQuickActions" class="fixed bottom-28 right-8 bg-white rounded-xl shadow-2xl p-4 w-64 z-50">
        <h3 class="font-bold text-gray-900 mb-3">Hızlı İşlemler</h3>
        <div class="space-y-2">
          <button
            v-for="action in quickActions"
            :key="action.id"
            @click="executeQuickAction(action)"
            class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-100 flex items-center gap-3"
          >
            <span>{{ action.icon }}</span>
            <span class="font-medium">{{ action.label }}</span>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const showQuickActions = ref(false)

const quickActions = [
  { id: 'new-product', label: 'Yeni Ürün Ekle', icon: 'plus' },
  { id: 'new-campaign', label: 'Kampanya Oluştur', icon: 'target' },
  { id: 'approve-seller', label: 'Satıcı Onayla', icon: 'check' },
  { id: 'process-order', label: 'Sipariş İşle', icon: 'box' },
  { id: 'view-analytics', label: 'Analitikleri Gör', icon: 'chart' },
  { id: 'export-data', label: 'Veri Export', icon: 'download' }
]

function executeQuickAction(action: any) {
  showQuickActions.value = false
  
  const actionRoutes: Record<string, string> = {
    'new-product': '/admin/products/create',
    'new-campaign': '/admin/campaigns/create',
    'approve-seller': '/admin/seller-applications?filter=pending',
    'process-order': '/admin/orders?status=pending',
    'view-analytics': '/admin/analytics',
    'export-data': '/admin/exports'
  }

  if (actionRoutes[action.id]) {
    router.push(actionRoutes[action.id])
  }
}
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
