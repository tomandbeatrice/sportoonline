<template>
  <div class="registration-container">
    <div class="registration-card">
      <div class="header">
        <h1>🏪 Satıcı Ol</h1>
        <p class="subtitle">Sport Online'da satış yapmaya başlayın</p>
      </div>

      <div v-if="registrationComplete" class="success-message">
        <div class="success-icon"><BadgeIcon name="check" cls="w-16 h-16 text-green-600" /></div>
        <h2>Başvurunuz Alındı!</h2>
        <p>Başvurunuz inceleniyor. Sonuç en kısa sürede e-posta ile bildirilecektir.</p>
        <button @click="$router.push('/')" class="btn-primary">Ana Sayfaya Dön</button>
      </div>

      <form v-else @submit.prevent="submitRegistration" class="registration-form">
        <!-- Kişisel Bilgiler -->
        <section class="form-section">
          <h3>📋 Kişisel Bilgiler</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label>Ad *</label>
              <input 
                v-model="form.first_name" 
                type="text" 
                required
                placeholder="Adınız"
              />
            </div>
            
            <div class="form-group">
              <label>Soyad *</label>
              <input 
                v-model="form.last_name" 
                type="text" 
                required
                placeholder="Soyadınız"
              />
            </div>
          </div>

          <div class="form-group">
            <label>E-posta *</label>
            <input 
              v-model="form.email" 
              type="email" 
              required
              placeholder="ornek@email.com"
            />
          </div>

          <div class="form-group">
            <label>Telefon *</label>
            <input 
              v-model="form.phone" 
              type="tel" 
              required
              placeholder="0555 123 45 67"
            />
          </div>
        </section>

        <!-- Firma Bilgileri -->
        <section class="form-section">
          <h3>🏢 Firma Bilgileri</h3>
          
          <div class="form-group">
            <label>Firma Adı *</label>
            <input 
              v-model="form.company_name" 
              type="text" 
              required
              placeholder="Firma adınız"
            />
          </div>

          <div class="form-group">
            <label>Vergi Numarası / TC Kimlik No *</label>
            <input 
              v-model="form.tax_number" 
              type="text" 
              required
              placeholder="1234567890"
              maxlength="11"
            />
          </div>

          <div class="form-group">
            <label>Vergi Dairesi</label>
            <input 
              v-model="form.tax_office" 
              type="text" 
              placeholder="Vergi dairesi adı"
            />
          </div>

          <div class="form-group">
            <label>Firma Adresi *</label>
            <textarea 
              v-model="form.company_address" 
              required
              rows="3"
              placeholder="Firma adresinizi yazın"
            ></textarea>
          </div>
        </section>

        <!-- Banka Bilgileri -->
        <section class="form-section">
          <h3>💳 Banka Bilgileri</h3>
          
          <div class="form-group">
            <label>Banka Adı *</label>
            <select v-model="form.bank_name" required>
              <option value="">Banka Seçin</option>
              <option value="Ziraat Bankası">Ziraat Bankası</option>
              <option value="İş Bankası">İş Bankası</option>
              <option value="Garanti BBVA">Garanti BBVA</option>
              <option value="Akbank">Akbank</option>
              <option value="Yapı Kredi">Yapı Kredi</option>
              <option value="Halkbank">Halkbank</option>
              <option value="Vakıfbank">Vakıfbank</option>
              <option value="QNB Finansbank">QNB Finansbank</option>
              <option value="DenizBank">DenizBank</option>
              <option value="TEB">TEB</option>
              <option value="Diğer">Diğer</option>
            </select>
          </div>

          <div class="form-group">
            <label>IBAN *</label>
            <input 
              v-model="form.iban" 
              type="text" 
              required
              placeholder="TR00 0000 0000 0000 0000 0000 00"
              maxlength="32"
            />
            <span v-if="ibanError" class="error-text">{{ ibanError }}</span>
          </div>

          <div class="form-group">
            <label>Hesap Sahibi Adı *</label>
            <input 
              v-model="form.account_holder" 
              type="text" 
              required
              placeholder="Hesap sahibinin adı"
            />
          </div>
        </section>

        <!-- Ürün Kategorisi -->
        <section class="form-section">
          <h3>🏷️ Satış Yapacağınız Kategoriler</h3>
          
          <div class="categories-grid">
            <label 
              v-for="category in categories" 
              :key="category.id"
              class="category-checkbox"
            >
              <input 
                type="checkbox" 
                :value="category.id"
                v-model="form.categories"
              />
              <span>{{ category.name }}</span>
            </label>
          </div>
        </section>

        <!-- Sözleşme -->
        <section class="form-section">
          <div class="agreement-box">
            <label class="checkbox-label">
              <input type="checkbox" v-model="form.accept_terms" required />
              <span>
                <a href="/satici-sozlesmesi" target="_blank">Satıcı Sözleşmesi</a>'ni 
                ve 
                <a href="/gizlilik-politikasi" target="_blank">Gizlilik Politikası</a>'nı 
                okudum, kabul ediyorum.
              </span>
            </label>
          </div>
        </section>

        <!-- Submit Button -->
        <div class="form-actions">
          <button type="submit" :disabled="submitting" class="btn-submit">
            <span v-if="!submitting">Başvuru Gönder</span>
            <span v-else>Gönderiliyor...</span>
          </button>
        </div>

        <p v-if="error" class="error-message">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const router = useRouter()

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  company_name: '',
  tax_number: '',
  tax_office: '',
  company_address: '',
  bank_name: '',
  iban: '',
  account_holder: '',
  categories: [] as number[],
  accept_terms: false,
})

const categories = ref([])
const submitting = ref(false)
const error = ref('')
const registrationComplete = ref(false)

const ibanError = computed(() => {
  const iban = form.value.iban.replace(/\s/g, '')
  if (!iban) return ''
  
  if (!iban.startsWith('TR')) {
    return 'IBAN TR ile başlamalıdır'
  }
  
  if (iban.length > 0 && iban.length !== 26) {
    return 'IBAN 26 karakter olmalıdır'
  }
  
  return ''
})

const loadCategories = async () => {
  try {
    const { data } = await axios.get('/api/categories')
    categories.value = data
  } catch (err) {
    console.error('Kategoriler yüklenemedi:', err)
  }
}

const submitRegistration = async () => {
  if (ibanError.value) {
    error.value = 'Lütfen geçerli bir IBAN giriniz'
    return
  }

  if (form.value.categories.length === 0) {
    error.value = 'Lütfen en az bir kategori seçin'
    return
  }

  submitting.value = true
  error.value = ''

  try {
    await axios.post('/api/seller/register', {
      ...form.value,
      iban: form.value.iban.replace(/\s/g, ''), // Remove spaces from IBAN
    })

    registrationComplete.value = true
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Başvuru gönderilemedi. Lütfen tekrar deneyin.'
    console.error('Registration error:', err)
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.registration-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.registration-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  max-width: 800px;
  width: 100%;
  padding: 3rem;
}

.header {
  text-align: center;
  margin-bottom: 3rem;
}

.header h1 {
  font-size: 2.5rem;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.subtitle {
  font-size: 1.125rem;
  color: #6b7280;
}

.success-message {
  text-align: center;
  padding: 3rem 2rem;
}

.success-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
}

.success-message h2 {
  font-size: 2rem;
  color: #10b981;
  margin-bottom: 1rem;
}

.success-message p {
  font-size: 1.125rem;
  color: #6b7280;
  margin-bottom: 2rem;
}

.form-section {
  margin-bottom: 2.5rem;
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 12px;
}

.form-section h3 {
  font-size: 1.25rem;
  color: #1f2937;
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
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
  border-color: #667eea;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.category-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
}

.category-checkbox:hover {
  border-color: #667eea;
}

.category-checkbox input[type="checkbox"] {
  width: auto;
}

.agreement-box {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  border: 2px solid #e5e7eb;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
  margin-top: 0.25rem;
  width: auto;
}

.checkbox-label a {
  color: #667eea;
  text-decoration: underline;
}

.form-actions {
  text-align: center;
  margin-top: 2rem;
}

.btn-submit,
.btn-primary {
  padding: 1rem 3rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1.125rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
}

.btn-submit:hover,
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.error-text {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
}

.error-message {
  color: #ef4444;
  text-align: center;
  margin-top: 1rem;
  padding: 1rem;
  background: #fee2e2;
  border-radius: 8px;
}

@media (max-width: 768px) {
  .registration-card {
    padding: 2rem 1.5rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .categories-grid {
    grid-template-columns: 1fr;
  }
}
</style>
