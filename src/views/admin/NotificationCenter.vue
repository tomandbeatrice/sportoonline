<template>
  <div class="notification-center">
    <div class="header">
      <h1 class="inline-flex items-center gap-2"><BadgeIcon name="bell" cls="w-8 h-8 text-yellow-500" /> Bildirim Merkezi</h1>
      <button @click="sendTestNotification" class="btn-primary">
        <span>✉️</span> Test Bildirimi Gönder
      </button>
    </div>

    <div class="tabs">
      <button 
        v-for="tab in tabs" 
        :key="tab.id"
        :class="['tab', { active: activeTab === tab.id }]"
        @click="activeTab = tab.id"
      >
        <BadgeIcon v-if="tab.iconName" :name="tab.iconName" cls="w-5 h-5 mr-2" />
        <span v-else class="mr-2">{{ tab.icon }}</span>
        {{ tab.label }}
      </button>
    </div>

    <!-- Email Queue Tab -->
    <div v-if="activeTab === 'email'" class="tab-content">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">📧</div>
          <div class="stat-info">
            <div class="stat-label">Bekleyen</div>
            <div class="stat-value">{{ emailStats.pending }}</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">⏳</div>
          <div class="stat-info">
            <div class="stat-label">İşleniyor</div>
            <div class="stat-value">{{ emailStats.processing }}</div>
          </div>
        </div>
        <div class="stat-card success">
          <div class="stat-icon"><BadgeIcon name="check" cls="w-6 h-6 text-green-600" /></div>
          <div class="stat-info">
            <div class="stat-label">Gönderildi</div>
            <div class="stat-value">{{ emailStats.sent }}</div>
          </div>
        </div>
        <div class="stat-card danger">
          <div class="stat-icon"><BadgeIcon name="close" cls="w-6 h-6 text-red-600" /></div>
          <div class="stat-info">
            <div class="stat-label">Başarısız</div>
            <div class="stat-value">{{ emailStats.failed }}</div>
          </div>
        </div>
      </div>

      <div class="filters">
        <input 
          v-model="emailFilters.search" 
          type="text" 
          placeholder="Email ara..."
          class="search-input"
        >
        <select v-model="emailFilters.status">
          <option value="">Tüm Durumlar</option>
          <option value="pending">Bekleyen</option>
          <option value="processing">İşleniyor</option>
          <option value="sent">Gönderildi</option>
          <option value="failed">Başarısız</option>
        </select>
        <button @click="loadEmailQueue" class="btn-secondary">
          🔄 Yenile
        </button>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Email</th>
              <th>Konu</th>
              <th>Durum</th>
              <th>Deneme</th>
              <th>Oluşturulma</th>
              <th>İşlem</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="email in emailQueue" :key="email.id">
              <td>{{ email.to }}</td>
              <td>{{ email.subject }}</td>
              <td>
                <span :class="['badge', email.status]">
                  {{ getStatusLabel(email.status) }}
                </span>
              </td>
              <td>{{ email.attempts }}/{{ email.max_attempts }}</td>
              <td>{{ formatDate(email.created_at) }}</td>
              <td>
                <button 
                  v-if="email.status === 'failed'" 
                  @click="retryEmail(email.id)"
                  class="btn-sm btn-primary"
                >
                  🔄 Tekrar Dene
                </button>
                <button 
                  @click="viewEmail(email)"
                  class="btn-sm btn-secondary"
                >
                  👁️ Görüntüle
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- SMS Tab -->
    <div v-if="activeTab === 'sms'" class="tab-content">
      <div class="sms-config">
        <h2>SMS Ayarları</h2>
        
        <div class="form-group">
          <label>SMS Sağlayıcı</label>
          <select v-model="smsConfig.provider">
            <option value="">Seçiniz</option>
            <option value="netgsm">Netgsm</option>
            <option value="iletimerkezi">İleti Merkezi</option>
          </select>
        </div>

        <template v-if="smsConfig.provider === 'netgsm'">
          <div class="form-group">
            <label>Kullanıcı Adı</label>
            <input v-model="smsConfig.username" type="text">
          </div>
          <div class="form-group">
            <label>Şifre</label>
            <input v-model="smsConfig.password" type="password">
          </div>
          <div class="form-group">
            <label>Başlık</label>
            <input v-model="smsConfig.header" type="text" placeholder="8503040000">
          </div>
        </template>

        <template v-if="smsConfig.provider === 'iletimerkezi'">
          <div class="form-group">
            <label>API Key</label>
            <input v-model="smsConfig.api_key" type="text">
          </div>
          <div class="form-group">
            <label>API Secret</label>
            <input v-model="smsConfig.api_secret" type="password">
          </div>
          <div class="form-group">
            <label>Sender</label>
            <input v-model="smsConfig.sender" type="text">
          </div>
        </template>

        <div class="form-group">
          <label>
            <input v-model="smsConfig.enabled" type="checkbox">
            SMS Gönderimini Etkinleştir
          </label>
        </div>

        <button @click="saveSmsConfig" class="btn-primary">💾 Kaydet</button>
      </div>

      <div class="sms-templates">
        <h2>SMS Şablonları</h2>
        <button @click="showTemplateModal = true" class="btn-primary">
          ➕ Yeni Şablon
        </button>

        <div class="templates-list">
          <div v-for="template in smsTemplates" :key="template.id" class="template-card">
            <h3>{{ template.name }}</h3>
            <p class="template-code">{{ template.code }}</p>
            <p class="template-content">{{ template.content }}</p>
            <div class="template-actions">
              <button @click="editTemplate(template)" class="btn-sm btn-secondary">
                ✏️ Düzenle
              </button>
              <button @click="sendTestSms(template)" class="btn-sm btn-primary">
                📱 Test Gönder
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="sms-history">
        <h2>SMS Geçmişi</h2>
        <table>
          <thead>
            <tr>
              <th>Telefon</th>
              <th>Mesaj</th>
              <th>Durum</th>
              <th>Gönderim Tarihi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sms in smsHistory" :key="sms.id">
              <td>{{ sms.phone }}</td>
              <td>{{ sms.message }}</td>
              <td>
                <span :class="['badge', sms.status]">
                  {{ getStatusLabel(sms.status) }}
                </span>
              </td>
              <td>{{ formatDate(sms.sent_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Push Notifications Tab -->
    <div v-if="activeTab === 'push'" class="tab-content">
      <div class="push-config">
        <h2>Push Notification Ayarları</h2>
        
        <div class="form-group">
          <label>Firebase Server Key</label>
          <input v-model="pushConfig.firebase_server_key" type="password">
        </div>

        <div class="form-group">
          <label>Firebase Sender ID</label>
          <input v-model="pushConfig.firebase_sender_id" type="text">
        </div>

        <div class="form-group">
          <label>
            <input v-model="pushConfig.enabled" type="checkbox">
            Push Notification Etkinleştir
          </label>
        </div>

        <button @click="savePushConfig" class="btn-primary">💾 Kaydet</button>
      </div>

      <div class="send-push">
        <h2>Push Notification Gönder</h2>
        
        <div class="form-group">
          <label>Hedef Kitle</label>
          <select v-model="pushForm.audience">
            <option value="all">Tüm Kullanıcılar</option>
            <option value="buyers">Alıcılar</option>
            <option value="sellers">Satıcılar</option>
            <option value="segment">Segment</option>
          </select>
        </div>

        <div v-if="pushForm.audience === 'segment'" class="form-group">
          <label>Segment Seç</label>
          <select v-model="pushForm.segment">
            <option value="vip">VIP Müşteriler</option>
            <option value="regular">Normal Müşteriler</option>
            <option value="new">Yeni Müşteriler</option>
          </select>
        </div>

        <div class="form-group">
          <label>Başlık</label>
          <input v-model="pushForm.title" type="text">
        </div>

        <div class="form-group">
          <label>Mesaj</label>
          <textarea v-model="pushForm.message" rows="4"></textarea>
        </div>

        <div class="form-group">
          <label>İkon URL</label>
          <input v-model="pushForm.icon" type="text" placeholder="https://...">
        </div>

        <div class="form-group">
          <label>Tıklama Aksiyonu (URL)</label>
          <input v-model="pushForm.click_action" type="text" placeholder="/products/123">
        </div>

        <button @click="sendPushNotification" class="btn-primary">
          🚀 Gönder
        </button>
      </div>

      <div class="push-history">
        <h2>Push Notification Geçmişi</h2>
        <table>
          <thead>
            <tr>
              <th>Başlık</th>
              <th>Mesaj</th>
              <th>Hedef</th>
              <th>Gönderildi</th>
              <th>Tıklama</th>
              <th>Tarih</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="push in pushHistory" :key="push.id">
              <td>{{ push.title }}</td>
              <td>{{ push.message }}</td>
              <td>
                <span class="badge">{{ push.audience }}</span>
              </td>
              <td>{{ push.sent_count }}</td>
              <td>{{ push.click_count }}</td>
              <td>{{ formatDate(push.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- System Notifications Tab -->
    <div v-if="activeTab === 'system'" class="tab-content">
      <div class="notification-types">
        <h2>Bildirim Türleri</h2>
        
        <div class="type-list">
          <div v-for="type in notificationTypes" :key="type.code" class="type-card">
            <div class="type-header">
              <h3>{{ type.name }}</h3>
              <label class="toggle-switch">
                <input v-model="type.enabled" type="checkbox" @change="toggleNotificationType(type)">
                <span class="slider"></span>
              </label>
            </div>
            <p>{{ type.description }}</p>
            <div class="type-channels">
              <label>
                <input v-model="type.channels.email" type="checkbox"> Email
              </label>
              <label>
                <input v-model="type.channels.sms" type="checkbox"> SMS
              </label>
              <label>
                <input v-model="type.channels.push" type="checkbox"> Push
              </label>
              <label>
                <input v-model="type.channels.database" type="checkbox"> Database
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="recent-notifications">
        <h2>Son Bildirimler</h2>
        <table>
          <thead>
            <tr>
              <th>Kullanıcı</th>
              <th>Tür</th>
              <th>Mesaj</th>
              <th>Kanal</th>
              <th>Okundu</th>
              <th>Tarih</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="notification in recentNotifications" :key="notification.id">
              <td>{{ notification.user.name }}</td>
              <td>
                <span class="badge">{{ notification.type }}</span>
              </td>
              <td>{{ notification.message }}</td>
              <td>{{ notification.channel }}</td>
              <td>
                <span :class="['badge', notification.read_at ? 'success' : 'warning']">
                  {{ notification.read_at ? 'Okundu' : 'Okunmadı' }}
                </span>
              </td>
              <td>{{ formatDate(notification.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Email Detail Modal -->
    <div v-if="showEmailModal" class="modal" @click.self="showEmailModal = false">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Email Detayı</h2>
          <button @click="showEmailModal = false" class="close-btn">×</button>
        </div>
        <div class="modal-body">
          <div class="detail-row">
            <strong>Alıcı:</strong> {{ selectedEmail.to }}
          </div>
          <div class="detail-row">
            <strong>Konu:</strong> {{ selectedEmail.subject }}
          </div>
          <div class="detail-row">
            <strong>Durum:</strong> 
            <span :class="['badge', selectedEmail.status]">
              {{ getStatusLabel(selectedEmail.status) }}
            </span>
          </div>
          <div class="detail-row">
            <strong>Deneme:</strong> {{ selectedEmail.attempts }}/{{ selectedEmail.max_attempts }}
          </div>
          <div class="detail-row">
            <strong>İçerik:</strong>
            <div class="email-content" v-html="selectedEmail.content"></div>
          </div>
          <div v-if="selectedEmail.error" class="detail-row">
            <strong>Hata:</strong>
            <div class="error-message">{{ selectedEmail.error }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- SMS Template Modal -->
    <div v-if="showTemplateModal" class="modal" @click.self="showTemplateModal = false">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ templateForm.id ? 'Şablon Düzenle' : 'Yeni Şablon' }}</h2>
          <button @click="showTemplateModal = false" class="close-btn">×</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Şablon Adı</label>
            <input v-model="templateForm.name" type="text">
          </div>
          <div class="form-group">
            <label>Kod</label>
            <input v-model="templateForm.code" type="text" placeholder="order_confirmation">
          </div>
          <div class="form-group">
            <label>İçerik</label>
            <textarea v-model="templateForm.content" rows="4" placeholder="Merhaba {name}, siparişiniz alındı..."></textarea>
          </div>
          <div class="form-group">
            <label>Değişkenler</label>
            <p class="help-text">Kullanılabilir: {name}, {order_id}, {amount}, {date}</p>
          </div>
          <button @click="saveTemplate" class="btn-primary">💾 Kaydet</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const activeTab = ref('email')
const tabs = [
  { id: 'email', label: 'Email Kuyruğu', icon: '📧' },
  { id: 'sms', label: 'SMS', icon: '📱' },
  { id: 'push', label: 'Push Notification', iconName: 'bell' },
  { id: 'system', label: 'Sistem Bildirimleri', icon: '⚙️' }
]

// Email Queue
const emailStats = reactive({
  pending: 0,
  processing: 0,
  sent: 0,
  failed: 0
})

const emailFilters = reactive({
  search: '',
  status: ''
})

const emailQueue = ref([])
const showEmailModal = ref(false)
const selectedEmail = ref({})

// SMS
const smsConfig = reactive({
  provider: '',
  username: '',
  password: '',
  header: '',
  api_key: '',
  api_secret: '',
  sender: '',
  enabled: false
})

const smsTemplates = ref([])
const smsHistory = ref([])
const showTemplateModal = ref(false)
const templateForm = reactive({
  id: null,
  name: '',
  code: '',
  content: ''
})

// Push Notifications
const pushConfig = reactive({
  firebase_server_key: '',
  firebase_sender_id: '',
  enabled: false
})

const pushForm = reactive({
  audience: 'all',
  segment: '',
  title: '',
  message: '',
  icon: '',
  click_action: ''
})

const pushHistory = ref([])

// System Notifications
const notificationTypes = ref([
  {
    code: 'order_created',
    name: 'Sipariş Oluşturuldu',
    description: 'Yeni sipariş oluşturulduğunda bildirim gönder',
    enabled: true,
    channels: { email: true, sms: false, push: true, database: true }
  },
  {
    code: 'order_shipped',
    name: 'Sipariş Kargoya Verildi',
    description: 'Sipariş kargoya verildiğinde bildirim gönder',
    enabled: true,
    channels: { email: true, sms: true, push: true, database: true }
  },
  {
    code: 'order_delivered',
    name: 'Sipariş Teslim Edildi',
    description: 'Sipariş teslim edildiğinde bildirim gönder',
    enabled: true,
    channels: { email: true, sms: false, push: true, database: true }
  },
  {
    code: 'seller_approved',
    name: 'Satıcı Onaylandı',
    description: 'Satıcı başvurusu onaylandığında bildirim gönder',
    enabled: true,
    channels: { email: true, sms: true, push: false, database: true }
  },
  {
    code: 'low_stock',
    name: 'Düşük Stok Uyarısı',
    description: 'Ürün stoğu azaldığında bildirim gönder',
    enabled: true,
    channels: { email: true, sms: false, push: false, database: true }
  }
])

const recentNotifications = ref([])

onMounted(() => {
  loadEmailQueue()
  loadSmsConfig()
  loadSmsTemplates()
  loadSmsHistory()
  loadPushConfig()
  loadPushHistory()
  loadRecentNotifications()
})

// Email Queue Functions
const loadEmailQueue = async () => {
  try {
    const response = await axios.get('/api/admin/notifications/email-queue', {
      params: emailFilters
    })
    emailQueue.value = response.data.emails
    emailStats.pending = response.data.stats.pending
    emailStats.processing = response.data.stats.processing
    emailStats.sent = response.data.stats.sent
    emailStats.failed = response.data.stats.failed
  } catch (error) {
    console.error('Email queue yüklenemedi:', error)
  }
}

const retryEmail = async (id) => {
  try {
    await axios.post(`/api/admin/notifications/email-queue/${id}/retry`)
    alert('Email yeniden kuyruğa eklendi')
    loadEmailQueue()
  } catch (error) {
    console.error('Email retry hatası:', error)
  }
}

const viewEmail = (email) => {
  selectedEmail.value = email
  showEmailModal.value = true
}

// SMS Functions
const loadSmsConfig = async () => {
  try {
    const response = await axios.get('/api/admin/notifications/sms-config')
    Object.assign(smsConfig, response.data)
  } catch (error) {
    console.error('SMS config yüklenemedi:', error)
  }
}

const saveSmsConfig = async () => {
  try {
    await axios.post('/api/admin/notifications/sms-config', smsConfig)
    alert('SMS ayarları kaydedildi')
  } catch (error) {
    console.error('SMS config kayıt hatası:', error)
  }
}

const loadSmsTemplates = async () => {
  try {
    const response = await axios.get('/api/admin/notifications/sms-templates')
    smsTemplates.value = response.data
  } catch (error) {
    console.error('SMS templates yüklenemedi:', error)
  }
}

const loadSmsHistory = async () => {
  try {
    const response = await axios.get('/api/admin/notifications/sms-history')
    smsHistory.value = response.data
  } catch (error) {
    console.error('SMS history yüklenemedi:', error)
  }
}

const editTemplate = (template) => {
  Object.assign(templateForm, template)
  showTemplateModal.value = true
}

const saveTemplate = async () => {
  try {
    if (templateForm.id) {
      await axios.put(`/api/admin/notifications/sms-templates/${templateForm.id}`, templateForm)
    } else {
      await axios.post('/api/admin/notifications/sms-templates', templateForm)
    }
    alert('Şablon kaydedildi')
    showTemplateModal.value = false
    loadSmsTemplates()
    resetTemplateForm()
  } catch (error) {
    console.error('Template kayıt hatası:', error)
  }
}

const sendTestSms = async (template) => {
  const phone = prompt('Test göndermek için telefon numarası girin:')
  if (!phone) return

  try {
    await axios.post('/api/admin/notifications/sms-test', {
      phone,
      template_id: template.id
    })
    alert('Test SMS gönderildi')
  } catch (error) {
    console.error('Test SMS hatası:', error)
  }
}

const resetTemplateForm = () => {
  templateForm.id = null
  templateForm.name = ''
  templateForm.code = ''
  templateForm.content = ''
}

// Push Functions
const loadPushConfig = async () => {
  try {
    const response = await axios.get('/api/admin/notifications/push-config')
    Object.assign(pushConfig, response.data)
  } catch (error) {
    console.error('Push config yüklenemedi:', error)
  }
}

const savePushConfig = async () => {
  try {
    await axios.post('/api/admin/notifications/push-config', pushConfig)
    alert('Push notification ayarları kaydedildi')
  } catch (error) {
    console.error('Push config kayıt hatası:', error)
  }
}

const sendPushNotification = async () => {
  try {
    await axios.post('/api/admin/notifications/push-send', pushForm)
    alert('Push notification gönderildi')
    loadPushHistory()
    // Reset form
    pushForm.title = ''
    pushForm.message = ''
    pushForm.icon = ''
    pushForm.click_action = ''
  } catch (error) {
    console.error('Push gönderim hatası:', error)
  }
}

const loadPushHistory = async () => {
  try {
    const response = await axios.get('/api/admin/notifications/push-history')
    pushHistory.value = response.data
  } catch (error) {
    console.error('Push history yüklenemedi:', error)
  }
}

// System Notifications
const toggleNotificationType = async (type) => {
  try {
    await axios.post('/api/admin/notifications/types', {
      code: type.code,
      enabled: type.enabled,
      channels: type.channels
    })
  } catch (error) {
    console.error('Notification type güncelleme hatası:', error)
  }
}

const loadRecentNotifications = async () => {
  try {
    const response = await axios.get('/api/admin/notifications/recent')
    recentNotifications.value = response.data
  } catch (error) {
    console.error('Recent notifications yüklenemedi:', error)
  }
}

const sendTestNotification = async () => {
  try {
    await axios.post('/api/admin/notifications/test')
    alert('Test bildirimi gönderildi')
  } catch (error) {
    console.error('Test notification hatası:', error)
  }
}

// Helpers
const getStatusLabel = (status) => {
  const labels = {
    pending: 'Bekliyor',
    processing: 'İşleniyor',
    sent: 'Gönderildi',
    failed: 'Başarısız'
  }
  return labels[status] || status
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('tr-TR')
}
</script>

<style scoped>
.notification-center {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.header h1 {
  font-size: 28px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 30px;
  border-bottom: 2px solid #e5e7eb;
}

.tab {
  padding: 12px 24px;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  cursor: pointer;
  font-size: 15px;
  font-weight: 500;
  color: #6b7280;
  transition: all 0.3s;
}

.tab:hover {
  color: #2563eb;
}

.tab.active {
  color: #2563eb;
  border-bottom-color: #2563eb;
}

.tab-content {
  animation: fadeIn 0.3s;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 15px;
  align-items: center;
}

.stat-card.success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.stat-card.danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.stat-icon {
  font-size: 32px;
}

.stat-info {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  opacity: 0.8;
  margin-bottom: 5px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
}

.filters {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
}

.search-input {
  flex: 1;
  padding: 10px 15px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
}

select {
  padding: 10px 15px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  background: white;
}

.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f9fafb;
}

th {
  padding: 12px;
  text-align: left;
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
}

td {
  padding: 12px;
  border-top: 1px solid #e5e7eb;
  font-size: 14px;
}

.badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge.pending {
  background: #fef3c7;
  color: #92400e;
}

.badge.processing {
  background: #dbeafe;
  color: #1e40af;
}

.badge.sent, .badge.success {
  background: #d1fae5;
  color: #065f46;
}

.badge.failed, .badge.danger {
  background: #fee2e2;
  color: #991b1b;
}

.badge.warning {
  background: #fef3c7;
  color: #92400e;
}

.btn-primary, .btn-secondary, .btn-sm {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-primary:hover {
  background: #1d4ed8;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 12px;
  margin-right: 5px;
}

.sms-config, .push-config, .send-push {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.sms-config h2, .push-config h2, .send-push h2, .sms-templates h2, .notification-types h2, .recent-notifications h2, .sms-history h2, .push-history h2 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
  color: #1f2937;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 8px;
}

.form-group input[type="text"],
.form-group input[type="password"],
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px 15px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
}

.form-group input[type="checkbox"] {
  margin-right: 8px;
}

.help-text {
  font-size: 13px;
  color: #6b7280;
  margin-top: 5px;
}

.sms-templates {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.templates-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.template-card {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 15px;
}

.template-card h3 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 10px;
  color: #1f2937;
}

.template-code {
  font-size: 12px;
  font-family: monospace;
  color: #6b7280;
  background: #f3f4f6;
  padding: 4px 8px;
  border-radius: 4px;
  display: inline-block;
  margin-bottom: 10px;
}

.template-content {
  font-size: 13px;
  color: #4b5563;
  margin-bottom: 15px;
}

.template-actions {
  display: flex;
  gap: 10px;
}

.type-list {
  display: grid;
  gap: 20px;
}

.type-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
}

.type-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.type-header h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 24px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
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

.slider:before {
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

input:checked + .slider {
  background-color: #2563eb;
}

input:checked + .slider:before {
  transform: translateX(24px);
}

.type-channels {
  display: flex;
  gap: 20px;
  margin-top: 15px;
}

.type-channels label {
  font-size: 14px;
  color: #4b5563;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  margin: 0;
  color: #1f2937;
}

.close-btn {
  background: none;
  border: none;
  font-size: 28px;
  color: #6b7280;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.3s;
}

.close-btn:hover {
  background: #f3f4f6;
}

.modal-body {
  padding: 20px;
}

.detail-row {
  margin-bottom: 15px;
}

.detail-row strong {
  display: block;
  font-size: 13px;
  color: #6b7280;
  margin-bottom: 5px;
}

.email-content {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 15px;
  margin-top: 5px;
}

.error-message {
  background: #fee2e2;
  color: #991b1b;
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  margin-top: 5px;
}

.sms-history, .push-history, .recent-notifications {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.notification-types {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}
</style>
