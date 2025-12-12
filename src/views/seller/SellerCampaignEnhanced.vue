<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
          <h1 class="text-2xl font-bold text-slate-900">Kampanya Yönetimi</h1>
          <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">SATICI</span>
        </div>
        <button @click="openCampaignModal()" class="px-6 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700">
          ➕ Yeni Kampanya Oluştur
        </button>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Stats -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <p class="text-2xl font-bold text-slate-900">{{ stats.active }}</p>
          <p class="text-xs text-slate-500">Aktif Kampanya</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 shadow-sm border border-green-100">
          <p class="text-2xl font-bold text-green-700">{{ formatPrice(stats.totalRevenue) }} TL</p>
          <p class="text-xs text-green-600">Toplam Gelir</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 shadow-sm border border-blue-100">
          <p class="text-2xl font-bold text-blue-700">{{ stats.totalOrders }}</p>
          <p class="text-xs text-blue-600">Toplam Sipariş</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 shadow-sm border border-purple-100">
          <p class="text-2xl font-bold text-purple-700">%{{ stats.avgConversion }}</p>
          <p class="text-xs text-purple-600">Dönüşüm Oranı</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
        <div class="flex flex-wrap gap-4">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Kampanya adı ara..."
            class="flex-1 min-w-[200px] px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500"
          />
          <select v-model="statusFilter" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="">Tüm Durumlar</option>
            <option value="active">Aktif</option>
            <option value="scheduled">Zamanlanmış</option>
            <option value="paused">Duraklatılmış</option>
            <option value="ended">Sona Ermiş</option>
          </select>
          <select v-model="typeFilter" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="">Tüm Tipler</option>
            <option value="percentage">Yüzde İndirim</option>
            <option value="fixed">Sabit İndirim</option>
            <option value="free_shipping">Ücretsiz Kargo</option>
            <option value="bundle">Paket</option>
          </select>
        </div>
      </div>

      <!-- Campaigns Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div 
          v-for="campaign in filteredCampaigns" 
          :key="campaign.id"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition"
        >
          <!-- Status Badge -->
          <div class="relative">
            <div class="absolute top-3 right-3 z-10">
              <span :class="getStatusBadge(campaign.status)" class="px-3 py-1 rounded-full text-xs font-bold uppercase shadow-sm">
                {{ getStatusText(campaign.status) }}
              </span>
            </div>
            <div class="h-32 bg-gradient-to-br" :class="getGradientClass(campaign.type)"></div>
          </div>

          <div class="p-6">
            <!-- Campaign Info -->
            <div class="mb-4">
              <h3 class="text-lg font-bold text-slate-900 mb-2">{{ campaign.name }}</h3>
              <p class="text-sm text-slate-600 mb-3">{{ campaign.description }}</p>
              
              <!-- Campaign Type & Value -->
              <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl">{{ getCampaignIcon(campaign.type) }}</span>
                <span class="text-xl font-bold text-orange-600">
                  {{ campaign.type === 'percentage' ? `%${campaign.value}` : campaign.type === 'fixed' ? `${formatPrice(campaign.value)} TL` : 'Ücretsiz Kargo' }}
                </span>
              </div>

              <!-- Date Range -->
              <div class="flex items-center gap-2 text-sm text-slate-600 mb-4">
                <span>📅</span>
                <span>{{ campaign.startDate }} - {{ campaign.endDate }}</span>
              </div>

              <!-- Product Count -->
              <div class="flex items-center gap-2 text-sm text-slate-600 mb-4">
                <span>📦</span>
                <span>{{ campaign.productCount }} ürün</span>
              </div>

              <!-- Performance Metrics -->
              <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="text-center p-2 bg-slate-50 rounded-lg">
                  <p class="text-xs text-slate-500">Görüntülenme</p>
                  <p class="font-bold text-slate-900">{{ campaign.views || 0 }}</p>
                </div>
                <div class="text-center p-2 bg-slate-50 rounded-lg">
                  <p class="text-xs text-slate-500">Sipariş</p>
                  <p class="font-bold text-slate-900">{{ campaign.orders || 0 }}</p>
                </div>
                <div class="text-center p-2 bg-slate-50 rounded-lg">
                  <p class="text-xs text-slate-500">Gelir</p>
                  <p class="font-bold text-slate-900">{{ formatPrice(campaign.revenue || 0) }}</p>
                </div>
              </div>

              <!-- Progress Bar -->
              <div v-if="campaign.status === 'active'" class="mb-4">
                <div class="flex justify-between text-xs text-slate-600 mb-1">
                  <span>İlerleme</span>
                  <span>{{ calculateProgress(campaign) }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2">
                  <div :style="{width: calculateProgress(campaign) + '%'}" class="bg-orange-500 h-2 rounded-full transition-all"></div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2 pt-4 border-t border-slate-100">
              <button @click="viewCampaignDetails(campaign)" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm hover:bg-slate-200">
                📊 Detaylar
              </button>
              <button v-if="campaign.status === 'active'" @click="pauseCampaign(campaign)" class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg text-sm hover:bg-yellow-200">
                ⏸️
              </button>
              <button v-if="campaign.status === 'paused'" @click="resumeCampaign(campaign)" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200">
                ▶️
              </button>
              <button @click="openCampaignModal(campaign)" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200">
                ✏️
              </button>
              <button @click="deleteCampaign(campaign)" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200">
                🗑️
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Campaign Modal -->
    <transition name="modal">
      <div v-if="showCampaignModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeCampaignModal">
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
          <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">{{ editingCampaign ? 'Kampanya Düzenle' : 'Yeni Kampanya Oluştur' }}</h2>
            <button @click="closeCampaignModal" class="p-2 hover:bg-slate-100 rounded-lg">✕</button>
          </div>

          <form @submit.prevent="saveCampaign" class="p-6 space-y-6">
            <!-- Basic Info -->
            <div>
              <h3 class="font-bold text-slate-900 mb-4">Kampanya Bilgileri</h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Kampanya Adı *</label>
                  <input v-model="campaignForm.name" type="text" required class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="Yılbaşı İndirimi 2025" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
                  <textarea v-model="campaignForm.description" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg resize-none" placeholder="Kampanya açıklaması..."></textarea>
                </div>
              </div>
            </div>

            <!-- Campaign Type -->
            <div>
              <h3 class="font-bold text-slate-900 mb-4">İndirim Türü</h3>
              <div class="grid grid-cols-2 gap-4">
                <label 
                  v-for="type in campaignTypes" 
                  :key="type.id"
                  :class="['p-4 border-2 rounded-xl cursor-pointer transition', campaignForm.type === type.id ? 'border-orange-500 bg-orange-50' : 'border-slate-200 hover:border-slate-300']"
                >
                  <input type="radio" :value="type.id" v-model="campaignForm.type" class="sr-only" />
                  <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ type.icon }}</span>
                    <div>
                      <p class="font-medium text-slate-900">{{ type.name }}</p>
                      <p class="text-xs text-slate-500">{{ type.description }}</p>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Value & Rules -->
            <div>
              <h3 class="font-bold text-slate-900 mb-4">İndirim Değeri ve Kurallar</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-if="campaignForm.type !== 'free_shipping'">
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    {{ campaignForm.type === 'percentage' ? 'İndirim Yüzdesi (%)' : 'İndirim Tutarı (TL)' }} *
                  </label>
                  <input v-model.number="campaignForm.value" type="number" :step="campaignForm.type === 'percentage' ? '1' : '0.01'" required class="w-full px-4 py-2 border border-slate-200 rounded-lg" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Minimum Sepet Tutarı (TL)</label>
                  <input v-model.number="campaignForm.minAmount" type="number" step="0.01" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="0" />
                </div>
                <div v-if="campaignForm.type === 'percentage'">
                  <label class="block text-sm font-medium text-slate-700 mb-2">Maksimum İndirim (TL)</label>
                  <input v-model.number="campaignForm.maxDiscount" type="number" step="0.01" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="Sınırsız" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Kullanım Limiti</label>
                  <input v-model.number="campaignForm.usageLimit" type="number" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="Sınırsız" />
                </div>
              </div>
            </div>

            <!-- Date Range -->
            <div>
              <h3 class="font-bold text-slate-900 mb-4">Tarih Aralığı</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Başlangıç Tarihi *</label>
                  <input v-model="campaignForm.startDate" type="date" required class="w-full px-4 py-2 border border-slate-200 rounded-lg" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Bitiş Tarihi *</label>
                  <input v-model="campaignForm.endDate" type="date" required class="w-full px-4 py-2 border border-slate-200 rounded-lg" />
                </div>
              </div>
            </div>

            <!-- Product Selection -->
            <div>
              <h3 class="font-bold text-slate-900 mb-4">Ürün Seçimi</h3>
              <div class="space-y-3">
                <label class="flex items-center cursor-pointer">
                  <input type="radio" value="all" v-model="campaignForm.productScope" class="w-4 h-4 text-orange-600 border-slate-300" />
                  <span class="ml-2 text-sm text-slate-700">Tüm ürünlere uygula</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="radio" value="category" v-model="campaignForm.productScope" class="w-4 h-4 text-orange-600 border-slate-300" />
                  <span class="ml-2 text-sm text-slate-700">Kategoriye göre uygula</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="radio" value="specific" v-model="campaignForm.productScope" class="w-4 h-4 text-orange-600 border-slate-300" />
                  <span class="ml-2 text-sm text-slate-700">Belirli ürünlere uygula</span>
                </label>
              </div>

              <div v-if="campaignForm.productScope === 'category'" class="mt-4">
                <select v-model="campaignForm.selectedCategory" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                  <option value="">Kategori seçin</option>
                  <option value="electronics">Elektronik</option>
                  <option value="clothing">Giyim</option>
                  <option value="sports">Spor</option>
                </select>
              </div>

              <div v-if="campaignForm.productScope === 'specific'" class="mt-4">
                <button type="button" @click="showProductPicker = true" class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">
                  {{ campaignForm.selectedProducts.length }} ürün seçildi - Ürün Seç
                </button>
              </div>
            </div>

            <!-- Conflict Warning -->
            <div v-if="hasConflict" class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
              <div class="flex items-start gap-3">
                <span class="text-2xl">⚠️</span>
                <div>
                  <p class="font-medium text-amber-900 mb-1">Çakışma Uyarısı</p>
                  <p class="text-sm text-amber-700">Bu tarih aralığında başka bir kampanya var. Devam etmek istiyor musunuz?</p>
                </div>
              </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
              <button type="button" @click="closeCampaignModal" class="px-6 py-2 border border-slate-200 rounded-lg hover:bg-slate-50">İptal</button>
              <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                {{ editingCampaign ? 'Güncelle' : 'Kampanya Oluştur' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const searchQuery = ref('')
const statusFilter = ref('')
const typeFilter = ref('')
const showCampaignModal = ref(false)
const showProductPicker = ref(false)
const editingCampaign = ref<any>(null)
const hasConflict = ref(false)

const stats = ref({
  active: 5,
  totalRevenue: 127500,
  totalOrders: 342,
  avgConversion: 8.4
})

const campaigns = ref([
  {
    id: 1,
    name: 'Kış İndirimi 2025',
    description: 'Tüm kış ürünlerinde %30 indirim',
    type: 'percentage',
    value: 30,
    status: 'active',
    startDate: '2025-01-01',
    endDate: '2025-01-31',
    productCount: 45,
    views: 12450,
    orders: 189,
    revenue: 45600
  },
  {
    id: 2,
    name: 'Ücretsiz Kargo Kampanyası',
    description: '250 TL üzeri ücretsiz kargo',
    type: 'free_shipping',
    value: 0,
    status: 'active',
    startDate: '2025-01-15',
    endDate: '2025-02-15',
    productCount: 120,
    views: 8900,
    orders: 156,
    revenue: 38900
  },
  {
    id: 3,
    name: 'Elektronik Festivali',
    description: 'Elektronik ürünlerde 500 TL indirim',
    type: 'fixed',
    value: 500,
    status: 'paused',
    startDate: '2025-02-01',
    endDate: '2025-02-28',
    productCount: 23,
    views: 5600,
    orders: 78,
    revenue: 42000
  }
])

const campaignTypes = [
  { id: 'percentage', name: 'Yüzde İndirim', icon: '%', description: 'Ürün fiyatının %X kadarı indirim' },
  { id: 'fixed', name: 'Sabit İndirim', icon: '💰', description: 'Sabit tutar indirim' },
  { id: 'free_shipping', name: 'Ücretsiz Kargo', icon: '🚚', description: 'Kargo ücretsiz' },
  { id: 'bundle', name: 'Paket İndirimi', icon: '📦', description: 'Al 2 öde 1 tarzı' }
]

const campaignForm = ref({
  name: '',
  description: '',
  type: 'percentage',
  value: 0,
  minAmount: 0,
  maxDiscount: null,
  usageLimit: null,
  startDate: '',
  endDate: '',
  productScope: 'all',
  selectedCategory: '',
  selectedProducts: [] as number[]
})

// Computed
const filteredCampaigns = computed(() => {
  return campaigns.value.filter(c => {
    const matchSearch = !searchQuery.value || c.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchStatus = !statusFilter.value || c.status === statusFilter.value
    const matchType = !typeFilter.value || c.type === typeFilter.value
    return matchSearch && matchStatus && matchType
  })
})

// Methods
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(price)
}

const getStatusBadge = (status: string) => {
  const classes: Record<string, string> = {
    active: 'bg-green-500 text-white',
    scheduled: 'bg-blue-500 text-white',
    paused: 'bg-yellow-500 text-white',
    ended: 'bg-slate-500 text-white'
  }
  return classes[status] || classes.ended
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    active: 'Aktif',
    scheduled: 'Zamanlanmış',
    paused: 'Duraklatılmış',
    ended: 'Sona Erdi'
  }
  return texts[status] || status
}

const getGradientClass = (type: string) => {
  const gradients: Record<string, string> = {
    percentage: 'from-purple-400 to-purple-600',
    fixed: 'from-blue-400 to-blue-600',
    free_shipping: 'from-green-400 to-green-600',
    bundle: 'from-orange-400 to-orange-600'
  }
  return gradients[type] || gradients.percentage
}

const getCampaignIcon = (type: string) => {
  const icons: Record<string, string> = {
    percentage: '📊',
    fixed: '💵',
    free_shipping: '🚚',
    bundle: '📦'
  }
  return icons[type] || '🎁'
}

const calculateProgress = (campaign: any) => {
  const now = new Date().getTime()
  const start = new Date(campaign.startDate).getTime()
  const end = new Date(campaign.endDate).getTime()
  const progress = ((now - start) / (end - start)) * 100
  return Math.min(Math.max(progress, 0), 100).toFixed(0)
}

const openCampaignModal = (campaign?: any) => {
  if (campaign) {
    editingCampaign.value = campaign
    campaignForm.value = { ...campaign, selectedProducts: campaign.selectedProducts || [] }
  } else {
    editingCampaign.value = null
    campaignForm.value = {
      name: '',
      description: '',
      type: 'percentage',
      value: 0,
      minAmount: 0,
      maxDiscount: null,
      usageLimit: null,
      startDate: '',
      endDate: '',
      productScope: 'all',
      selectedCategory: '',
      selectedProducts: []
    }
  }
  showCampaignModal.value = true
}

const closeCampaignModal = () => {
  showCampaignModal.value = false
  editingCampaign.value = null
  hasConflict.value = false
}

const saveCampaign = () => {
  toast.success(editingCampaign.value ? 'Kampanya güncellendi' : 'Kampanya oluşturuldu')
  closeCampaignModal()
}

const viewCampaignDetails = (campaign: any) => {
  toast.info(`${campaign.name} detayları açılıyor...`)
}

const pauseCampaign = (campaign: any) => {
  campaign.status = 'paused'
  toast.success('Kampanya duraklatıldı')
}

const resumeCampaign = (campaign: any) => {
  campaign.status = 'active'
  toast.success('Kampanya yeniden başlatıldı')
}

const deleteCampaign = (campaign: any) => {
  if (confirm(`${campaign.name} silinecek. Emin misiniz?`)) {
    campaigns.value = campaigns.value.filter(c => c.id !== campaign.id)
    toast.success('Kampanya silindi')
  }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>
