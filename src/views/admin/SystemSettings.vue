<template>
  <div class="system-settings">
    <!-- Header -->
    <div class="header">
      <div>
        <h1>⚙️ Sistem Ayarları</h1>
        <p class="subtitle">Platform yapılandırması ve entegrasyonlar</p>
      </div>
      <button @click="saveAllSettings" class="btn btn-primary" :disabled="saving">
        {{ saving ? 'Kaydediliyor...' : '💾 Tüm Ayarları Kaydet' }}
      </button>
    </div>

    <!-- Settings Tabs -->
    <div class="settings-tabs">
      <button 
        v-for="tab in tabs" 
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="['tab-btn', { active: activeTab === tab.id }]"
      >
        <span>{{ tab.icon }}</span>
        {{ tab.label }}
      </button>
    </div>

    <!-- General Settings -->
    <div v-show="activeTab === 'general'" class="settings-section">
      <div class="section-card">
        <h3>🏢 Genel Bilgiler</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Site Adı *</label>
            <input v-model="settings.general.site_name" type="text" required />
          </div>
          <div class="form-group">
            <label>Site URL *</label>
            <input v-model="settings.general.site_url" type="url" required />
          </div>
          <div class="form-group">
            <label>Destek Email *</label>
            <input v-model="settings.general.support_email" type="email" required />
          </div>
          <div class="form-group">
            <label>Destek Telefon</label>
            <input v-model="settings.general.support_phone" type="tel" />
          </div>
          <div class="form-group full">
            <label>Site Açıklaması</label>
            <textarea v-model="settings.general.site_description" rows="3"></textarea>
          </div>
        </div>
      </div>

      <div class="section-card">
        <h3>🖼️ Logo ve Görsel</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Logo</label>
            <div class="image-upload">
              <input type="file" @change="uploadLogo" accept="image/*" />
              <img v-if="settings.general.logo_url" :src="settings.general.logo_url" alt="Logo" class="preview-image" />
            </div>
          </div>
          <div class="form-group">
            <label>Favicon</label>
            <div class="image-upload">
              <input type="file" @change="uploadFavicon" accept="image/*" />
              <img v-if="settings.general.favicon_url" :src="settings.general.favicon_url" alt="Favicon" class="preview-image-small" />
            </div>
          </div>
        </div>
      </div>

      <div class="section-card">
        <h3>💰 Para Birimi ve Vergi</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Para Birimi</label>
            <select v-model="settings.general.currency">
              <option value="TRY">TRY (₺)</option>
              <option value="USD">USD ($)</option>
              <option value="EUR">EUR (€)</option>
            </select>
          </div>
          <div class="form-group">
            <label>KDV Oranı (%)</label>
            <input v-model.number="settings.general.vat_rate" type="number" min="0" max="100" />
          </div>
          <div class="form-group">
            <label>Minimum Sipariş Tutarı</label>
            <input v-model.number="settings.general.min_order_amount" type="number" min="0" />
          </div>
          <div class="form-group">
            <label>Ücretsiz Kargo Limiti</label>
            <input v-model.number="settings.general.free_shipping_limit" type="number" min="0" />
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Settings -->
    <div v-show="activeTab === 'payment'" class="settings-section">
      <div class="section-card">
        <h3>💳 Ödeme Gateway'leri</h3>
        
        <!-- Iyzico -->
        <div class="gateway-card">
          <div class="gateway-header">
            <div>
              <h4>Iyzico</h4>
              <p>Kredi kartı ve sanal POS</p>
            </div>
            <label class="toggle">
              <input v-model="settings.payment.iyzico.enabled" type="checkbox" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div v-if="settings.payment.iyzico.enabled" class="gateway-settings">
            <div class="form-grid">
              <div class="form-group">
                <label>API Key</label>
                <input v-model="settings.payment.iyzico.api_key" type="text" />
              </div>
              <div class="form-group">
                <label>Secret Key</label>
                <input v-model="settings.payment.iyzico.secret_key" type="password" />
              </div>
              <div class="form-group">
                <label>Mod</label>
                <select v-model="settings.payment.iyzico.mode">
                  <option value="sandbox">Test (Sandbox)</option>
                  <option value="live">Canlı (Production)</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- PayTR -->
        <div class="gateway-card">
          <div class="gateway-header">
            <div>
              <h4>PayTR</h4>
              <p>Türk ödeme sistemi</p>
            </div>
            <label class="toggle">
              <input v-model="settings.payment.paytr.enabled" type="checkbox" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div v-if="settings.payment.paytr.enabled" class="gateway-settings">
            <div class="form-grid">
              <div class="form-group">
                <label>Merchant ID</label>
                <input v-model="settings.payment.paytr.merchant_id" type="text" />
              </div>
              <div class="form-group">
                <label>Merchant Key</label>
                <input v-model="settings.payment.paytr.merchant_key" type="password" />
              </div>
              <div class="form-group">
                <label>Merchant Salt</label>
                <input v-model="settings.payment.paytr.merchant_salt" type="password" />
              </div>
            </div>
          </div>
        </div>

        <!-- MokaPOS -->
        <div class="gateway-card">
          <div class="gateway-header">
            <div>
              <h4>MokaPOS</h4>
              <p>Sanal POS çözümü</p>
            </div>
            <label class="toggle">
              <input v-model="settings.payment.mokapos.enabled" type="checkbox" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div v-if="settings.payment.mokapos.enabled" class="gateway-settings">
            <div class="form-grid">
              <div class="form-group">
                <label>Dealer Code</label>
                <input v-model="settings.payment.mokapos.dealer_code" type="text" />
              </div>
              <div class="form-group">
                <label>Username</label>
                <input v-model="settings.payment.mokapos.username" type="text" />
              </div>
              <div class="form-group">
                <label>Password</label>
                <input v-model="settings.payment.mokapos.password" type="password" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Shipping Settings -->
    <div v-show="activeTab === 'shipping'" class="settings-section">
      <div class="section-card">
        <h3>📦 Kargo Şirketleri</h3>

        <!-- Aras Kargo -->
        <div class="shipping-card">
          <div class="shipping-header">
            <div>
              <h4>Aras Kargo</h4>
              <p>Entegrasyon ve API ayarları</p>
            </div>
            <label class="toggle">
              <input v-model="settings.shipping.aras.enabled" type="checkbox" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div v-if="settings.shipping.aras.enabled" class="shipping-settings">
            <div class="form-grid">
              <div class="form-group">
                <label>Müşteri Kodu</label>
                <input v-model="settings.shipping.aras.customer_code" type="text" />
              </div>
              <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input v-model="settings.shipping.aras.username" type="text" />
              </div>
              <div class="form-group">
                <label>Şifre</label>
                <input v-model="settings.shipping.aras.password" type="password" />
              </div>
              <div class="form-group">
                <label>Kargo Ücreti (₺)</label>
                <input v-model.number="settings.shipping.aras.price" type="number" min="0" />
              </div>
            </div>
          </div>
        </div>

        <!-- Yurtiçi Kargo -->
        <div class="shipping-card">
          <div class="shipping-header">
            <div>
              <h4>Yurtiçi Kargo</h4>
              <p>Entegrasyon ve API ayarları</p>
            </div>
            <label class="toggle">
              <input v-model="settings.shipping.yurtici.enabled" type="checkbox" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div v-if="settings.shipping.yurtici.enabled" class="shipping-settings">
            <div class="form-grid">
              <div class="form-group">
                <label>Müşteri No</label>
                <input v-model="settings.shipping.yurtici.customer_no" type="text" />
              </div>
              <div class="form-group">
                <label>API Key</label>
                <input v-model="settings.shipping.yurtici.api_key" type="text" />
              </div>
              <div class="form-group">
                <label>Kargo Ücreti (₺)</label>
                <input v-model.number="settings.shipping.yurtici.price" type="number" min="0" />
              </div>
            </div>
          </div>
        </div>

        <!-- MNG Kargo -->
        <div class="shipping-card">
          <div class="shipping-header">
            <div>
              <h4>MNG Kargo</h4>
              <p>Entegrasyon ve API ayarları</p>
            </div>
            <label class="toggle">
              <input v-model="settings.shipping.mng.enabled" type="checkbox" />
              <span class="toggle-slider"></span>
            </label>
          </div>
          <div v-if="settings.shipping.mng.enabled" class="shipping-settings">
            <div class="form-grid">
              <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input v-model="settings.shipping.mng.username" type="text" />
              </div>
              <div class="form-group">
                <label>Şifre</label>
                <input v-model="settings.shipping.mng.password" type="password" />
              </div>
              <div class="form-group">
                <label>Kargo Ücreti (₺)</label>
                <input v-model.number="settings.shipping.mng.price" type="number" min="0" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Email Settings -->
    <div v-show="activeTab === 'email'" class="settings-section">
      <div class="section-card">
        <h3>📧 Email Ayarları</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>SMTP Host</label>
            <input v-model="settings.email.smtp_host" type="text" />
          </div>
          <div class="form-group">
            <label>SMTP Port</label>
            <input v-model.number="settings.email.smtp_port" type="number" />
          </div>
          <div class="form-group">
            <label>SMTP Kullanıcı Adı</label>
            <input v-model="settings.email.smtp_username" type="text" />
          </div>
          <div class="form-group">
            <label>SMTP Şifre</label>
            <input v-model="settings.email.smtp_password" type="password" />
          </div>
          <div class="form-group">
            <label>Gönderen Adı</label>
            <input v-model="settings.email.from_name" type="text" />
          </div>
          <div class="form-group">
            <label>Gönderen Email</label>
            <input v-model="settings.email.from_email" type="email" />
          </div>
        </div>

        <div class="test-section">
          <h4>Email Testi</h4>
          <div class="test-form">
            <input v-model="testEmail" type="email" placeholder="Test email adresi" />
            <button @click="sendTestEmail" class="btn btn-secondary">Test Email Gönder</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Commission Settings -->
    <div v-show="activeTab === 'commission'" class="settings-section">
      <div class="section-card">
        <h3>💼 Komisyon Oranları</h3>
        
        <div class="commission-card">
          <h4>Genel Komisyon</h4>
          <div class="form-grid">
            <div class="form-group">
              <label>Platform Komisyonu (%)</label>
              <input v-model.number="settings.commission.default_rate" type="number" min="0" max="100" step="0.1" />
              <small>Tüm satışlar için varsayılan oran</small>
            </div>
          </div>
        </div>

        <div class="commission-card">
          <h4>Kategori Bazlı Komisyon</h4>
          <div class="category-commissions">
            <div v-for="(commission, index) in settings.commission.categories" :key="index" class="commission-row">
              <select v-model="commission.category_id" class="category-select">
                <option value="">Kategori Seçiniz</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
              <input v-model.number="commission.rate" type="number" min="0" max="100" step="0.1" placeholder="%" />
              <button @click="removeCommission(index)" class="btn-icon danger">🗑️</button>
            </div>
            <button @click="addCommission" class="btn btn-sm btn-secondary">+ Kategori Ekle</button>
          </div>
        </div>

        <div class="commission-card">
          <h4>Satıcı Bazlı Komisyon</h4>
          <div class="form-group">
            <label>
              <input v-model="settings.commission.allow_seller_specific" type="checkbox" />
              Satıcılara özel komisyon oranı tanımlanabilsin
            </label>
            <small>Satıcı yönetim panelinden her satıcı için özel oran belirlenebilir</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Maintenance Mode -->
    <div v-show="activeTab === 'maintenance'" class="settings-section">
      <div class="section-card">
        <h3>🔧 Bakım Modu</h3>
        
        <div class="maintenance-toggle">
          <label class="toggle large">
            <input v-model="settings.maintenance.enabled" type="checkbox" />
            <span class="toggle-slider"></span>
          </label>
          <div class="maintenance-info">
            <h4>{{ settings.maintenance.enabled ? 'Bakım Modu Aktif' : 'Bakım Modu Pasif' }}</h4>
            <p>{{ settings.maintenance.enabled ? 'Site ziyaretçilere kapalıdır' : 'Site normal şekilde çalışıyor' }}</p>
          </div>
        </div>

        <div v-if="settings.maintenance.enabled" class="form-grid">
          <div class="form-group full">
            <label>Bakım Mesajı</label>
            <textarea v-model="settings.maintenance.message" rows="4"></textarea>
          </div>
          <div class="form-group">
            <label>Tahmini Bitiş Zamanı</label>
            <input v-model="settings.maintenance.estimated_end" type="datetime-local" />
          </div>
          <div class="form-group">
            <label>İzin Verilen IP'ler</label>
            <textarea v-model="settings.maintenance.allowed_ips" rows="3" placeholder="Her satıra bir IP adresi"></textarea>
            <small>Bu IP'lerden gelen ziyaretçiler siteye erişebilir</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const activeTab = ref('general')
const saving = ref(false)
const testEmail = ref('')
const categories = ref([])

const tabs = [
  { id: 'general', icon: '🏢', label: 'Genel' },
  { id: 'payment', icon: '💳', label: 'Ödeme' },
  { id: 'shipping', icon: '📦', label: 'Kargo' },
  { id: 'email', icon: '📧', label: 'Email' },
  { id: 'commission', icon: '💼', label: 'Komisyon' },
  { id: 'maintenance', icon: '🔧', label: 'Bakım' }
]

const settings = reactive({
  general: {
    site_name: '',
    site_url: '',
    site_description: '',
    support_email: '',
    support_phone: '',
    logo_url: '',
    favicon_url: '',
    currency: 'TRY',
    vat_rate: 20,
    min_order_amount: 0,
    free_shipping_limit: 150
  },
  payment: {
    iyzico: {
      enabled: false,
      api_key: '',
      secret_key: '',
      mode: 'sandbox'
    },
    paytr: {
      enabled: false,
      merchant_id: '',
      merchant_key: '',
      merchant_salt: ''
    },
    mokapos: {
      enabled: false,
      dealer_code: '',
      username: '',
      password: ''
    }
  },
  shipping: {
    aras: {
      enabled: false,
      customer_code: '',
      username: '',
      password: '',
      price: 15
    },
    yurtici: {
      enabled: false,
      customer_no: '',
      api_key: '',
      price: 15
    },
    mng: {
      enabled: false,
      username: '',
      password: '',
      price: 15
    }
  },
  email: {
    smtp_host: '',
    smtp_port: 587,
    smtp_username: '',
    smtp_password: '',
    from_name: '',
    from_email: ''
  },
  commission: {
    default_rate: 10,
    allow_seller_specific: true,
    categories: [] as Array<{category_id: number, rate: number}>
  },
  maintenance: {
    enabled: false,
    message: '',
    estimated_end: '',
    allowed_ips: ''
  }
})

onMounted(() => {
  loadSettings()
  loadCategories()
})

const loadSettings = async () => {
  try {
    const response = await axios.get('/api/admin/settings')
    Object.assign(settings, response.data)
  } catch (error) {
    console.error('Ayarlar yüklenemedi:', error)
  }
}

const loadCategories = async () => {
  try {
    const response = await axios.get('/api/admin/categories')
    categories.value = response.data
  } catch (error) {
    console.error('Kategoriler yüklenemedi:', error)
  }
}

const saveAllSettings = async () => {
  saving.value = true
  try {
    await axios.post('/api/admin/settings', settings)
    alert('Ayarlar kaydedildi')
  } catch (error: any) {
    console.error('Ayarlar kaydedilemedi:', error)
    alert(error.response?.data?.message || 'Bir hata oluştu')
  } finally {
    saving.value = false
  }
}

const uploadLogo = async (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    const formData = new FormData()
    formData.append('logo', file)
    try {
      const response = await axios.post('/api/admin/settings/upload-logo', formData)
      settings.general.logo_url = response.data.url
    } catch (error) {
      console.error('Logo yüklenemedi:', error)
      alert('Logo yüklenirken bir hata oluştu')
    }
  }
}

const uploadFavicon = async (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    const formData = new FormData()
    formData.append('favicon', file)
    try {
      const response = await axios.post('/api/admin/settings/upload-favicon', formData)
      settings.general.favicon_url = response.data.url
    } catch (error) {
      console.error('Favicon yüklenemedi:', error)
      alert('Favicon yüklenirken bir hata oluştu')
    }
  }
}

const sendTestEmail = async () => {
  if (!testEmail.value) {
    alert('Email adresi giriniz')
    return
  }

  try {
    await axios.post('/api/admin/settings/test-email', { email: testEmail.value })
    alert('Test email gönderildi')
  } catch (error) {
    console.error('Email gönderilemedi:', error)
    alert('Email gönderilirken bir hata oluştu')
  }
}

const addCommission = () => {
  settings.commission.categories.push({ category_id: 0, rate: 10 })
}

const removeCommission = (index: number) => {
  settings.commission.categories.splice(index, 1)
}
</script>

<style scoped>
.system-settings {
  padding: 24px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.header h1 {
  font-size: 28px;
  font-weight: 600;
  margin: 0 0 4px 0;
}

.subtitle {
  color: #666;
  margin: 0;
}

.settings-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  border-bottom: 2px solid #e5e7eb;
  overflow-x: auto;
}

.tab-btn {
  padding: 12px 24px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.tab-btn:hover {
  color: #2563eb;
}

.tab-btn.active {
  color: #2563eb;
  border-bottom-color: #2563eb;
}

.settings-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.section-card,
.gateway-card,
.shipping-card,
.commission-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 24px;
}

.section-card h3 {
  font-size: 18px;
  font-weight: 600;
  margin: 0 0 20px 0;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
}

.form-group.full {
  grid-column: 1 / -1;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 6px;
  color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  font-family: inherit;
}

.form-group small {
  display: block;
  margin-top: 4px;
  font-size: 12px;
  color: #6b7280;
}

.image-upload {
  border: 2px dashed #d1d5db;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
}

.preview-image {
  max-width: 200px;
  max-height: 60px;
  margin-top: 12px;
}

.preview-image-small {
  width: 32px;
  height: 32px;
  margin-top: 8px;
}

.gateway-header,
.shipping-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e5e7eb;
}

.gateway-header h4,
.shipping-header h4 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 4px 0;
}

.gateway-header p,
.shipping-header p {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
}

.toggle {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 24px;
}

.toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #d1d5db;
  transition: 0.3s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.3s;
  border-radius: 50%;
}

.toggle input:checked + .toggle-slider {
  background-color: #2563eb;
}

.toggle input:checked + .toggle-slider:before {
  transform: translateX(24px);
}

.test-section {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid #e5e7eb;
}

.test-section h4 {
  font-size: 15px;
  font-weight: 600;
  margin: 0 0 12px 0;
}

.test-form {
  display: flex;
  gap: 12px;
  align-items: center;
}

.test-form input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
}

.commission-card {
  margin-bottom: 16px;
}

.commission-card h4 {
  font-size: 15px;
  font-weight: 600;
  margin: 0 0 16px 0;
}

.category-commissions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.commission-row {
  display: flex;
  gap: 12px;
  align-items: center;
}

.category-select {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
}

.commission-row input {
  width: 100px;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
}

.maintenance-toggle {
  display: flex;
  gap: 20px;
  align-items: center;
  padding: 20px;
  background: #f9fafb;
  border-radius: 8px;
  margin-bottom: 24px;
}

.toggle.large {
  width: 64px;
  height: 32px;
}

.toggle.large .toggle-slider:before {
  height: 24px;
  width: 24px;
  left: 4px;
  bottom: 4px;
}

.toggle.large input:checked + .toggle-slider:before {
  transform: translateX(32px);
}

.maintenance-info h4 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 4px 0;
}

.maintenance-info p {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 13px;
}

.btn-icon {
  padding: 6px 10px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.btn-icon.danger:hover {
  background: #fee2e2;
  border-color: #fca5a5;
}
</style>
