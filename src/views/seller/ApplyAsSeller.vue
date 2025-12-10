<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 py-12">
    <div class="max-w-4xl mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-10">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4">
          <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
          Yeni Başvurular Açık
        </div>
        <h1 class="text-4xl font-black text-slate-900 mb-3">🏪 Satıcı / Hizmet Sağlayıcı Başvurusu</h1>
        <p class="text-lg text-slate-600 max-w-xl mx-auto">
          SportoOnline ailesine katılın ve milyonlarca müşteriye ulaşın!
        </p>
      </div>

      <!-- Success State -->
      <div v-if="success" class="bg-white rounded-3xl shadow-xl p-10 text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <span class="text-4xl">✅</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 mb-2">Başvurunuz Alındı!</h2>
        <p class="text-slate-600 mb-6">Başvurunuz incelemeye alındı. En kısa sürede size geri dönüş yapacağız.</p>
        <div class="bg-slate-50 rounded-xl p-4 text-left max-w-md mx-auto">
          <p class="text-sm text-slate-500 mb-2">Başvuru Detayları:</p>
          <p class="font-medium text-slate-900">{{ form.store_name }}</p>
          <p class="text-sm text-slate-600">{{ selectedServiceType?.name }}</p>
        </div>
        <router-link to="/" class="inline-block mt-8 px-8 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">
          Ana Sayfaya Dön
        </router-link>
      </div>

      <!-- Application Form -->
      <form v-else @submit.prevent="submitApplication" class="space-y-8">
        <!-- Step 1: Service Type Selection -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-bold">1</div>
            <div>
              <h2 class="text-lg font-bold text-slate-900">Hizmet Türü Seçin</h2>
              <p class="text-sm text-slate-500">Hangi alanda hizmet vermek istiyorsunuz?</p>
            </div>
          </div>

          <div class="grid md:grid-cols-3 gap-4">
            <div 
              v-for="service in serviceTypes" 
              :key="service.id"
              @click="form.service_type = service.id"
              :class="[
                'relative p-5 rounded-xl border-2 cursor-pointer transition-all',
                form.service_type === service.id 
                  ? 'border-blue-500 bg-blue-50 shadow-lg' 
                  : 'border-slate-200 hover:border-slate-300 hover:shadow-md'
              ]"
            >
              <div class="flex items-start justify-between mb-3">
                <span class="text-3xl">{{ service.icon }}</span>
                <div v-if="form.service_type === service.id" class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                  <span class="text-white text-sm">✓</span>
                </div>
              </div>
              <h3 class="font-bold text-slate-900 mb-1">{{ service.name }}</h3>
              <p class="text-sm text-slate-500">{{ service.description }}</p>
              <div class="mt-3 flex flex-wrap gap-1">
                <span v-for="tag in service.tags" :key="tag" class="text-xs px-2 py-0.5 bg-slate-100 text-slate-600 rounded">
                  {{ tag }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 2: Business Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-bold">2</div>
            <div>
              <h2 class="text-lg font-bold text-slate-900">İşletme Bilgileri</h2>
              <p class="text-sm text-slate-500">İşletmeniz hakkında bilgi verin</p>
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Mağaza / İşletme Adı *</label>
              <input 
                v-model="form.store_name" 
                type="text" 
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Örn: Spor Dünyası"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">İşletme Türü *</label>
              <select 
                v-model="form.business_type" 
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              >
                <option value="">Seçiniz</option>
                <option value="individual">Bireysel (Şahıs)</option>
                <option value="company">Şirket (Limited/Anonim)</option>
                <option value="sole_proprietor">Esnaf / Serbest Meslek</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-2">İş Açıklaması *</label>
              <textarea 
                v-model="form.business_description" 
                required
                rows="4"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                placeholder="Ne tür ürünler veya hizmetler sunacaksınız? Deneyiminiz nedir?"
              ></textarea>
            </div>

            <!-- Category Selection (for Products) -->
            <div v-if="form.service_type === 'products'" class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-2">Ürün Kategorileri *</label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="cat in productCategories"
                  :key="cat.id"
                  type="button"
                  @click="toggleCategory(cat.id)"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium border transition',
                    form.categories.includes(cat.id)
                      ? 'border-blue-500 bg-blue-50 text-blue-700'
                      : 'border-slate-200 text-slate-600 hover:border-slate-300'
                  ]"
                >
                  {{ cat.icon }} {{ cat.name }}
                </button>
              </div>
            </div>

            <!-- Service Specific Fields -->
            <div v-if="form.service_type === 'food'" class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-2">Mutfak Türleri *</label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="cuisine in cuisineTypes"
                  :key="cuisine"
                  type="button"
                  @click="toggleCuisine(cuisine)"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium border transition',
                    form.cuisines.includes(cuisine)
                      ? 'border-orange-500 bg-orange-50 text-orange-700'
                      : 'border-slate-200 text-slate-600 hover:border-slate-300'
                  ]"
                >
                  {{ cuisine }}
                </button>
              </div>
            </div>

            <div v-if="form.service_type === 'transport'">
              <label class="block text-sm font-medium text-slate-700 mb-2">Araç Sayısı *</label>
              <input 
                v-model.number="form.vehicle_count" 
                type="number" 
                min="1"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Kaç aracınız var?"
              />
            </div>

            <div v-if="form.service_type === 'transport'">
              <label class="block text-sm font-medium text-slate-700 mb-2">Hizmet Bölgesi *</label>
              <select 
                v-model="form.service_region" 
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              >
                <option value="">Seçiniz</option>
                <option value="istanbul_europe">İstanbul Avrupa</option>
                <option value="istanbul_asia">İstanbul Anadolu</option>
                <option value="istanbul_all">İstanbul Geneli</option>
                <option value="ankara">Ankara</option>
                <option value="izmir">İzmir</option>
                <option value="national">Türkiye Geneli</option>
              </select>
            </div>

            <div v-if="form.service_type === 'hotel'">
              <label class="block text-sm font-medium text-slate-700 mb-2">Oda Sayısı *</label>
              <input 
                v-model.number="form.room_count" 
                type="number" 
                min="1"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Toplam oda sayısı"
              />
            </div>

            <div v-if="form.service_type === 'hotel'">
              <label class="block text-sm font-medium text-slate-700 mb-2">Yıldız Derecesi</label>
              <select 
                v-model="form.star_rating" 
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              >
                <option value="">Seçiniz</option>
                <option value="1">⭐ 1 Yıldız</option>
                <option value="2">⭐⭐ 2 Yıldız</option>
                <option value="3">⭐⭐⭐ 3 Yıldız</option>
                <option value="4">⭐⭐⭐⭐ 4 Yıldız</option>
                <option value="5">⭐⭐⭐⭐⭐ 5 Yıldız</option>
              </select>
            </div>

            <!-- Career/Job Posting Fields -->
            <div v-if="form.service_type === 'career'" class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-2">Sektör *</label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="sector in careerSectors"
                  :key="sector"
                  type="button"
                  @click="toggleSector(sector)"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium border transition',
                    form.sectors.includes(sector)
                      ? 'border-amber-500 bg-amber-50 text-amber-700'
                      : 'border-slate-200 text-slate-600 hover:border-slate-300'
                  ]"
                >
                  {{ sector }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 3: Contact & Location -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-bold">3</div>
            <div>
              <h2 class="text-lg font-bold text-slate-900">İletişim & Konum</h2>
              <p class="text-sm text-slate-500">Size nasıl ulaşabiliriz?</p>
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Yetkili Ad Soyad *</label>
              <input 
                v-model="form.contact_name" 
                type="text" 
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Adınız ve Soyadınız"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Telefon *</label>
              <input 
                v-model="form.phone" 
                type="tel" 
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="0555 123 45 67"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">E-posta *</label>
              <input 
                v-model="form.email" 
                type="email" 
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="ornek@firma.com"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Şehir *</label>
              <select 
                v-model="form.city" 
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              >
                <option value="">Seçiniz</option>
                <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-2">Adres *</label>
              <textarea 
                v-model="form.address" 
                required
                rows="2"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                placeholder="Mahalle, sokak, numara..."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Step 4: Legal & Financial -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-bold">4</div>
            <div>
              <h2 class="text-lg font-bold text-slate-900">Yasal & Finansal Bilgiler</h2>
              <p class="text-sm text-slate-500">Ödeme alabilmeniz için gerekli bilgiler</p>
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Vergi Dairesi</label>
              <input 
                v-model="form.tax_office" 
                type="text"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Örn: Kadıköy"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Vergi / TC Kimlik No</label>
              <input 
                v-model="form.tax_number" 
                type="text"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="10 veya 11 haneli"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-2">IBAN *</label>
              <input 
                v-model="form.bank_account" 
                type="text" 
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-mono"
                placeholder="TR00 0000 0000 0000 0000 0000 00"
              />
              <p class="text-xs text-slate-500 mt-1">Kazançlarınız bu hesaba aktarılacaktır.</p>
            </div>
          </div>
        </div>

        <!-- Terms & Submit -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
          <label class="flex items-start gap-3 cursor-pointer">
            <input 
              v-model="form.accept_terms" 
              type="checkbox" 
              required
              class="w-5 h-5 mt-0.5 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
            />
            <span class="text-sm text-slate-600">
              <a href="/terms" target="_blank" class="text-blue-600 hover:underline">Satıcı Sözleşmesi</a>'ni ve 
              <a href="/privacy" target="_blank" class="text-blue-600 hover:underline">Gizlilik Politikası</a>'nı okudum, kabul ediyorum. 
              Platformun %{{ commissionRate }} komisyon oranı uyguladığını biliyorum.
            </span>
          </label>

          <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            {{ error }}
          </div>

          <button 
            type="submit" 
            :disabled="loading || !form.service_type"
            class="mt-6 w-full py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition flex items-center justify-center gap-2"
          >
            <span v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span>{{ loading ? 'Gönderiliyor...' : '🚀 Başvuruyu Gönder' }}</span>
          </button>
        </div>
      </form>

      <!-- Info Cards -->
      <div class="mt-12 grid md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl p-6 border border-slate-200">
          <span class="text-3xl mb-3 block">⏱️</span>
          <h3 class="font-bold text-slate-900 mb-1">Hızlı Onay</h3>
          <p class="text-sm text-slate-500">Başvurular 24-48 saat içinde değerlendirilir.</p>
        </div>
        <div class="bg-white rounded-xl p-6 border border-slate-200">
          <span class="text-3xl mb-3 block">💰</span>
          <h3 class="font-bold text-slate-900 mb-1">Düşük Komisyon</h3>
          <p class="text-sm text-slate-500">Sadece %{{ commissionRate }} komisyon, gizli ücret yok.</p>
        </div>
        <div class="bg-white rounded-xl p-6 border border-slate-200">
          <span class="text-3xl mb-3 block">📈</span>
          <h3 class="font-bold text-slate-900 mb-1">Büyüme Desteği</h3>
          <p class="text-sm text-slate-500">Pazarlama ve analitik araçları ücretsiz.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const serviceTypes = [
  { 
    id: 'products', 
    name: 'Ürün Satışı', 
    icon: '🛍️',
    description: 'Fiziksel ürünler satmak istiyorum',
    tags: ['Spor Malzemesi', 'Giyim', 'Elektronik']
  },
  { 
    id: 'food', 
    name: 'Restoran / Yemek', 
    icon: '🍽️',
    description: 'Yemek siparişi almak istiyorum',
    tags: ['Restoran', 'Kafe', 'Fast Food']
  },
  { 
    id: 'hotel', 
    name: 'Konaklama', 
    icon: '🏨',
    description: 'Otel/Apart/Villa kiralamak istiyorum',
    tags: ['Otel', 'Pansiyon', 'Tatil Köyü']
  },
  { 
    id: 'transport', 
    name: 'Ulaşım Hizmeti', 
    icon: '🚗',
    description: 'Araç/Transfer hizmeti vermek istiyorum',
    tags: ['VIP Transfer', 'Kiralama', 'Taksi']
  },
  { 
    id: 'services', 
    name: 'Profesyonel Hizmet', 
    icon: '🔧',
    description: 'Spor eğitimi, antrenörlük vb.',
    tags: ['Antrenör', 'Fizik Tedavi', 'Masaj']
  },
  { 
    id: 'career', 
    name: 'İş İlanı Veren', 
    icon: '💼',
    description: 'Personel arıyorum, iş ilanı vermek istiyorum',
    tags: ['Tam Zamanlı', 'Yarı Zamanlı', 'Freelance']
  }
]

const productCategories = [
  { id: 'sports', name: 'Spor Malzemesi', icon: '⚽' },
  { id: 'clothing', name: 'Spor Giyim', icon: '👕' },
  { id: 'shoes', name: 'Spor Ayakkabı', icon: '👟' },
  { id: 'equipment', name: 'Fitness Ekipmanı', icon: '🏋️' },
  { id: 'outdoor', name: 'Outdoor', icon: '🏕️' },
  { id: 'cycling', name: 'Bisiklet', icon: '🚴' },
  { id: 'swimming', name: 'Yüzme', icon: '🏊' },
  { id: 'nutrition', name: 'Sporcu Beslenmesi', icon: '🥤' }
]

const cuisineTypes = ['Türk Mutfağı', 'Fast Food', 'Dünya Mutfağı', 'Tatlı/Pasta', 'Kahvaltı', 'Vegan/Vejetaryen', 'Deniz Ürünleri', 'Pizza/İtalyan']

const careerSectors = ['Spor Eğitimi', 'Fitness', 'Satış/Pazarlama', 'Müşteri Hizmetleri', 'Yazılım/IT', 'Lojistik', 'Yönetim', 'Sağlık/Beslenme']

const cities = ['İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya', 'Adana', 'Konya', 'Gaziantep', 'Mersin', 'Diyarbakır', 'Kayseri', 'Eskişehir', 'Samsun', 'Denizli', 'Şanlıurfa', 'Trabzon', 'Malatya', 'Erzurum', 'Van', 'Batman']

const commissionRate = 12

const form = ref({
  service_type: '',
  store_name: '',
  business_type: '',
  business_description: '',
  categories: [] as string[],
  cuisines: [] as string[],
  sectors: [] as string[],
  vehicle_count: null as number | null,
  service_region: '',
  room_count: null as number | null,
  star_rating: '',
  contact_name: '',
  phone: '',
  email: '',
  city: '',
  address: '',
  tax_office: '',
  tax_number: '',
  bank_account: '',
  accept_terms: false
})

const loading = ref(false)
const error = ref('')
const success = ref(false)

const selectedServiceType = computed(() => serviceTypes.find(s => s.id === form.value.service_type))

const toggleCategory = (catId: string) => {
  const idx = form.value.categories.indexOf(catId)
  if (idx > -1) {
    form.value.categories.splice(idx, 1)
  } else {
    form.value.categories.push(catId)
  }
}

const toggleCuisine = (cuisine: string) => {
  const idx = form.value.cuisines.indexOf(cuisine)
  if (idx > -1) {
    form.value.cuisines.splice(idx, 1)
  } else {
    form.value.cuisines.push(cuisine)
  }
}

const toggleSector = (sector: string) => {
  const idx = form.value.sectors.indexOf(sector)
  if (idx > -1) {
    form.value.sectors.splice(idx, 1)
  } else {
    form.value.sectors.push(sector)
  }
}

async function submitApplication() {
  if (!form.value.service_type) {
    error.value = 'Lütfen bir hizmet türü seçin'
    return
  }

  loading.value = true
  error.value = ''

  try {
    // Build service_data based on service type
    const serviceData: Record<string, any> = {}
    
    if (form.value.service_type === 'products') {
      serviceData.categories = form.value.categories
    } else if (form.value.service_type === 'food') {
      serviceData.cuisines = form.value.cuisines
    } else if (form.value.service_type === 'hotel') {
      serviceData.room_count = form.value.room_count
      serviceData.star_rating = form.value.star_rating
    } else if (form.value.service_type === 'transport') {
      serviceData.vehicle_count = form.value.vehicle_count
      serviceData.service_region = form.value.service_region
    } else if (form.value.service_type === 'career') {
      serviceData.sectors = form.value.sectors
    }

    const payload = {
      service_type: form.value.service_type,
      store_name: form.value.store_name,
      business_type: form.value.business_type,
      description: form.value.business_description,
      categories: form.value.service_type === 'products' ? form.value.categories : [],
      contact_name: form.value.contact_name,
      phone: form.value.phone,
      email: form.value.email,
      city: form.value.city,
      address: form.value.address,
      tax_office: form.value.tax_office,
      tax_number: form.value.tax_number,
      bank_account: form.value.bank_account,
      service_data: serviceData
    }

    await axios.post('/api/seller-application/register', payload)
    
    success.value = true
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Başvuru gönderilemedi. Lütfen tekrar deneyin.'
  } finally {
    loading.value = false
  }
}
</script>