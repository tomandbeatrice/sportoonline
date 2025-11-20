<template>
  <div class="seller-applications p-6">
    <h2 class="text-2xl font-bold mb-6">Satıcı Başvuruları</h2>

    <!-- Filtreler -->
    <div class="mb-6 flex gap-4">
      <button
        @click="filterStatus = 'all'"
        :class="['px-4 py-2 rounded', filterStatus === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200']"
      >
        Tümü ({{ applications.length }})
      </button>
      <button
        @click="filterStatus = 'pending'"
        :class="['px-4 py-2 rounded', filterStatus === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-200']"
      >
        Bekleyenler ({{ pendingCount }})
      </button>
      <button
        @click="filterStatus = 'approved'"
        :class="['px-4 py-2 rounded', filterStatus === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-200']"
      >
        Onaylananlar ({{ approvedCount }})
      </button>
      <button
        @click="filterStatus = 'rejected'"
        :class="['px-4 py-2 rounded', filterStatus === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200']"
      >
        Reddedilenler ({{ rejectedCount }})
      </button>
    </div>

    <!-- Başvurular Tablosu -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
    </div>

    <div v-else-if="filteredApplications.length === 0" class="text-center py-8 text-gray-500">
      Başvuru bulunamadı.
    </div>

    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satıcı Adı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-posta</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="app in filteredApplications" :key="app.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ app.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ app.user?.name || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ app.user?.email || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatDate(app.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-3 py-1 rounded-full text-xs font-medium',
                  app.status === 'approved' ? 'bg-green-100 text-green-800' :
                  app.status === 'rejected' ? 'bg-red-100 text-red-800' :
                  'bg-yellow-100 text-yellow-800'
                ]"
              >
                {{ getStatusLabel(app.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <div v-if="app.status === 'pending'" class="flex gap-2">
                <button
                  @click="updateStatus(app.id, 'approved')"
                  class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
                >
                  Onayla
                </button>
                <button
                  @click="updateStatus(app.id, 'rejected')"
                  class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                >
                  Reddet
                </button>
              </div>
              <span v-else class="text-gray-400">-</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const applications = ref([])
const loading = ref(true)
const filterStatus = ref('all')

const filteredApplications = computed(() => {
  if (filterStatus.value === 'all') return applications.value
  return applications.value.filter(app => app.status === filterStatus.value)
})

const pendingCount = computed(() => applications.value.filter(app => app.status === 'pending').length)
const approvedCount = computed(() => applications.value.filter(app => app.status === 'approved').length)
const rejectedCount = computed(() => applications.value.filter(app => app.status === 'rejected').length)

function getStatusLabel(status) {
  const labels = {
    pending: 'Bekliyor',
    approved: 'Onaylandı',
    rejected: 'Reddedildi'
  }
  return labels[status] || status
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

async function loadApplications() {
  loading.value = true
  try {
    const response = await axios.get('/api/admin/seller-applications')
    applications.value = response.data
  } catch (error) {
    console.error('Başvurular yüklenirken hata:', error)
    alert('Başvurular yüklenemedi')
  } finally {
    loading.value = false
  }
}

async function updateStatus(vendorId, newStatus) {
  const confirmMsg = newStatus === 'approved' 
    ? 'Bu başvuruyu onaylamak istediğinize emin misiniz?' 
    : 'Bu başvuruyu reddetmek istediğinize emin misiniz?'
  
  if (!confirm(confirmMsg)) return

  try {
    await axios.patch(`/api/admin/vendors/${vendorId}/status`, { status: newStatus })
    alert(newStatus === 'approved' ? 'Başvuru onaylandı!' : 'Başvuru reddedildi!')
    await loadApplications()
  } catch (error) {
    console.error('Durum güncellenirken hata:', error)
    alert('İşlem başarısız oldu')
  }
}

onMounted(() => {
  loadApplications()
})
</script>

<style scoped>
/* Özel stiller gerekirse buraya eklenebilir */
</style>
