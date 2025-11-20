<template>
  <div class="product-reviews space-y-6">
    <!-- Header & Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Overall Rating -->
        <div class="text-center">
          <div class="text-6xl font-bold text-yellow-600 mb-2">{{ averageRating.toFixed(1) }}</div>
          <div class="flex items-center justify-center gap-1 text-3xl mb-2">
            <span v-for="star in 5" :key="star">
              <BadgeIcon :name="star <= Math.round(averageRating) ? 'star' : undefined" :cls="star <= Math.round(averageRating) ? 'w-4 h-4 text-yellow-500 inline-block' : 'w-4 h-4 text-slate-200 inline-block'" />
            </span>
          </div>
          <div class="text-gray-600">{{ totalReviews }} değerlendirme</div>
        </div>

        <!-- Rating Distribution -->
        <div class="space-y-2">
          <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-3">
            <div class="flex items-center gap-1 w-20">
              <span>{{ star }}</span>
              <BadgeIcon name="star" cls="w-4 h-4 text-yellow-500 inline-block" />
            </div>
            <div class="flex-1 h-3 bg-gray-200 rounded-full overflow-hidden">
              <div
                class="h-full bg-yellow-500 transition-all"
                :style="{ width: `${getRatingPercentage(star)}%` }"
              ></div>
            </div>
            <div class="w-16 text-sm text-gray-600 text-right">
              {{ getRatingCount(star) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Write Review -->
    <div class="bg-white rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-4">Ürünü Değerlendirin</h3>
      <form @submit.prevent="submitReview">
        <!-- Star Rating -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Puanınız</label>
          <div class="flex items-center gap-2">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              @click="newReview.rating = star"
              class="transition-all hover:scale-110"
              :aria-pressed="star <= newReview.rating"
              :title="`${star} yıldız`"
            >
              <IconStar :cls="star <= newReview.rating ? 'w-8 h-8 text-yellow-400' : 'w-8 h-8 text-slate-200'" :filled="star <= newReview.rating" />
            </button>
            <span class="ml-4 text-gray-600">{{ getRatingLabel(newReview.rating) }}</span>
          </div>
        </div>

        <!-- Review Title -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Başlık</label>
          <input
            v-model="newReview.title"
            type="text"
            placeholder="Yorumunuzu özetleyin"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>

        <!-- Review Comment -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Yorumunuz</label>
          <textarea
            v-model="newReview.comment"
            rows="4"
            placeholder="Ürün hakkında detaylı görüşlerinizi paylaşın..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            required
          ></textarea>
        </div>

        <!-- Photo Upload -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Fotoğraf Ekle (İsteğe Bağlı)</label>
          <div class="flex flex-wrap gap-3">
            <div
              v-for="(photo, index) in newReview.photos"
              :key="index"
              class="relative w-24 h-24 border-2 border-gray-300 rounded-lg overflow-hidden group"
            >
              <img :src="photo.preview" class="w-full h-full object-cover" />
              <button
                type="button"
                @click="removePhoto(index)"
                class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
              >
                ✕
              </button>
            </div>
            <label
              v-if="newReview.photos.length < 5"
              class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:border-blue-500 transition-colors"
            >
              <span class="text-3xl text-gray-400">📷</span>
              <span class="text-xs text-gray-500 mt-1">Ekle</span>
              <input
                type="file"
                accept="image/*"
                multiple
                @change="handlePhotoUpload"
                class="hidden"
              />
            </label>
          </div>
          <p class="text-xs text-gray-500 mt-2">En fazla 5 fotoğraf ekleyebilirsiniz</p>
        </div>

        <!-- Pros & Cons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Artıları (İsteğe Bağlı)</label>
            <textarea
              v-model="newReview.pros"
              rows="3"
              placeholder="Ürünün beğendiğiniz özellikleri..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Eksileri (İsteğe Bağlı)</label>
            <textarea
              v-model="newReview.cons"
              rows="3"
              placeholder="Ürünün eksik bulduğunuz yönleri..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
            ></textarea>
          </div>
        </div>

        <button
          type="submit"
          :disabled="submitting || !newReview.rating"
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          {{ submitting ? 'Gönderiliyor...' : 'Yorumu Gönder' }}
        </button>
      </form>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="flex flex-wrap items-center gap-3">
        <span class="font-semibold text-gray-700">Filtrele:</span>
        <select
          v-model="filters.rating"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tüm Puanlar</option>
          <option value="5">5 Yıldız</option>
          <option value="4">4 Yıldız</option>
          <option value="3">3 Yıldız</option>
          <option value="2">2 Yıldız</option>
          <option value="1">1 Yıldız</option>
        </select>
        <select
          v-model="filters.sort"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="recent">En Yeni</option>
          <option value="helpful">En Yararlı</option>
          <option value="highest">En Yüksek Puan</option>
          <option value="lowest">En Düşük Puan</option>
        </select>
        <label class="flex items-center gap-2 cursor-pointer">
          <input
            type="checkbox"
            v-model="filters.withPhotos"
            class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
          />
          <span class="text-sm text-gray-700">Sadece Fotoğraflı Yorumlar</span>
        </label>
      </div>
    </div>

    <!-- Reviews List -->
    <div class="space-y-4">
      <div
        v-for="review in filteredReviews"
        :key="review.id"
        class="bg-white rounded-xl shadow-sm p-6"
      >
        <!-- Review Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-xl">
              {{ review.user_name[0].toUpperCase() }}
            </div>
            <div>
              <div class="font-semibold text-gray-900">{{ review.user_name }}</div>
              <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>{{ formatDate(review.created_at) }}</span>
                <span v-if="review.verified_purchase" class="text-green-600 flex items-center gap-1">
                  ✓ Doğrulanmış Alıcı
                </span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-1 text-xl">
            <IconStar v-for="star in 5" :key="star" :cls="star <= review.rating ? 'w-5 h-5 text-yellow-400' : 'w-5 h-5 text-slate-200'" :filled="star <= review.rating" />
          </div>
        </div>

        <!-- Review Title -->
        <h4 class="font-bold text-gray-900 text-lg mb-2">{{ review.title }}</h4>

        <!-- Review Comment -->
        <p class="text-gray-700 mb-3">{{ review.comment }}</p>

        <!-- Pros & Cons -->
        <div v-if="review.pros || review.cons" class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
          <div v-if="review.pros" class="bg-green-50 border border-green-200 rounded-lg p-3">
            <div class="font-semibold text-green-800 mb-1 flex items-center gap-1">
              ✓ Artıları
            </div>
            <p class="text-sm text-green-700">{{ review.pros }}</p>
          </div>
          <div v-if="review.cons" class="bg-red-50 border border-red-200 rounded-lg p-3">
            <div class="font-semibold text-red-800 mb-1 flex items-center gap-1">
              ✗ Eksileri
            </div>
            <p class="text-sm text-red-700">{{ review.cons }}</p>
          </div>
        </div>

        <!-- Review Photos -->
        <div v-if="review.photos && review.photos.length > 0" class="flex gap-2 mb-4">
          <img
            v-for="(photo, index) in review.photos"
            :key="index"
            :src="photo.url"
            @click="openPhotoGallery(review.photos, index)"
            class="w-20 h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
          />
        </div>

        <!-- Seller Response -->
        <div v-if="review.seller_response" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-3">
          <div class="font-semibold text-blue-900 mb-1 flex items-center gap-2">
            🏪 Satıcı Yanıtı
            <span class="text-xs text-blue-600 font-normal">{{ formatDate(review.seller_response.created_at) }}</span>
          </div>
          <p class="text-sm text-blue-800">{{ review.seller_response.message }}</p>
        </div>

        <!-- Review Actions -->
        <div class="flex items-center gap-4 pt-3 border-t border-gray-200">
          <button
            @click="toggleHelpful(review)"
            class="flex items-center gap-2 text-sm font-medium transition-colors"
            :class="review.user_found_helpful ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600'"
          >
            <span class="text-lg">{{ review.user_found_helpful ? '👍' : '👍' }}</span>
            <span>Yardımcı Oldu ({{ review.helpful_count || 0 }})</span>
          </button>
          <button
            @click="toggleNotHelpful(review)"
            class="flex items-center gap-2 text-sm font-medium transition-colors"
            :class="review.user_found_not_helpful ? 'text-red-600' : 'text-gray-600 hover:text-red-600'"
          >
            <span class="text-lg">👎</span>
            <span>Yardımcı Olmadı ({{ review.not_helpful_count || 0 }})</span>
          </button>
          <button class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
            <span class="text-lg">🚩</span>
            <span>Bildir</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Load More -->
    <div v-if="hasMoreReviews" class="text-center">
      <button
        @click="loadMoreReviews"
        class="px-6 py-3 border-2 border-gray-300 hover:border-blue-500 hover:text-blue-600 rounded-lg font-semibold transition-colors"
      >
        Daha Fazla Yorum Yükle
      </button>
    </div>

    <!-- Empty State -->
    <div v-if="filteredReviews.length === 0" class="bg-white rounded-xl shadow-sm p-12 text-center">
      <div class="text-6xl mb-4">💬</div>
      <div class="text-xl font-semibold text-gray-900 mb-2">Henüz Yorum Yok</div>
      <div class="text-gray-500 mb-4">Bu ürün için ilk yorumu siz yapın!</div>
    </div>

    <!-- Photo Gallery Modal -->
    <div v-if="photoGallery.show" class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4">
      <button
        @click="photoGallery.show = false"
        class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300"
      >
        ✕
      </button>
      <button
        v-if="photoGallery.currentIndex > 0"
        @click="photoGallery.currentIndex--"
        class="absolute left-4 text-white text-4xl hover:text-gray-300"
      >
        ‹
      </button>
      <img
        :src="photoGallery.photos[photoGallery.currentIndex]?.url"
        class="max-w-full max-h-full object-contain"
      />
      <button
        v-if="photoGallery.currentIndex < photoGallery.photos.length - 1"
        @click="photoGallery.currentIndex++"
        class="absolute right-4 text-white text-4xl hover:text-gray-300"
      >
        ›
      </button>
      <div class="absolute bottom-4 text-white">
        {{ photoGallery.currentIndex + 1 }} / {{ photoGallery.photos.length }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
import IconStar from '@/components/icons/IconStar.vue'
import axios from 'axios'

const props = defineProps({
  productId: {
    type: Number,
    required: true
  }
})

const reviews = ref([])
const newReview = ref({
  rating: 0,
  title: '',
  comment: '',
  pros: '',
  cons: '',
  photos: []
})
const submitting = ref(false)
const filters = ref({
  rating: '',
  sort: 'recent',
  withPhotos: false
})
const currentPage = ref(1)
const hasMoreReviews = ref(false)
const photoGallery = ref({
  show: false,
  photos: [],
  currentIndex: 0
})

const totalReviews = computed(() => reviews.value.length)
const averageRating = computed(() => {
  if (reviews.value.length === 0) return 0
  const sum = reviews.value.reduce((acc, r) => acc + r.rating, 0)
  return sum / reviews.value.length
})

const filteredReviews = computed(() => {
  let result = [...reviews.value]

  // Filter by rating
  if (filters.value.rating) {
    result = result.filter(r => r.rating === parseInt(filters.value.rating))
  }

  // Filter by photos
  if (filters.value.withPhotos) {
    result = result.filter(r => r.photos && r.photos.length > 0)
  }

  // Sort
  switch (filters.value.sort) {
    case 'recent':
      result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      break
    case 'helpful':
      result.sort((a, b) => (b.helpful_count || 0) - (a.helpful_count || 0))
      break
    case 'highest':
      result.sort((a, b) => b.rating - a.rating)
      break
    case 'lowest':
      result.sort((a, b) => a.rating - b.rating)
      break
  }

  return result
})

const getRatingCount = (star) => {
  return reviews.value.filter(r => r.rating === star).length
}

const getRatingPercentage = (star) => {
  if (totalReviews.value === 0) return 0
  return (getRatingCount(star) / totalReviews.value) * 100
}

const getRatingLabel = (rating) => {
  const labels = ['', 'Çok Kötü', 'Kötü', 'Orta', 'İyi', 'Mükemmel']
  return labels[rating] || ''
}

const loadReviews = async () => {
  try {
    const { data } = await axios.get(`/api/products/${props.productId}/reviews`, {
      params: { page: currentPage.value }
    })
    reviews.value = data.reviews || data.data || data
    hasMoreReviews.value = data.has_more || false
  } catch (error) {
    console.error('Yorumlar yüklenemedi:', error)
  }
}

const loadMoreReviews = async () => {
  currentPage.value++
  const { data } = await axios.get(`/api/products/${props.productId}/reviews`, {
    params: { page: currentPage.value }
  })
  reviews.value.push(...(data.reviews || data.data || data))
  hasMoreReviews.value = data.has_more || false
}

const submitReview = async () => {
  if (!newReview.value.rating) {
    alert('Lütfen bir puan seçin')
    return
  }

  submitting.value = true
  try {
    const formData = new FormData()
    formData.append('product_id', props.productId)
    formData.append('rating', newReview.value.rating)
    formData.append('title', newReview.value.title)
    formData.append('comment', newReview.value.comment)
    if (newReview.value.pros) formData.append('pros', newReview.value.pros)
    if (newReview.value.cons) formData.append('cons', newReview.value.cons)
    
    newReview.value.photos.forEach((photo, index) => {
      if (photo.file) {
        formData.append(`photos[${index}]`, photo.file)
      }
    })

    const { data } = await axios.post('/api/reviews', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    reviews.value.unshift(data.review || data)
    
    // Reset form
    newReview.value = {
      rating: 0,
      title: '',
      comment: '',
      pros: '',
      cons: '',
      photos: []
    }

    alert('Yorumunuz başarıyla gönderildi!')
  } catch (error) {
    console.error('Yorum gönderilemedi:', error)
    alert('Bir hata oluştu. Lütfen tekrar deneyin.')
  } finally {
    submitting.value = false
  }
}

const handlePhotoUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    if (newReview.value.photos.length >= 5) return
    
    const reader = new FileReader()
    reader.onload = (e) => {
      newReview.value.photos.push({
        file,
        preview: e.target.result
      })
    }
    reader.readAsDataURL(file)
  })
}

const removePhoto = (index) => {
  newReview.value.photos.splice(index, 1)
}

const toggleHelpful = async (review) => {
  try {
    await axios.post(`/api/reviews/${review.id}/helpful`)
    review.user_found_helpful = !review.user_found_helpful
    if (review.user_found_helpful) {
      review.helpful_count = (review.helpful_count || 0) + 1
      if (review.user_found_not_helpful) {
        review.not_helpful_count = Math.max(0, (review.not_helpful_count || 0) - 1)
        review.user_found_not_helpful = false
      }
    } else {
      review.helpful_count = Math.max(0, (review.helpful_count || 0) - 1)
    }
  } catch (error) {
    console.error('İşlem başarısız:', error)
  }
}

const toggleNotHelpful = async (review) => {
  try {
    await axios.post(`/api/reviews/${review.id}/not-helpful`)
    review.user_found_not_helpful = !review.user_found_not_helpful
    if (review.user_found_not_helpful) {
      review.not_helpful_count = (review.not_helpful_count || 0) + 1
      if (review.user_found_helpful) {
        review.helpful_count = Math.max(0, (review.helpful_count || 0) - 1)
        review.user_found_helpful = false
      }
    } else {
      review.not_helpful_count = Math.max(0, (review.not_helpful_count || 0) - 1)
    }
  } catch (error) {
    console.error('İşlem başarısız:', error)
  }
}

const openPhotoGallery = (photos, index) => {
  photoGallery.value = {
    show: true,
    photos,
    currentIndex: index
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(() => {
  loadReviews()
})
</script>
