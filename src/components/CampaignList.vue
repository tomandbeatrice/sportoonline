<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
      <BadgeIcon name="clipboard-list" cls="w-6 h-6 text-blue-600" /> Kampanya Listesi
    </h2>

    <CampaignCreate @created="addCampaign" />

    <div class="flex gap-4 my-6">
      <input v-model="search" type="text" placeholder="Kampanya adı ara..." class="input input-bordered w-full max-w-xs" />
      <select v-model="sortKey" class="select select-bordered">
        <option value="name">Ad</option>
        <option value="start_date">Başlangıç</option>
        <option value="end_date">Bitiş</option>
      </select>
      <button class="btn btn-outline btn-sm flex items-center gap-2" @click="exportCSV">
        <BadgeIcon name="download" cls="w-4 h-4" /> CSV Export
      </button>
    </div>

    <ul class="list-disc pl-4 space-y-2">
      <li v-for="item in paginatedCampaigns" :key="item.id" @click="selectCampaign(item)" class="cursor-pointer hover:text-blue-600">
        <strong>{{ item.name }}</strong> — {{ item.status }}
        <span class="text-sm text-gray-500">({{ item.start_date }} → {{ item.end_date }})</span>
        <button class="btn btn-xs btn-info ml-2" @click.stop="toggleStatus(item.id)">Durumu Değiştir</button>
      </li>
    </ul>

    <div class="flex gap-2 mt-6">
      <button class="btn btn-sm" :disabled="currentPage === 1" @click="prevPage">←</button>
      <span class="text-sm">Sayfa {{ currentPage }} / {{ totalPages }}</span>
      <button class="btn btn-sm" :disabled="currentPage === totalPages" @click="nextPage">→</button>
    </div>

    <dialog v-if="selected" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg">{{ selected.name }}</h3>
        <p class="py-2">Durum: {{ selected.status }}</p>
        <p class="py-2">Tarih: {{ selected.start_date }} → {{ selected.end_date }}</p>
        <p class="py-2">Skor: {{ selected.score }}</p>
        <div class="modal-action">
          <button class="btn" @click="selected = null">Kapat</button>
        </div>
      </div>
    </dialog>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useApi } from '@/composables/useApi'
import CampaignCreate from './CampaignCreate.vue'
import { useToast } from 'vue-toastification'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

interface Campaign {
  id: number
  name: string
  status: string
  start_date: string
  end_date: string
  score?: number
}

const { get, post } = useApi()
const toast = useToast()

const campaigns = ref<Campaign[]>([])
const search = ref('')
const sortKey = ref<'name' | 'start_date' | 'end_date'>('name')
const selected = ref<Campaign | null>(null)
const currentPage = ref(1)
const pageSize = 10

onMounted(async () => {
  try {
    campaigns.value = await get('/admin/campaign-list')
  } catch (e) {
    toast.error('Kampanya listesi alınamadı!')
  }
})

const filteredCampaigns = computed(() => {
  return [...campaigns.value]
    .filter(c => c.name.toLowerCase().includes(search.value.toLowerCase()))
    .sort((a, b) => {
      if (sortKey.value === 'name') {
        return a.name.localeCompare(b.name)
      } else {
        // Tarih karşılaştırması
        return new Date(a[sortKey.value]).getTime() - new Date(b[sortKey.value]).getTime()
      }
    })
})

const paginatedCampaigns = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  return filteredCampaigns.value.slice(start, start + pageSize)
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredCampaigns.value.length / pageSize)))

function prevPage() {
  if (currentPage.value > 1) currentPage.value--
}
function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++
}

function selectCampaign(item: Campaign) {
  selected.value = item
}

async function toggleStatus(id: number) {
  try {
    await post('/admin/campaign-toggle-status', { id })
    campaigns.value = await get('/admin/campaign-list')
    toast.success('Durum güncellendi!')
  } catch (e) {
    toast.error('Durum güncellenemedi!')
  }
}

function exportCSV() {
  const header = ['ID', 'Ad', 'Durum', 'Başlangıç', 'Bitiş']
  const rows = campaigns.value.map(c => [c.id, c.name, c.status, c.start_date, c.end_date])
  const csv = [header, ...rows].map(r => r.join(',')).join('\n')

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = 'kampanyalar.csv'
  link.click()
}

function addCampaign(newItem: Campaign) {
  campaigns.value.unshift(newItem)
  toast.success('✅ Yeni kampanya eklendi!')
}
</script>

<style scoped>
.modal-open {
  display: block;
}
</style>