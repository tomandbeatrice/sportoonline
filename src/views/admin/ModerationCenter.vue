<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
            🛡️ Moderasyon Merkezi
            <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">AI</span>
          </h1>
          <p class="text-slate-500 text-sm mt-1">Yorum, soru ve içerik moderasyonu</p>
        </div>
        <div class="flex gap-3">
          <button @click="autoModerate" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
            🤖 Otomatik Moderasyon
          </button>
          <button @click="showPolicyModal = true" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition">
            📋 Politika Kuralları
          </button>
        </div>
      </div>
    </header>

    <!-- Stats -->
    <div class="p-6">
      <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Bekleyen İnceleme</p>
              <p class="text-2xl font-bold text-orange-600">{{ pendingReviews.length }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center text-2xl">⏳</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Otomatik Bayrak</p>
              <p class="text-2xl font-bold text-red-600">{{ autoFlagged.length }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-2xl">🚩</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">İhbar Edilen</p>
              <p class="text-2xl font-bold text-yellow-600">{{ reported.length }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-2xl">⚠️</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Bugün Onaylanan</p>
              <p class="text-2xl font-bold text-green-600">{{ approvedToday }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl">✅</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-slate-600 mb-1">Bugün Reddedilen</p>
              <p class="text-2xl font-bold text-slate-600">{{ rejectedToday }}</p>
            </div>
            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center text-2xl">❌</div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="bg-white border border-slate-200 rounded-lg mb-6">
        <div class="flex border-b border-slate-200">
          <button @click="activeTab = 'pending'" class="flex-1 py-3 font-medium border-b-2 transition" :class="activeTab === 'pending' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600'">
            ⏳ Bekleyen ({{ pendingReviews.length }})
          </button>
          <button @click="activeTab = 'flagged'" class="flex-1 py-3 font-medium border-b-2 transition" :class="activeTab === 'flagged' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600'">
            🚩 Bayraklanan ({{ autoFlagged.length }})
          </button>
          <button @click="activeTab = 'reported'" class="flex-1 py-3 font-medium border-b-2 transition" :class="activeTab === 'reported' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600'">
            ⚠️ İhbar Edilen ({{ reported.length }})
          </button>
          <button @click="activeTab = 'approved'" class="flex-1 py-3 font-medium border-b-2 transition" :class="activeTab === 'approved' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-600'">
            ✅ Onaylanan
          </button>
        </div>

        <!-- Content Area -->
        <div class="p-6">
          <!-- Filters -->
          <div class="flex gap-3 mb-6">
            <input v-model="searchQuery" type="text" placeholder="İçerik ara..." class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
            <select v-model="contentType" class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              <option value="">Tüm Tipler</option>
              <option value="review">Yorumlar</option>
              <option value="question">Sorular</option>
              <option value="answer">Cevaplar</option>
            </select>
            <select v-model="sortBy" class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-orange-500">
              <option value="date">Tarihe Göre</option>
              <option value="severity">Önceliğe Göre</option>
            </select>
          </div>

          <!-- Content List -->
          <div class="space-y-4">
            <div v-for="item in filteredContent" :key="item.id" class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition">
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-600">
                  {{ item.author.charAt(0).toUpperCase() }}
                </div>
                <div class="flex-1">
                  <div class="flex items-start justify-between mb-2">
                    <div>
                      <div class="flex items-center gap-2">
                        <span class="font-medium text-slate-800">{{ item.author }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-medium" :class="getTypeBadge(item.type)">{{ getTypeLabel(item.type) }}</span>
                        <span v-if="item.auto_flagged" class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs font-medium">🤖 Otomatik Bayrak</span>
                      </div>
                      <p class="text-xs text-slate-500">{{ item.product_name }} - {{ formatDate(item.created_at) }}</p>
                    </div>
                    <div v-if="item.severity" class="px-3 py-1 rounded-full text-xs font-bold" :class="getSeverityBadge(item.severity)">
                      {{ getSeverityLabel(item.severity) }}
                    </div>
                  </div>

                  <div class="mb-3 p-4 bg-slate-50 rounded-lg">
                    <p class="text-slate-800">{{ item.content }}</p>
                  </div>

                  <div v-if="item.flag_reasons?.length" class="mb-3 flex flex-wrap gap-2">
                    <span v-for="reason in item.flag_reasons" :key="reason" class="px-2 py-1 bg-red-50 text-red-700 rounded text-xs">
                      {{ reason }}
                    </span>
                  </div>

                  <div v-if="item.report_reason" class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                      <strong>İhbar Nedeni:</strong> {{ item.report_reason }}
                    </p>
                    <p v-if="item.report_count" class="text-xs text-yellow-600 mt-1">{{ item.report_count }} kullanıcı ihbar etti</p>
                  </div>

                  <div class="flex gap-2">
                    <button @click="approveContent(item)" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">✅ Onayla</button>
                    <button @click="rejectContent(item)" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">❌ Reddet</button>
                    <button @click="escalateContent(item)" class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition">⬆️ Yöneticiye İlet</button>
                    <button @click="viewDetails(item)" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition">🔍 Detaylar</button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="filteredContent.length === 0" class="text-center py-12">
              <div class="text-6xl mb-4">✨</div>
              <h3 class="text-xl font-bold text-slate-800 mb-2">Harika! İncelenecek içerik yok</h3>
              <p class="text-slate-500">Tüm içerikler gözden geçirildi.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Policy Modal -->
    <Transition name="modal">
      <div v-if="showPolicyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="showPolicyModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-slate-200 flex justify-between items-center sticky top-0 bg-white">
            <h2 class="text-xl font-bold text-slate-800">Moderasyon Politika Kuralları</h2>
            <button @click="showPolicyModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
          </div>

          <div class="p-6 space-y-6">
            <div v-for="policy in policies" :key="policy.id" class="border border-slate-200 rounded-lg p-6">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="text-lg font-bold text-slate-800 mb-1">{{ policy.name }}</h3>
                  <p class="text-sm text-slate-500">{{ policy.description }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold" :class="policy.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'">
                  {{ policy.is_active ? 'Aktif' : 'Pasif' }}
                </span>
              </div>

              <div class="space-y-2">
                <div v-for="rule in policy.rules" :key="rule.keyword" class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                  <div class="flex-1">
                    <p class="font-medium text-slate-800">{{ rule.keyword }}</p>
                    <p class="text-xs text-slate-500">{{ rule.action }} - Şiddet: {{ rule.severity }}</p>
                  </div>
                  <button class="text-slate-400 hover:text-slate-600">⚙️</button>
                </div>
              </div>
            </div>
          </div>

          <div class="p-6 border-t border-slate-200 flex justify-end gap-3">
            <button @click="showPolicyModal = false" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition">Kapat</button>
            <button @click="savePolicies" class="px-6 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">Kaydet</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const activeTab = ref('pending')
const searchQuery = ref('')
const contentType = ref('')
const sortBy = ref('date')
const showPolicyModal = ref(false)

// Content Data
const allContent = ref([
  {
    id: 1,
    type: 'review',
    author: 'Ahmet Y.',
    product_name: 'iPhone 14 Pro',
    content: 'Ürün çok güzel ama kargo çok yavaştı, berbat bir hizmet.',
    created_at: '2025-12-11T10:30:00',
    status: 'pending',
    auto_flagged: false,
    flag_reasons: [],
    severity: null,
    report_reason: null,
    report_count: 0
  },
  {
    id: 2,
    type: 'review',
    author: 'Mehmet K.',
    product_name: 'Samsung Galaxy S23',
    content: 'Bu ürün tam bir sahtekarlık! Para çöp! Kesinlikle almayın!',
    created_at: '2025-12-11T09:15:00',
    status: 'flagged',
    auto_flagged: true,
    flag_reasons: ['Saldırgan dil', 'Aşırı olumsuz ifade', 'Küfür'],
    severity: 'high',
    report_reason: null,
    report_count: 0
  },
  {
    id: 3,
    type: 'question',
    author: 'Ayşe T.',
    product_name: 'MacBook Pro 16"',
    content: 'Ürünün garantisi var mı? Türkiye garantisi mi?',
    created_at: '2025-12-11T08:45:00',
    status: 'pending',
    auto_flagged: false,
    flag_reasons: [],
    severity: null,
    report_reason: null,
    report_count: 0
  },
  {
    id: 4,
    type: 'review',
    author: 'Fatma S.',
    product_name: 'Nike Air Max',
    content: 'Satıcı dolandırıcı, sahte ürün gönderdi. Polise şikayet ettim.',
    created_at: '2025-12-10T16:20:00',
    status: 'reported',
    auto_flagged: true,
    flag_reasons: ['Dolandırıcılık iddiası', 'Sahtecilik iddiası'],
    severity: 'critical',
    report_reason: 'Satıcıyı haksız suçluyor',
    report_count: 2
  }
])

const pendingReviews = computed(() => allContent.value.filter(c => c.status === 'pending'))
const autoFlagged = computed(() => allContent.value.filter(c => c.status === 'flagged'))
const reported = computed(() => allContent.value.filter(c => c.status === 'reported'))
const approvedToday = ref(45)
const rejectedToday = ref(12)

const filteredContent = computed(() => {
  let result = allContent.value

  // Filter by tab
  if (activeTab.value === 'pending') {
    result = result.filter(c => c.status === 'pending')
  } else if (activeTab.value === 'flagged') {
    result = result.filter(c => c.status === 'flagged')
  } else if (activeTab.value === 'reported') {
    result = result.filter(c => c.status === 'reported')
  } else if (activeTab.value === 'approved') {
    result = result.filter(c => c.status === 'approved')
  }

  // Filter by content type
  if (contentType.value) {
    result = result.filter(c => c.type === contentType.value)
  }

  // Search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(c =>
      c.content.toLowerCase().includes(query) ||
      c.author.toLowerCase().includes(query) ||
      c.product_name.toLowerCase().includes(query)
    )
  }

  // Sort
  if (sortBy.value === 'date') {
    result = result.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
  } else if (sortBy.value === 'severity') {
    const severityOrder = { critical: 0, high: 1, medium: 2, low: 3 }
    result = result.sort((a, b) => {
      const aVal = a.severity ? severityOrder[a.severity as keyof typeof severityOrder] : 999
      const bVal = b.severity ? severityOrder[b.severity as keyof typeof severityOrder] : 999
      return aVal - bVal
    })
  }

  return result
})

// Policies
const policies = ref([
  {
    id: 1,
    name: 'Küfür ve Hakaret',
    description: 'Küfür, hakaret ve saldırgan ifadeler',
    is_active: true,
    rules: [
      { keyword: 'küfür_listesi', action: 'auto_reject', severity: 'high' },
      { keyword: 'saldırgan_ifadeler', action: 'flag_review', severity: 'medium' }
    ]
  },
  {
    id: 2,
    name: 'Spam ve Reklam',
    description: 'İstenmeyen reklam ve spam içerikler',
    is_active: true,
    rules: [
      { keyword: 'whatsapp|telegram|instagram', action: 'auto_reject', severity: 'high' },
      { keyword: 'indirim_kodu_paylaşımı', action: 'flag_review', severity: 'low' }
    ]
  },
  {
    id: 3,
    name: 'Sahtecilik ve Dolandırıcılık İddiaları',
    description: 'Satıcıyı suçlayan iddiaları',
    is_active: true,
    rules: [
      { keyword: 'dolandırıcı|sahte|fake', action: 'escalate', severity: 'critical' }
    ]
  }
])

// Actions
const getTypeBadge = (type: string) => {
  switch (type) {
    case 'review': return 'bg-blue-100 text-blue-700'
    case 'question': return 'bg-purple-100 text-purple-700'
    case 'answer': return 'bg-green-100 text-green-700'
    default: return 'bg-slate-100 text-slate-700'
  }
}

const getTypeLabel = (type: string) => {
  switch (type) {
    case 'review': return 'Yorum'
    case 'question': return 'Soru'
    case 'answer': return 'Cevap'
    default: return type
  }
}

const getSeverityBadge = (severity: string) => {
  switch (severity) {
    case 'critical': return 'bg-red-600 text-white'
    case 'high': return 'bg-orange-600 text-white'
    case 'medium': return 'bg-yellow-600 text-white'
    case 'low': return 'bg-blue-600 text-white'
    default: return 'bg-slate-600 text-white'
  }
}

const getSeverityLabel = (severity: string) => {
  switch (severity) {
    case 'critical': return 'KRİTİK'
    case 'high': return 'YÜKSEK'
    case 'medium': return 'ORTA'
    case 'low': return 'DÜŞÜK'
    default: return severity?.toUpperCase()
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('tr-TR')
}

const approveContent = (item: any) => {
  item.status = 'approved'
  approvedToday.value++
  toast.success('İçerik onaylandı')
}

const rejectContent = (item: any) => {
  item.status = 'rejected'
  rejectedToday.value++
  toast.success('İçerik reddedildi')
}

const escalateContent = (item: any) => {
  toast.info('İçerik yöneticiye iletildi')
}

const viewDetails = (item: any) => {
  toast.info('Detaylar görüntüleniyor')
}

const autoModerate = () => {
  toast.info('Otomatik moderasyon başlatıldı...')
  setTimeout(() => {
    toast.success('Otomatik moderasyon tamamlandı')
  }, 2000)
}

const savePolicies = () => {
  toast.success('Politika kuralları kaydedildi')
  showPolicyModal.value = false
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
