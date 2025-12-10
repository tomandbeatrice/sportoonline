<template>
  <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-gray-900">Modül Merkezi</h2>
      <select v-model="selectedCategory" class="px-4 py-2 border rounded-lg text-sm">
        <option value="all">Tüm Modüller</option>
        <option value="sales">Satış & Sipariş</option>
        <option value="inventory">Ürün & Stok</option>
        <option value="marketing">Pazarlama</option>
        <option value="analytics">Analitik</option>
        <option value="local">Yerel & Keşfet</option>
        <option value="admin">Yönetim</option>
      </select>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <ModuleCard
        v-for="module in filteredModules"
        :key="module.id"
        :module="module"
        @click="openModule(module)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUnifiedDashboardStore } from '@/stores/unifiedDashboardStore'
import ModuleCard from '@/components/unified/ModuleCard.vue'

const router = useRouter()
const store = useUnifiedDashboardStore()
const selectedCategory = ref('all')

const filteredModules = computed(() => {
  if (selectedCategory.value === 'all') return store.modules
  return store.modules.filter(m => m.category === selectedCategory.value)
})

const emit = defineEmits(['open-module'])

function openModule(module: any) {
  if (module.route && !module.route.includes(':')) {
    router.push(module.route)
  } else {
    emit('open-module', module)
  }
}
</script>
