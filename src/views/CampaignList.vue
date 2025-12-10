import EmptyState from '@/components/ui/EmptyState.vue'
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import LoadingSkeleton from '@/components/ui/LoadingSkeleton.vue'
import { useApi } from '@/composables/useApi'
import { toast } from 'vue3-toastify'
import CampaignCreate from './CampaignCreate.vue'
import CampaignEdit from './CampaignEdit.vue'
import CampaignDelete from './CampaignDelete.vue'
import CampaignTabs from './CampaignTabs.vue'

interface Campaign {
  id: number
  name: string
  status: 'active' | 'passive'
  start_date: string
  end_date: string
  score: number
}

const { get, post } = useApi()

const campaigns = ref<Campaign[]>([])
const search = ref('')
const sortKey = ref<'name' | 'start_date' | 'end_date'>('name')
const tabFilter = ref<'Tümü' | 'Aktif' | 'Pasif'>('Tümü')
const selected = ref<Campaign | null>(null)
const currentPage = ref(1)
const pageSize = 10

const editing = ref(false)
const editingItem = ref<Campaign | null>(null)

const deleting = ref(false)
const deletingItem = ref<Campaign | null>(null)
const loading = ref(false)
const loadError = ref<string | null>(null)

onMounted(async () => {
  loading.value = true
  loadError.value = null
  try {
    const response = await get<Campaign[] | { data: Campaign[] }>('/admin/campaign-list')
    // API response'u array veya {data: array} formatında olabilir
    campaigns.value = Array.isArray(response) ? response : (response?.data || [])
  } catch (error: any) {
    console.warn('API bağlantısı başarısız, mock data kullanılıyor:', error.message)
    loadError.value = 'Backend sunucusuna bağlanılamadı. Demo veriler gösteriliyor.'
    // Fallback mock data
    campaigns.value = [
      { id: 1, name: 'Yaz İndirimi', status: 'active', start_date: '2025-06-01', end_date: '2025-08-31', score: 85 },
      { id: 2, name: 'Black Friday', status: 'passive', start_date: '2025-11-25', end_date: '2025-11-30', score: 92 },
      { id: 3, name: 'Yılbaşı Kampanyası', status: 'active', start_date: '2025-12-15', end_date: '2026-01-05', score: 78 },
      { id: 4, name: 'Bahar Festivali', status: 'active', start_date: '2025-03-21', end_date: '2025-04-21', score: 65 },
      { id: 5, name: 'Okula Dönüş', status: 'passive', start_date: '2025-09-01', end_date: '2025-09-30', score: 71 }
    ]
  } finally {
    loading.value = false
  }
})

const filteredCampaigns = computed(() => {
  if (!Array.isArray(campaigns.value)) return []
  return campaigns.value
    .filter(c => {
      const matchSearch = c.name.toLowerCase().includes(search.value.toLowerCase())
      const matchTab =
        tabFilter.value === 'Tümü' ||
        (tabFilter.value === 'Aktif' && c.status === 'active') ||
        (tabFilter.value === 'Pasif' && c.status === 'passive')
      return matchSearch && matchTab
    })
    .sort((a, b) => a[sortKey.value].localeCompare(b[sortKey.value]))
})

const paginatedCampaigns = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  return filteredCampaigns.value.slice(start, start + pageSize)
})

const totalPages = computed(() => Math.ceil(filteredCampaigns.value.length / pageSize))

const selectCampaign = (item: Campaign) => {
  selected.value = item
}

const toggleStatus = async (id: number) => {
  await post('/admin/campaign-toggle-status', { id })
  campaigns.value = await get<Campaign[]>('/admin/campaign-list')
  toast.info('🔄 Durum güncellendi!')
}

const exportCSV = () => {
  const header = ['ID', 'Ad', 'Durum', 'Başlangıç', 'Bitiş']
  const rows = campaigns.value.map(c => [c.id, c.name, c.status, c.start_date, c.end_date])
  const csv = [header, ...rows].map(r => r.join(',')).join('\n')

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = 'kampanyalar.csv'
  link.click()
  toast.success('📤 CSV başarıyla indirildi!')
}

const addCampaign = (newItem: Campaign) => {
  campaigns.value.unshift(newItem)
  toast.success('✅ Yeni kampanya eklendi!')
}

const edit = (item: Campaign) => {
  editingItem.value = item
  editing.value = true
}

const updateCampaign = (updated: Campaign) => {
  const index = campaigns.value.findIndex(c => c.id === updated.id)
  if (index !== -1) campaigns.value[index] = updated
  toast.success('✏️ Kampanya güncellendi!')
}

const askDelete = (item: Campaign) => {
  deletingItem.value = item
  deleting.value = true
}

const removeCampaign = (id: number) => {
  campaigns.value = campaigns.value.filter(c => c.id !== id)
  toast.success('🗑 Kampanya silindi!')
}
</script>
<template>
  <div :aria-busy="loading">
    <div v-if="loading" class="py-6">
      <LoadingSkeleton variant="list" :lines="5" />
    </div>
    <EmptyState v-else-if="loadError" :title="'Kampanya verisi alınamadı'" :description="loadError">
      <template #icon>
        <span class="text-5xl">⚠️</span>
      </template>
    </EmptyState>
    <EmptyState v-else-if="filteredCampaigns.length === 0" :title="'Kampanya bulunamadı'" :description="'Kriterlere uygun kampanya yok.'">
      <template #icon>
        <span class="text-5xl">🗒️</span>
      </template>
    </EmptyState>
    <div v-else>
      <!-- ...existing campaign list UI... -->
    </div>
  </div>
</template>