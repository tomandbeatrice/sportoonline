<template>
  <div class="vendor-comparison p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Satıcı Karşılaştırma</h2>

    <!-- Vendor Selection -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Karşılaştırılacak Satıcıları Seçin</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="(slot, index) in 3" :key="index">
          <select
            v-model="selectedVendors[index]"
            @change="loadComparison"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          >
            <option :value="null">{{ index === 0 ? 'Satıcı 1 Seçin' : index === 1 ? 'Satıcı 2 Seçin' : 'Satıcı 3 Seçin' }}</option>
            <option
              v-for="vendor in availableVendors"
              :key="vendor.id"
              :value="vendor"
              :disabled="isVendorSelected(vendor.id, index)"
            >
              {{ vendor.name }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Comparison Table -->
    <div v-if="hasSelectedVendors" class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Kriter</th>
              <th
                v-for="(vendor, index) in selectedVendors"
                :key="index"
                class="px-6 py-4 text-center"
              >
                <div v-if="vendor" class="space-y-2">
                  <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full overflow-hidden">
                    <img v-if="vendor.logo_url" :src="vendor.logo_url" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center text-2xl">
                      🏪
                    </div>
                  </div>
                  <div class="font-bold text-gray-900">{{ vendor.name }}</div>
                </div>
                <div v-else class="text-gray-400 italic">Satıcı Seçilmedi</div>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <!-- Rating -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-semibold text-gray-900"><BadgeIcon name="star" cls="w-4 h-4 inline-block mr-2" /> Ortalama Puan</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`rating-${index}`"
                class="px-6 py-4 text-center"
              >
                <div v-if="vendor">
                  <div class="text-2xl font-bold text-yellow-600">{{ vendor.avg_rating?.toFixed(1) || 'N/A' }}</div>
                  <div class="text-xs text-gray-500">({{ vendor.review_count || 0 }} değerlendirme)</div>
                </div>
                <div v-else class="text-gray-400">-</div>
              </td>
            </tr>

            <!-- Products Count -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-semibold text-gray-900"><BadgeIcon name="box" cls="w-4 h-4 inline-block mr-2" /> Ürün Sayısı</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`products-${index}`"
                class="px-6 py-4 text-center"
              >
                <div v-if="vendor" class="text-xl font-bold text-blue-600">
                  {{ vendor.products_count || 0 }}
                </div>
                <div v-else class="text-gray-400">-</div>
              </td>
            </tr>

            <!-- Delivery Time -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-semibold text-gray-900">🚚 Ortalama Teslimat</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`delivery-${index}`"
                class="px-6 py-4 text-center"
              >
                <div v-if="vendor">
                  <div class="text-lg font-bold text-purple-600">
                    {{ vendor.avg_delivery_days || 2-3 }} gün
                  </div>
                </div>
                <div v-else class="text-gray-400">-</div>
              </td>
            </tr>

            <!-- Return Rate -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-semibold text-gray-900">↩️ İade Oranı</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`return-${index}`"
                class="px-6 py-4 text-center"
              >
                <div v-if="vendor">
                  <div class="text-lg font-bold" :class="getReturnRateClass(vendor.return_rate)">
                    %{{ vendor.return_rate?.toFixed(1) || 0 }}
                  </div>
                </div>
                <div v-else class="text-gray-400">-</div>
              </td>
            </tr>

            <!-- Response Time -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-semibold text-gray-900">💬 Yanıt Süresi</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`response-${index}`"
                class="px-6 py-4 text-center"
              >
                <div v-if="vendor">
                  <div class="text-lg font-bold text-green-600">
                    {{ vendor.avg_response_time || '< 1' }} saat
                  </div>
                </div>
                <div v-else class="text-gray-400">-</div>
              </td>
            </tr>

            <!-- Total Sales -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-semibold text-gray-900"><BadgeIcon name="money" cls="w-4 h-4 inline-block mr-2" /> Toplam Satış</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`sales-${index}`"
                class="px-6 py-4 text-center"
              >
                <div v-if="vendor">
                  <div class="text-lg font-bold text-green-600">
                    {{ vendor.total_orders || 0 }}+
                  </div>
                  <div class="text-xs text-gray-500">sipariş</div>
                </div>
                <div v-else class="text-gray-400">-</div>
              </td>
            </tr>

            <!-- Shipping Options -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-semibold text-gray-900">📮 Kargo Seçenekleri</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`shipping-${index}`"
                class="px-6 py-4"
              >
                <div v-if="vendor">
                  <div class="flex flex-wrap gap-1 justify-center">
                    <span
                      v-for="option in vendor.shipping_options"
                      :key="option"
                      class="px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded-full"
                    >
                      {{ option }}
                    </span>
                  </div>
                </div>
                <div v-else class="text-center text-gray-400">-</div>
              </td>
            </tr>

            <!-- Payment Methods -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-semibold text-gray-900">💳 Ödeme Yöntemleri</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`payment-${index}`"
                class="px-6 py-4"
              >
                <div v-if="vendor">
                  <div class="flex flex-wrap gap-1 justify-center">
                    <span
                      v-for="method in vendor.payment_methods"
                      :key="method"
                      class="px-2 py-1 bg-green-50 text-green-600 text-xs rounded-full"
                    >
                      {{ method }}
                    </span>
                  </div>
                </div>
                <div v-else class="text-center text-gray-400">-</div>
              </td>
            </tr>

            <!-- Member Since -->
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-semibold text-gray-900">📅 Üyelik Tarihi</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`member-${index}`"
                class="px-6 py-4 text-center"
              >
                <div v-if="vendor" class="text-sm text-gray-700">
                  {{ formatDate(vendor.created_at) }}
                </div>
                <div v-else class="text-gray-400">-</div>
              </td>
            </tr>

            <!-- Actions -->
            <tr>
              <td class="px-6 py-4 font-semibold text-gray-900">İşlemler</td>
              <td
                v-for="(vendor, index) in selectedVendors"
                :key="`actions-${index}`"
                class="px-6 py-4 text-center"
              >
                <div v-if="vendor" class="space-y-2">
                  <router-link
                    :to="`/vendors/${vendor.id}`"
                    class="block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm"
                  >
                    Mağazayı Görüntüle
                  </router-link>
                  <button
                    @click="viewReviews(vendor)"
                    class="w-full px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium text-sm"
                  >
                    Yorumları Gör
                  </button>
                </div>
                <div v-else class="text-gray-400">-</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
      <div class="text-6xl mb-4">🔍</div>
      <div class="text-xl font-semibold text-gray-900 mb-2">Satıcı Karşılaştırması</div>
      <div class="text-gray-500">En az iki satıcı seçerek karşılaştırmaya başlayın</div>
    </div>

    <!-- Reviews Modal -->
    <div v-if="selectedVendorForReviews" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b p-6 flex justify-between items-center">
          <h3 class="text-xl font-bold text-gray-900">{{ selectedVendorForReviews.name }} - Yorumlar</h3>
          <button @click="selectedVendorForReviews = null" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="p-6">
          <div class="space-y-4">
            <div
              v-for="review in vendorReviews"
              :key="review.id"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="flex items-center justify-between mb-2">
                <div class="font-semibold text-gray-900">{{ review.user_name }}</div>
                <div class="flex items-center gap-1">
                    <IconStar v-for="star in 5" :key="star" :cls="star <= review.rating ? 'w-5 h-5 text-yellow-400' : 'w-5 h-5 text-slate-200'" :filled="star <= review.rating" />
                  </div>
              </div>
              <div class="text-sm text-gray-700 mb-2">{{ review.comment }}</div>
              <div class="text-xs text-gray-500">{{ formatDate(review.created_at) }}</div>
            </div>
          </div>
          <div v-if="vendorReviews.length === 0" class="text-center py-8 text-gray-500">
            Henüz yorum yapılmamış
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
import IconStar from '@/components/icons/IconStar.vue'
import axios from 'axios'

const availableVendors = ref([])
const selectedVendors = ref([null, null, null])
const selectedVendorForReviews = ref(null)
const vendorReviews = ref([])

const hasSelectedVendors = computed(() => {
  return selectedVendors.value.filter(v => v !== null).length >= 2
})

const loadVendors = async () => {
  try {
    const { data } = await axios.get('/api/vendors')
    availableVendors.value = data.vendors || data
  } catch (error) {
    console.error('Satıcılar yüklenemedi:', error)
  }
}

const loadComparison = async () => {
  const vendorIds = selectedVendors.value.filter(v => v !== null).map(v => v.id)
  if (vendorIds.length < 2) return

  try {
    const { data } = await axios.get('/api/vendors/compare', {
      params: { ids: vendorIds.join(',') }
    })
    
    // Update vendors with comparison data
    selectedVendors.value = selectedVendors.value.map(v => {
      if (!v) return null
      const compData = data.find(d => d.id === v.id)
      return compData ? { ...v, ...compData } : v
    })
  } catch (error) {
    console.error('Karşılaştırma verisi yüklenemedi:', error)
  }
}

const isVendorSelected = (vendorId, currentIndex) => {
  return selectedVendors.value.some((v, i) => i !== currentIndex && v?.id === vendorId)
}

const viewReviews = async (vendor) => {
  selectedVendorForReviews.value = vendor
  try {
    const { data } = await axios.get(`/api/vendors/${vendor.id}/reviews`)
    vendorReviews.value = data.reviews || data
  } catch (error) {
    console.error('Yorumlar yüklenemedi:', error)
  }
}

const getReturnRateClass = (rate) => {
  if (!rate) return 'text-gray-500'
  if (rate < 2) return 'text-green-600'
  if (rate < 5) return 'text-yellow-600'
  return 'text-red-600'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(() => {
  loadVendors()
})
</script>
