<template>
  <div class="onboarding-container">
    <div class="onboarding-card">
      <!-- Progress Bar -->
      <div class="progress-bar">
        <div 
          v-for="(step, index) in steps" 
          :key="index"
          :class="['progress-step', {
            active: currentStep === index,
            completed: currentStep > index
          }]"
        >
          <div class="step-circle">{{ index + 1 }}</div>
          <div class="step-label">{{ step.title }}</div>
        </div>
      </div>

      <!-- Step Content -->
      <div class="step-content">
        <!-- Step 1: Hoşgeldin -->
        <div v-if="currentStep === 0" class="welcome-step">
          <div class="welcome-icon">🎉</div>
          <h1>Hoş Geldiniz!</h1>
          <p class="welcome-text">
            Satıcı hesabınız aktif edildi. Hemen satışa başlamak için birkaç basit adımı tamamlayalım.
          </p>
          
          <div class="features-grid">
            <div class="feature-item">
              <div class="feature-icon">📦</div>
              <h3>Ürün Yönetimi</h3>
              <p>Kolayca ürün ekleyin ve stokları yönetin</p>
            </div>
            <div class="feature-item">
              <div class="feature-icon">📊</div>
              <h3>Satış Takibi</h3>
              <p>Detaylı raporlarla satışlarınızı analiz edin</p>
            </div>
            <div class="feature-item">
              <div class="feature-icon">🎯</div>
              <h3>Kampanyalar</h3>
              <p>İndirim kampanyaları oluşturun</p>
            </div>
            <div class="feature-icon">💰</div>
              <h3>Ödemeler</h3>
              <p>Düzenli ödeme alın</p>
            </div>
          </div>

          <button @click="nextStep" class="btn-primary">Başlayalım 🚀</button>
        </div>

        <!-- Step 2: İlk Ürün Ekleme -->
        <div v-if="currentStep === 1" class="product-step">
          <h2>📦 İlk Ürününüzü Ekleyin</h2>
          <p>Satışa başlamak için en az bir ürün eklemeniz gerekiyor.</p>

          <form @submit.prevent="addProduct" class="product-form">
            <div class="form-group">
              <label>Ürün Adı *</label>
              <input 
                v-model="product.name" 
                type="text" 
                required
                placeholder="Örn: Nike Air Max 270"
              />
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Kategori *</label>
                <select v-model="product.category_id" required>
                  <option value="">Kategori Seçin</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label>Fiyat (₺) *</label>
                <input 
                  v-model="product.price" 
                  type="number" 
                  required
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Stok Adedi *</label>
                <input 
                  v-model="product.stock" 
                  type="number" 
                  required
                  min="0"
                  placeholder="0"
                />
              </div>

              <div class="form-group">
                <label>SKU</label>
                <input 
                  v-model="product.sku" 
                  type="text" 
                  placeholder="Stok Kodu"
                />
              </div>
            </div>

            <div class="form-group">
              <label>Ürün Açıklaması</label>
              <textarea 
                v-model="product.description" 
                rows="4"
                placeholder="Ürününüz hakkında detaylı bilgi..."
              ></textarea>
            </div>

            <div class="form-group">
              <label>Ürün Görseli URL</label>
              <input 
                v-model="product.image_url" 
                type="url" 
                placeholder="https://..."
              />
            </div>

            <div class="form-actions">
              <button type="button" @click="skipStep" class="btn-secondary">
                Şimdi Değil
              </button>
              <button type="submit" :disabled="submitting" class="btn-primary">
                {{ submitting ? 'Ekleniyor...' : 'Ürün Ekle' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Step 3: İlk Kampanya (Opsiyonel) -->
        <div v-if="currentStep === 2" class="campaign-step">
          <h2>🎯 İlk Kampanyanızı Oluşturun</h2>
          <p>Müşterilerinizi çekmek için bir indirim kampanyası başlatabilirsiniz (opsiyonel).</p>

          <form @submit.prevent="createCampaign" class="campaign-form">
            <div class="form-group">
              <label>Kampanya Adı</label>
              <input 
                v-model="campaign.name" 
                type="text" 
                placeholder="Örn: Açılış İndirimi"
              />
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>İndirim Tipi</label>
                <select v-model="campaign.type">
                  <option value="percentage">Yüzde İndirim</option>
                  <option value="fixed">Sabit Tutar</option>
                </select>
              </div>

              <div class="form-group">
                <label>
                  {{ campaign.type === 'percentage' ? 'İndirim %' : 'İndirim Tutarı (₺)' }}
                </label>
                <input 
                  v-model="campaign.discount_value" 
                  type="number" 
                  :max="campaign.type === 'percentage' ? 100 : undefined"
                  min="0"
                  step="0.01"
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Başlangıç Tarihi</label>
                <input 
                  v-model="campaign.start_date" 
                  type="datetime-local" 
                />
              </div>

              <div class="form-group">
                <label>Bitiş Tarihi</label>
                <input 
                  v-model="campaign.end_date" 
                  type="datetime-local" 
                />
              </div>
            </div>

            <div class="form-actions">
              <button type="button" @click="skipCampaign" class="btn-secondary">
                Şimdi Değil
              </button>
              <button type="submit" :disabled="submitting" class="btn-primary">
                {{ submitting ? 'Oluşturuluyor...' : 'Kampanya Oluştur' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Step 4: Tamamlandı -->
        <div v-if="currentStep === 3" class="complete-step">
          <div class="success-icon">✅</div>
          <h1>Tebrikler!</h1>
          <p>Satıcı hesabınız hazır. Artık satış yapmaya başlayabilirsiniz.</p>

          <div class="stats-summary">
            <div class="stat-item">
              <div class="stat-value">{{ completedSteps }}</div>
              <div class="stat-label">Tamamlanan Adım</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">{{ addedProductsCount }}</div>
              <div class="stat-label">Eklenen Ürün</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">{{ addedCampaignsCount }}</div>
              <div class="stat-label">Oluşturulan Kampanya</div>
            </div>
          </div>

          <button @click="goToDashboard" class="btn-primary btn-large">
            Satıcı Paneline Git 🎯
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const currentStep = ref(0)
const submitting = ref(false)
const categories = ref([])
const addedProductsCount = ref(0)
const addedCampaignsCount = ref(0)

const steps = [
  { title: 'Hoş Geldin' },
  { title: 'İlk Ürün' },
  { title: 'Kampanya' },
  { title: 'Tamamlandı' },
]

const product = ref({
  name: '',
  category_id: '',
  price: '',
  stock: '',
  sku: '',
  description: '',
  image_url: '',
  is_active: true,
})

const campaign = ref({
  name: '',
  type: 'percentage',
  discount_value: '',
  start_date: new Date().toISOString().slice(0, 16),
  end_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().slice(0, 16),
})

const completedSteps = computed(() => {
  return currentStep.value
})

const nextStep = () => {
  currentStep.value++
}

const skipStep = () => {
  currentStep.value++
}

const skipCampaign = () => {
  currentStep.value++
}

const loadCategories = async () => {
  try {
    const { data } = await axios.get('/api/categories')
    categories.value = data
  } catch (error) {
    console.error('Kategoriler yüklenemedi:', error)
  }
}

const addProduct = async () => {
  submitting.value = true

  try {
    await axios.post('/api/seller/products', product.value)
    addedProductsCount.value++
    alert('Ürün başarıyla eklendi! ✅')
    currentStep.value++
  } catch (error: any) {
    alert(error.response?.data?.message || 'Ürün eklenemedi')
  } finally {
    submitting.value = false
  }
}

const createCampaign = async () => {
  submitting.value = true

  try {
    await axios.post('/api/seller/campaigns', {
      ...campaign.value,
      is_active: true,
    })
    addedCampaignsCount.value++
    alert('Kampanya başarıyla oluşturuldu! 🎉')
    currentStep.value++
  } catch (error: any) {
    alert(error.response?.data?.message || 'Kampanya oluşturulamadı')
  } finally {
    submitting.value = false
  }
}

const goToDashboard = () => {
  router.push('/seller/dashboard')
}

onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.onboarding-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.onboarding-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  max-width: 900px;
  width: 100%;
  overflow: hidden;
}

.progress-bar {
  display: flex;
  justify-content: space-between;
  padding: 2rem;
  background: #f9fafb;
  border-bottom: 2px solid #e5e7eb;
}

.progress-step {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  position: relative;
}

.progress-step:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 1.25rem;
  left: 50%;
  right: -50%;
  height: 2px;
  background: #e5e7eb;
  z-index: 0;
}

.progress-step.completed::after {
  background: #10b981;
}

.step-circle {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  background: #e5e7eb;
  color: #6b7280;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  position: relative;
  z-index: 1;
}

.progress-step.active .step-circle {
  background: #3b82f6;
  color: white;
}

.progress-step.completed .step-circle {
  background: #10b981;
  color: white;
}

.step-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.step-content {
  padding: 3rem;
}

.welcome-step,
.complete-step {
  text-align: center;
}

.welcome-icon,
.success-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
}

h1 {
  font-size: 2.5rem;
  color: #1f2937;
  margin-bottom: 1rem;
}

.welcome-text {
  font-size: 1.125rem;
  color: #6b7280;
  margin-bottom: 3rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.feature-item {
  text-align: center;
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.feature-item h3 {
  font-size: 1.125rem;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.feature-item p {
  font-size: 0.875rem;
  color: #6b7280;
}

.product-step h2,
.campaign-step h2 {
  font-size: 2rem;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.product-step p,
.campaign-step p {
  color: #6b7280;
  margin-bottom: 2rem;
}

.product-form,
.campaign-form {
  max-width: 600px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #3b82f6;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.btn-primary,
.btn-secondary {
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: #e5e7eb;
  color: #374151;
}

.btn-large {
  padding: 1rem 3rem;
  font-size: 1.125rem;
  margin-top: 2rem;
}

.stats-summary {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  margin: 3rem 0;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 3rem;
  font-weight: bold;
  color: #10b981;
}

.stat-label {
  color: #6b7280;
  margin-top: 0.5rem;
}

@media (max-width: 768px) {
  .onboarding-card {
    margin: 1rem;
  }

  .step-content {
    padding: 2rem 1.5rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .stats-summary {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}
</style>
