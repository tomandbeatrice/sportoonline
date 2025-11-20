<template>
  <div class="campaign-management p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Kampanya Yönetimi</h1>
      <button
        @click="openCreateModal"
        class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold"
      >
        + Yeni Kampanya
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Aktif Kampanya</div>
        <div class="text-2xl font-bold text-green-600">{{ stats.active || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Toplam Kullanım</div>
        <div class="text-2xl font-bold text-blue-600">{{ stats.total_usage || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Toplam İndirim</div>
        <div class="text-2xl font-bold text-purple-600">₺{{ formatMoney(stats.total_discount || 0) }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Yaklaşan Son</div>
        <div class="text-2xl font-bold text-orange-600">{{ stats.expiring_soon || 0 }}</div>
      </div>
    </div>

    <!-- Campaigns List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kampanya Adı</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tür</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İndirim</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kupon Kodu</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih Aralığı</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanım</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="campaign in campaigns" :key="campaign.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ campaign.name }}</div>
                <div class="text-xs text-gray-500">{{ campaign.description }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                  {{ getCampaignTypeLabel(campaign.type) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                {{ formatDiscount(campaign) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <code class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">{{ campaign.coupon_code }}</code>
                  <button
                    @click="copyCouponCode(campaign.coupon_code)"
                    class="text-gray-400 hover:text-gray-600"
                    title="Kopyala"
                  >
                    📋
                  </button>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                <div>{{ formatDate(campaign.start_date) }}</div>
                <div class="text-xs text-gray-500">{{ formatDate(campaign.end_date) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ campaign.used_count }} / {{ campaign.usage_limit || '∞' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button
                  @click="toggleCampaignStatus(campaign)"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                  :class="campaign.is_active ? 'bg-green-500' : 'bg-gray-300'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                    :class="campaign.is_active ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="viewStats(campaign)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  İstatistik
                </button>
                <button
                  @click="openEditModal(campaign)"
                  class="text-green-600 hover:text-green-900 mr-3"
                >
                  Düzenle
                </button>
                <button
                  @click="deleteCampaign(campaign)"
                  class="text-red-600 hover:text-red-900"
                >
                  Sil
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="campaigns.length === 0" class="text-center py-12 text-gray-500">
        Henüz kampanya oluşturmadınız
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
          {{ editingCampaign ? 'Kampanyayı Düzenle' : 'Yeni Kampanya Oluştur' }}
        </h2>

        <form @submit.prevent="saveCampaign" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Kampanya Adı *</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
              <textarea
                v-model="form.description"
                rows="2"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kampanya Türü *</label>
              <select
                v-model="form.type"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="percentage">Yüzde İndirim</option>
                <option value="fixed">Sabit Tutar İndirim</option>
                <option value="buy_x_get_y">X Al Y Öde</option>
                <option value="free_shipping">Ücretsiz Kargo</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ form.type === 'percentage' ? 'İndirim Yüzdesi (%)' : 'İndirim Tutarı (₺)' }} *
              </label>
              <input
                v-model.number="form.discount_value"
                type="number"
                step="0.01"
                min="0"
                :max="form.type === 'percentage' ? 100 : undefined"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kupon Kodu</label>
              <input
                v-model="form.coupon_code"
                type="text"
                placeholder="Boş bırakılırsa otomatik oluşur"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Min. Sepet Tutarı (₺)</label>
              <input
                v-model.number="form.min_purchase_amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi *</label>
              <input
                v-model="form.start_date"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi *</label>
              <input
                v-model="form.end_date"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Toplam Kullanım Limiti</label>
              <input
                v-model.number="form.usage_limit"
                type="number"
                min="1"
                placeholder="Sınırsız"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kişi Başı Kullanım *</label>
              <input
                v-model.number="form.usage_per_customer"
                type="number"
                min="1"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Geçerli Olduğu Ürünler *</label>
              <select
                v-model="form.applies_to"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="all_products">Tüm Ürünler</option>
                <option value="specific_products">Belirli Ürünler</option>
                <option value="specific_categories">Belirli Kategoriler</option>
              </select>
            </div>

            <div class="col-span-2">
              <label class="flex items-center gap-2">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="rounded border-gray-300"
                />
                <span class="text-sm font-medium text-gray-700">Kampanyayı Aktif Yap</span>
              </label>
            </div>
          </div>

          <div class="flex gap-3 pt-4 border-t">
            <button
              type="button"
              @click="closeModal"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="flex-1 px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Stats Modal -->
    <div v-if="showStatsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold text-gray-900">Kampanya İstatistikleri</h2>
          <button @click="showStatsModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-if="campaignStats" class="space-y-4">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
              <div class="text-sm text-blue-600">Toplam Kullanım</div>
              <div class="text-2xl font-bold text-blue-900">{{ campaignStats.total_usage }}</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
              <div class="text-sm text-green-600">Toplam İndirim</div>
              <div class="text-2xl font-bold text-green-900">₺{{ formatMoney(campaignStats.total_discount_given) }}</div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
              <div class="text-sm text-purple-600">Benzersiz Müşteri</div>
              <div class="text-2xl font-bold text-purple-900">{{ campaignStats.unique_customers }}</div>
            </div>
            <div class="bg-orange-50 rounded-lg p-4">
              <div class="text-sm text-orange-600">Ort. İndirim</div>
              <div class="text-2xl font-bold text-orange-900">₺{{ formatMoney(campaignStats.avg_discount_per_order) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const campaigns = ref([])
const showModal = ref(false)
const showStatsModal = ref(false)
const editingCampaign = ref(null)
const campaignStats = ref(null)
const saving = ref(false)

const stats = computed(() => {
  const now = new Date()
  const soon = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000) // 7 days

  return {
    active: campaigns.value.filter(c => c.is_active).length,
    total_usage: campaigns.value.reduce((sum, c) => sum + (c.used_count || 0), 0),
    total_discount: campaigns.value.reduce((sum, c) => sum + (c.usages_count || 0) * (c.discount_value || 0), 0),
    expiring_soon: campaigns.value.filter(c => {
      const endDate = new Date(c.end_date)
      return endDate > now && endDate < soon
    }).length
  }
})

const form = ref({
  name: '',
  description: '',
  type: 'percentage',
  discount_value: 0,
  min_purchase_amount: 0,
  coupon_code: '',
  usage_limit: null,
  usage_per_customer: 1,
  start_date: '',
  end_date: '',
  is_active: true,
  applies_to: 'all_products',
  product_ids: [],
  category_ids: []
})

const loadCampaigns = async () => {
  try {
    const { data } = await axios.get('/api/campaigns')
    campaigns.value = data
  } catch (error) {
    console.error('Failed to load campaigns:', error)
  }
}

const openCreateModal = () => {
  editingCampaign.value = null
  form.value = {
    name: '',
    description: '',
    type: 'percentage',
    discount_value: 0,
    min_purchase_amount: 0,
    coupon_code: '',
    usage_limit: null,
    usage_per_customer: 1,
    start_date: '',
    end_date: '',
    is_active: true,
    applies_to: 'all_products',
    product_ids: [],
    category_ids: []
  }
  showModal.value = true
}

const openEditModal = (campaign) => {
  editingCampaign.value = campaign
  form.value = {
    name: campaign.name,
    description: campaign.description,
    type: campaign.type,
    discount_value: campaign.discount_value,
    min_purchase_amount: campaign.min_purchase_amount,
    coupon_code: campaign.coupon_code,
    usage_limit: campaign.usage_limit,
    usage_per_customer: campaign.usage_per_customer,
    start_date: campaign.start_date ? campaign.start_date.substring(0, 16) : '',
    end_date: campaign.end_date ? campaign.end_date.substring(0, 16) : '',
    is_active: campaign.is_active,
    applies_to: campaign.applies_to,
    product_ids: campaign.product_ids || [],
    category_ids: campaign.category_ids || []
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingCampaign.value = null
}

const saveCampaign = async () => {
  saving.value = true
  try {
    if (editingCampaign.value) {
      await axios.put(`/api/campaigns/${editingCampaign.value.id}`, form.value)
    } else {
      await axios.post('/api/campaigns', form.value)
    }
    await loadCampaigns()
    closeModal()
  } catch (error) {
    console.error('Failed to save campaign:', error)
    alert(error.response?.data?.error || 'Kampanya kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const toggleCampaignStatus = async (campaign) => {
  try {
    const { data } = await axios.post(`/api/campaigns/${campaign.id}/toggle-active`)
    campaign.is_active = data.is_active
  } catch (error) {
    console.error('Failed to toggle status:', error)
  }
}

const deleteCampaign = async (campaign) => {
  if (!confirm(`"${campaign.name}" kampanyasını silmek istediğinizden emin misiniz?`)) return

  try {
    await axios.delete(`/api/campaigns/${campaign.id}`)
    await loadCampaigns()
  } catch (error) {
    console.error('Failed to delete campaign:', error)
    alert('Kampanya silinemedi')
  }
}

const viewStats = async (campaign) => {
  try {
    const { data } = await axios.get(`/api/campaigns/${campaign.id}/stats`)
    campaignStats.value = data
    showStatsModal.value = true
  } catch (error) {
    console.error('Failed to load stats:', error)
  }
}

const copyCouponCode = (code) => {
  navigator.clipboard.writeText(code)
  alert('Kupon kodu kopyalandı: ' + code)
}

const getCampaignTypeLabel = (type) => {
  const labels = {
    percentage: 'Yüzde',
    fixed: 'Sabit',
    buy_x_get_y: 'X Al Y Öde',
    free_shipping: 'Ücretsiz Kargo'
  }
  return labels[type] || type
}

const formatDiscount = (campaign) => {
  if (campaign.type === 'percentage') {
    return `%${campaign.discount_value}`
  }
  if (campaign.type === 'fixed') {
    return `₺${formatMoney(campaign.discount_value)}`
  }
  return '-'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount || 0)
}

onMounted(() => {
  loadCampaigns()
})
</script>
