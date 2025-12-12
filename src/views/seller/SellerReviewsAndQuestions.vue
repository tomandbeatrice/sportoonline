<template>
  <div class="min-h-screen bg-slate-50 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Yorumlar ve Sorular</h1>
          <p class="text-slate-500">Müşteri yorumlarını yanıtlayın ve sorularını cevaplayın</p>
        </div>
        <div class="flex gap-3">
          <span class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg font-medium">
            ⭐ {{ stats.avgRating.toFixed(1) }} Ortalama Puan
          </span>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <p class="text-2xl font-bold text-slate-900">{{ stats.totalReviews }}</p>
          <p class="text-xs text-slate-500">Toplam Yorum</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 shadow-sm border border-yellow-100">
          <p class="text-2xl font-bold text-yellow-700">{{ stats.pendingReviews }}</p>
          <p class="text-xs text-yellow-600">Bekleyen</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 shadow-sm border border-green-100">
          <p class="text-2xl font-bold text-green-700">{{ stats.answeredReviews }}</p>
          <p class="text-xs text-green-600">Yanıtlanan</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 shadow-sm border border-blue-100">
          <p class="text-2xl font-bold text-blue-700">{{ stats.totalQuestions }}</p>
          <p class="text-xs text-blue-600">Soru</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 shadow-sm border border-red-100">
          <p class="text-2xl font-bold text-red-700">{{ stats.reportedReviews }}</p>
          <p class="text-xs text-red-600">İhbar Edildi</p>
        </div>
      </div>

      <!-- Tabs -->
      <div class="mb-6">
        <div class="border-b border-slate-200">
          <div class="flex gap-4">
            <button 
              @click="activeTab = 'reviews'"
              :class="['px-4 py-3 font-medium border-b-2 transition', activeTab === 'reviews' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-500 hover:text-slate-700']"
            >
              💬 Yorumlar ({{ reviews.length }})
            </button>
            <button 
              @click="activeTab = 'questions'"
              :class="['px-4 py-3 font-medium border-b-2 transition', activeTab === 'questions' ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-500 hover:text-slate-700']"
            >
              ❓ Sorular ({{ questions.length }})
            </button>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Ara..."
              class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500"
            />
          </div>
          <select v-if="activeTab === 'reviews'" v-model="ratingFilter" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="">Tüm Puanlar</option>
            <option value="5">⭐ 5 Yıldız</option>
            <option value="4">⭐ 4 Yıldız</option>
            <option value="3">⭐ 3 Yıldız</option>
            <option value="2">⭐ 2 Yıldız</option>
            <option value="1">⭐ 1 Yıldız</option>
          </select>
          <select v-model="statusFilter" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="">Tüm Durumlar</option>
            <option value="pending">Bekleyen</option>
            <option value="answered">Yanıtlanan</option>
            <option value="reported">İhbar Edildi</option>
          </select>
          <select v-model="sortBy" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="newest">En Yeni</option>
            <option value="oldest">En Eski</option>
            <option value="highest">En Yüksek Puan</option>
            <option value="lowest">En Düşük Puan</option>
          </select>
        </div>
      </div>

      <!-- Reviews Tab -->
      <div v-if="activeTab === 'reviews'" class="space-y-4">
        <div 
          v-for="review in filteredReviews" 
          :key="review.id"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition"
        >
          <div class="p-6">
            <!-- Review Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center font-bold text-slate-600">
                  {{ review.customer.name.substring(0, 2).toUpperCase() }}
                </div>
                <div>
                  <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-bold text-slate-900">{{ review.customer.name }}</h3>
                    <span v-if="review.verified" class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                      ✓ Onaylı Alıcı
                    </span>
                  </div>
                  <div class="flex items-center gap-3 text-sm text-slate-500">
                    <span class="flex items-center gap-1">
                      {{ '⭐'.repeat(review.rating) }}
                      <span class="font-medium text-slate-900">{{ review.rating }}/5</span>
                    </span>
                    <span>•</span>
                    <span>{{ review.date }}</span>
                    <span>•</span>
                    <span>{{ review.product }}</span>
                  </div>
                </div>
              </div>
              <div class="flex gap-2">
                <span v-if="review.status === 'pending'" class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">
                  Bekliyor
                </span>
                <span v-if="review.status === 'answered'" class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                  Yanıtlandı
                </span>
                <span v-if="review.status === 'reported'" class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded-full font-medium">
                  İhbar
                </span>
              </div>
            </div>

            <!-- Review Content -->
            <div class="mb-4">
              <p class="text-slate-700 leading-relaxed">{{ review.comment }}</p>
            </div>

            <!-- Review Images -->
            <div v-if="review.images && review.images.length > 0" class="flex gap-2 mb-4">
              <img 
                v-for="(img, index) in review.images" 
                :key="index"
                :src="img" 
                class="w-20 h-20 rounded-lg object-cover border border-slate-200 cursor-pointer hover:opacity-75"
              />
            </div>

            <!-- Seller Reply -->
            <div v-if="review.sellerReply" class="mt-4 p-4 bg-orange-50 border-l-4 border-orange-500 rounded-r-lg">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-sm font-medium text-orange-900">🏪 Satıcı Yanıtı</span>
                <span class="text-xs text-orange-600">{{ review.sellerReply.date }}</span>
              </div>
              <p class="text-sm text-slate-700">{{ review.sellerReply.message }}</p>
            </div>

            <!-- Reply Form -->
            <div v-if="!review.sellerReply && replyingTo === review.id" class="mt-4">
              <textarea 
                v-model="replyMessage"
                rows="3"
                class="w-full px-4 py-2 border border-slate-200 rounded-lg resize-none focus:ring-2 focus:ring-orange-500"
                placeholder="Yanıtınızı yazın..."
              ></textarea>
              <div class="flex gap-2 mt-2">
                <button @click="submitReply(review)" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm hover:bg-orange-700">
                  Gönder
                </button>
                <button @click="replyingTo = null" class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">
                  İptal
                </button>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 mt-4 pt-4 border-t border-slate-100">
              <button v-if="!review.sellerReply" @click="replyingTo = review.id" class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-sm hover:bg-orange-200">
                💬 Yanıtla
              </button>
              <button @click="markAsHelpful(review)" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200">
                👍 Faydalı İşaretle
              </button>
              <button @click="reportReview(review)" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200">
                🚨 İhbar Et
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Questions Tab -->
      <div v-if="activeTab === 'questions'" class="space-y-4">
        <div 
          v-for="question in filteredQuestions" 
          :key="question.id"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition"
        >
          <div class="p-6">
            <!-- Question Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl">
                  ❓
                </div>
                <div>
                  <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-bold text-slate-900">{{ question.customer.name }}</h3>
                  </div>
                  <div class="flex items-center gap-3 text-sm text-slate-500">
                    <span>{{ question.date }}</span>
                    <span>•</span>
                    <span>{{ question.product }}</span>
                  </div>
                </div>
              </div>
              <span v-if="question.answered" class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                ✓ Yanıtlandı
              </span>
              <span v-else class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">
                Bekliyor
              </span>
            </div>

            <!-- Question Content -->
            <div class="mb-4 p-4 bg-slate-50 rounded-lg">
              <p class="text-slate-700 font-medium">{{ question.question }}</p>
            </div>

            <!-- Seller Answer -->
            <div v-if="question.sellerAnswer" class="p-4 bg-orange-50 border-l-4 border-orange-500 rounded-r-lg">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-sm font-medium text-orange-900">🏪 Satıcı Yanıtı</span>
                <span class="text-xs text-orange-600">{{ question.sellerAnswer.date }}</span>
              </div>
              <p class="text-sm text-slate-700">{{ question.sellerAnswer.message }}</p>
            </div>

            <!-- Answer Form -->
            <div v-if="!question.sellerAnswer && answeringTo === question.id" class="mt-4">
              <textarea 
                v-model="answerMessage"
                rows="3"
                class="w-full px-4 py-2 border border-slate-200 rounded-lg resize-none focus:ring-2 focus:ring-orange-500"
                placeholder="Cevabınızı yazın..."
              ></textarea>
              <div class="flex gap-2 mt-2">
                <button @click="submitAnswer(question)" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm hover:bg-orange-700">
                  Cevapla
                </button>
                <button @click="answeringTo = null" class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">
                  İptal
                </button>
              </div>
            </div>

            <!-- Actions -->
            <div v-if="!question.sellerAnswer" class="flex gap-3 mt-4 pt-4 border-t border-slate-100">
              <button @click="answeringTo = question.id" class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-sm hover:bg-orange-200">
                💬 Cevapla
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="(activeTab === 'reviews' && filteredReviews.length === 0) || (activeTab === 'questions' && filteredQuestions.length === 0)" class="bg-white rounded-2xl p-12 text-center shadow-sm">
        <p class="text-4xl mb-4">{{ activeTab === 'reviews' ? '💬' : '❓' }}</p>
        <p class="text-slate-500">{{ activeTab === 'reviews' ? 'Henüz yorum yok' : 'Henüz soru yok' }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const activeTab = ref('reviews')
const searchQuery = ref('')
const ratingFilter = ref('')
const statusFilter = ref('')
const sortBy = ref('newest')
const replyingTo = ref<number | null>(null)
const answeringTo = ref<number | null>(null)
const replyMessage = ref('')
const answerMessage = ref('')

const stats = ref({
  totalReviews: 127,
  pendingReviews: 8,
  answeredReviews: 119,
  totalQuestions: 34,
  reportedReviews: 2,
  avgRating: 4.6
})

const reviews = ref([
  {
    id: 1,
    customer: { name: 'Ahmet Yılmaz', verified: true },
    rating: 5,
    comment: 'Ürün harika, tam beklediğim gibi. Kargo çok hızlı geldi. Teşekkürler!',
    date: '2025-12-05',
    product: 'iPhone 14 Pro 256GB',
    images: ['https://via.placeholder.com/100'],
    status: 'pending',
    sellerReply: null
  },
  {
    id: 2,
    customer: { name: 'Ayşe Demir', verified: true },
    rating: 4,
    comment: 'Ürün kaliteli ama fiyatı biraz yüksek. Yine de memnunum.',
    date: '2025-12-04',
    product: 'Samsung Galaxy S24',
    images: [],
    status: 'answered',
    sellerReply: {
      date: '2025-12-04',
      message: 'Yorumunuz için teşekkürler! Fiyatlandırmamız piyasa koşullarına göre rekabetçi seviyelerdedir. İyi günler dileriz.'
    }
  },
  {
    id: 3,
    customer: { name: 'Mehmet Kaya', verified: false },
    rating: 2,
    comment: 'Ürün açıklamasında yazanlarla uyuşmuyor. Hayal kırıklığına uğradım.',
    date: '2025-12-03',
    product: 'Nike Air Max 2024',
    images: [],
    status: 'reported',
    sellerReply: null
  }
])

const questions = ref([
  {
    id: 1,
    customer: { name: 'Can Öztürk' },
    question: 'Bu ürünün garantisi var mı? Kaç yıl?',
    date: '2025-12-06',
    product: 'iPhone 14 Pro 256GB',
    answered: false,
    sellerAnswer: null
  },
  {
    id: 2,
    customer: { name: 'Zeynep Aydın' },
    question: 'Ürün orijinal mi? Apple Türkiye garantisi var mı?',
    date: '2025-12-05',
    product: 'iPhone 14 Pro 256GB',
    answered: true,
    sellerAnswer: {
      date: '2025-12-05',
      message: 'Evet, ürünlerimiz %100 orijinal ve Apple Türkiye garantilidir. Fatura ve garanti belgesi ile gönderilir.'
    }
  }
])

// Computed
const filteredReviews = computed(() => {
  return reviews.value.filter(r => {
    const matchSearch = !searchQuery.value || 
      r.comment.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      r.customer.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchRating = !ratingFilter.value || r.rating === parseInt(ratingFilter.value)
    const matchStatus = !statusFilter.value || r.status === statusFilter.value
    return matchSearch && matchRating && matchStatus
  })
})

const filteredQuestions = computed(() => {
  return questions.value.filter(q => {
    const matchSearch = !searchQuery.value || 
      q.question.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      q.customer.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchStatus = !statusFilter.value || 
      (statusFilter.value === 'answered' && q.answered) ||
      (statusFilter.value === 'pending' && !q.answered)
    return matchSearch && matchStatus
  })
})

// Methods
const submitReply = (review: any) => {
  if (!replyMessage.value.trim()) {
    toast.error('Lütfen bir yanıt yazın')
    return
  }
  
  review.sellerReply = {
    date: new Date().toISOString().split('T')[0],
    message: replyMessage.value
  }
  review.status = 'answered'
  
  toast.success('Yanıt gönderildi')
  replyingTo.value = null
  replyMessage.value = ''
}

const submitAnswer = (question: any) => {
  if (!answerMessage.value.trim()) {
    toast.error('Lütfen bir cevap yazın')
    return
  }
  
  question.sellerAnswer = {
    date: new Date().toISOString().split('T')[0],
    message: answerMessage.value
  }
  question.answered = true
  
  toast.success('Cevap gönderildi')
  answeringTo.value = null
  answerMessage.value = ''
}

const markAsHelpful = (review: any) => {
  toast.success('Faydalı olarak işaretlendi')
}

const reportReview = (review: any) => {
  if (confirm('Bu yorumu ihbar etmek istiyor musunuz?')) {
    review.status = 'reported'
    toast.success('Yorum ihbar edildi')
  }
}
</script>
