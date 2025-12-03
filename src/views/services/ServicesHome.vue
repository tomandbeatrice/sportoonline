<template>
  <div class="min-h-screen bg-gradient-to-b from-purple-50 to-white">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 text-white py-12">
      <div class="container mx-auto px-4">
        <div class="flex items-center gap-4 mb-6">
          <span class="text-5xl">🔧</span>
          <div>
            <h1 class="text-3xl font-bold">Hizmetler</h1>
            <p class="text-purple-100">Profesyonel hizmetler kapınızda</p>
          </div>
        </div>
        
        <!-- Search -->
        <div class="max-w-2xl">
          <div class="flex bg-white rounded-xl overflow-hidden shadow-lg">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Ne tür bir hizmete ihtiyacınız var?"
              class="flex-1 px-4 py-4 text-gray-800 focus:outline-none"
            />
            <button class="bg-purple-600 hover:bg-purple-700 px-6 py-4 font-medium transition-colors">
              🔍 Ara
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8">
      <!-- Service Categories -->
      <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Hizmet Kategorileri</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div
            v-for="category in serviceCategories"
            :key="category.id"
            @click="selectCategory(category.id)"
            :class="[
              'flex flex-col items-center p-6 rounded-2xl cursor-pointer transition-all',
              selectedCategory === category.id
                ? 'bg-purple-100 border-2 border-purple-500 shadow-md'
                : 'bg-white border-2 border-gray-100 hover:border-purple-200 hover:shadow-sm'
            ]"
          >
            <span class="text-4xl mb-3">{{ category.icon }}</span>
            <span class="font-medium text-gray-800 text-center">{{ category.name }}</span>
            <span class="text-xs text-gray-500 mt-1">{{ category.providerCount }} uzman</span>
          </div>
        </div>
      </section>

      <!-- Featured Services -->
      <section class="mb-10">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-gray-800">Popüler Hizmetler</h2>
          <router-link to="/services/all" class="text-purple-600 hover:text-purple-700 font-medium">
            Tümünü Gör →
          </router-link>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="service in featuredServices"
            :key="service.id"
            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow"
          >
            <div class="relative">
              <img :src="service.image" :alt="service.name" class="w-full h-40 object-cover" />
              <div class="absolute top-3 left-3 bg-white px-3 py-1 rounded-lg text-sm">
                <span class="font-bold text-purple-600">{{ service.category }}</span>
              </div>
            </div>
            <div class="p-4">
              <h3 class="font-bold text-gray-800 mb-2">{{ service.name }}</h3>
              <p class="text-sm text-gray-500 mb-3">{{ service.description }}</p>
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-purple-600 font-bold text-lg">{{ service.price }}₺</span>
                  <span class="text-gray-400 text-sm">/saat</span>
                </div>
                <div class="flex items-center gap-1">
                  <span class="text-yellow-500">⭐</span>
                  <span class="font-medium">{{ service.rating }}</span>
                  <span class="text-gray-400 text-sm">({{ service.reviewCount }})</span>
                </div>
              </div>
              <button class="w-full mt-4 bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg font-medium transition-colors">
                Hizmet Al
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Top Providers -->
      <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-800 mb-4">En İyi Hizmet Sağlayıcılar</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div
            v-for="provider in topProviders"
            :key="provider.id"
            class="bg-white rounded-xl p-4 text-center hover:shadow-md transition-shadow cursor-pointer"
          >
            <img :src="provider.avatar" :alt="provider.name" class="w-16 h-16 mx-auto rounded-full object-cover mb-3" />
            <h4 class="font-bold text-gray-800">{{ provider.name }}</h4>
            <p class="text-sm text-purple-600">{{ provider.specialty }}</p>
            <div class="flex items-center justify-center gap-1 mt-2">
              <span class="text-yellow-500">⭐</span>
              <span class="font-medium">{{ provider.rating }}</span>
              <span class="text-gray-400 text-sm">({{ provider.jobCount }} iş)</span>
            </div>
            <div class="flex flex-wrap justify-center gap-1 mt-3">
              <span
                v-for="badge in provider.badges"
                :key="badge"
                class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded"
              >
                {{ badge }}
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- How It Works -->
      <section class="bg-gray-50 rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Nasıl Çalışır?</h2>
        <div class="grid md:grid-cols-4 gap-6">
          <div class="text-center">
            <div class="bg-purple-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
              1
            </div>
            <h4 class="font-bold text-gray-800 mb-2">Hizmet Seçin</h4>
            <p class="text-sm text-gray-500">İhtiyacınız olan hizmeti kategorilerden bulun</p>
          </div>
          <div class="text-center">
            <div class="bg-purple-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
              2
            </div>
            <h4 class="font-bold text-gray-800 mb-2">Uzman Seçin</h4>
            <p class="text-sm text-gray-500">Değerlendirmelere göre en iyi uzmanı seçin</p>
          </div>
          <div class="text-center">
            <div class="bg-purple-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
              3
            </div>
            <h4 class="font-bold text-gray-800 mb-2">Randevu Alın</h4>
            <p class="text-sm text-gray-500">Size uygun tarih ve saati belirleyin</p>
          </div>
          <div class="text-center">
            <div class="bg-purple-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
              4
            </div>
            <h4 class="font-bold text-gray-800 mb-2">Hizmet Alın</h4>
            <p class="text-sm text-gray-500">Uzman kapınıza gelsin, işinizi halledelim</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const searchQuery = ref('')
const selectedCategory = ref<string | null>(null)

const serviceCategories = [
  { id: 'cleaning', name: 'Temizlik', icon: '🧹', providerCount: 450 },
  { id: 'repair', name: 'Tamir', icon: '🔧', providerCount: 320 },
  { id: 'plumbing', name: 'Tesisatçı', icon: '🚿', providerCount: 180 },
  { id: 'electric', name: 'Elektrikçi', icon: '⚡', providerCount: 150 },
  { id: 'moving', name: 'Nakliyat', icon: '📦', providerCount: 200 },
  { id: 'beauty', name: 'Güzellik', icon: '💅', providerCount: 380 },
]

const featuredServices = ref([
  {
    id: 1,
    name: 'Ev Temizliği',
    category: 'Temizlik',
    description: 'Detaylı ev temizliği, cam silme dahil',
    image: '/images/services/cleaning.jpg',
    price: 350,
    rating: 4.8,
    reviewCount: 520
  },
  {
    id: 2,
    name: 'Klima Bakımı',
    category: 'Tamir',
    description: 'Klima temizlik ve bakım hizmeti',
    image: '/images/services/ac-repair.jpg',
    price: 250,
    rating: 4.6,
    reviewCount: 180
  },
  {
    id: 3,
    name: 'Boya Badana',
    category: 'Tadilat',
    description: 'Profesyonel iç cephe boyama',
    image: '/images/services/painting.jpg',
    price: 150,
    rating: 4.7,
    reviewCount: 340
  },
])

const topProviders = [
  {
    id: 1,
    name: 'Ahmet Yılmaz',
    specialty: 'Elektrik Ustası',
    avatar: '/images/providers/provider1.jpg',
    rating: 4.9,
    jobCount: 450,
    badges: ['Pro', 'Hızlı']
  },
  {
    id: 2,
    name: 'Ayşe Demir',
    specialty: 'Temizlik Uzmanı',
    avatar: '/images/providers/provider2.jpg',
    rating: 4.8,
    jobCount: 380,
    badges: ['Top Rated']
  },
  {
    id: 3,
    name: 'Mehmet Kaya',
    specialty: 'Tesisatçı',
    avatar: '/images/providers/provider3.jpg',
    rating: 4.7,
    jobCount: 290,
    badges: ['7/24']
  },
  {
    id: 4,
    name: 'Fatma Çelik',
    specialty: 'Güzellik Uzmanı',
    avatar: '/images/providers/provider4.jpg',
    rating: 4.9,
    jobCount: 520,
    badges: ['Pro', 'Popüler']
  },
]

const selectCategory = (categoryId: string) => {
  selectedCategory.value = categoryId
}
</script>
