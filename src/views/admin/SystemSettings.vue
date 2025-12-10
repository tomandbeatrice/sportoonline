<script setup lang="ts">
import { ref, computed } from 'vue'
import SettingsAIAdvisor from '@/components/admin/settings/SettingsAIAdvisor.vue'

// --- Types ---
interface SettingCategory {
  id: string
  label: string
  icon: string
  description: string
}

// --- State ---
const activeCategoryId = ref('general')
const saving = ref(false)

const categories: SettingCategory[] = [
  { id: 'general', label: 'Genel Ayarlar', icon: '🏢', description: 'Site kimliği ve iletişim bilgileri' },
  { id: 'security', label: 'Güvenlik', icon: '🛡️', description: 'Şifre politikaları ve 2FA' },
  { id: 'payment', label: 'Ödeme & Finans', icon: '💳', description: 'Para birimi ve vergi oranları' },
  { id: 'shipping', label: 'Kargo & Teslimat', icon: '🚚', description: 'Kargo limitleri ve firmalar' },
  { id: 'email', label: 'E-posta & Bildirim', icon: '📧', description: 'SMTP ve şablon ayarları' },
  { id: 'integrations', label: 'Entegrasyonlar', icon: '🔌', description: 'API ve 3. parti servisler' }
]

const settings = ref({
  general: {
    site_name: 'SportoOnline',
    site_url: 'https://sportoonline.com',
    support_email: 'destek@sportoonline.com',
    support_phone: '+90 850 123 45 67',
    site_description: 'Türkiye\'nin en büyük spor giyim mağazası.',
    logo_url: 'https://placehold.co/200x60/indigo/white?text=SportoOnline',
    favicon_url: 'https://placehold.co/32x32/indigo/white?text=S'
  },
  security: {
    force_2fa_admin: false,
    password_expiry_days: 90,
    session_timeout_mins: 30,
    debug_mode: true // Intentionally true to trigger AI warning
  },
  payment: {
    currency: 'TRY',
    vat_rate: 18,
    min_order_amount: 100,
    payment_methods: ['credit_card', 'bank_transfer']
  },
  shipping: {
    free_shipping_limit: 500,
    default_carrier: 'yurtici'
  },
  email: {
    smtp_host: 'smtp.sportoonline.com',
    smtp_port: 587,
    sender_name: 'SportoOnline Bilgilendirme'
  }
})

// --- Methods ---
const saveSettings = () => {
  saving.value = true
  setTimeout(() => {
    saving.value = false
    alert('Ayarlar başarıyla kaydedildi!')
  }, 800)
}

const uploadLogo = (event: Event) => {
  // Mock upload
  alert('Logo yükleme simülasyonu')
}
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50 overflow-hidden">
    <!-- Top Bar -->
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          ⚙️ Sistem Ayarları
          <span class="text-xs font-normal bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full border border-slate-200">v2.4.0</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Platform yapılandırması ve sistem sağlığı</p>
      </div>
      
      <button 
        @click="saveSettings" 
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold transition shadow-sm shadow-indigo-200 flex items-center gap-2"
        :disabled="saving"
      >
        <span v-if="saving" class="animate-spin">↻</span>
        <span v-else>💾</span>
        {{ saving ? 'Kaydediliyor...' : 'Değişiklikleri Kaydet' }}
      </button>
    </div>

    <!-- Main Content -->
    <div class="flex flex-1 overflow-hidden">
      
      <!-- Left Panel: Categories -->
      <div class="w-64 bg-white border-r border-slate-200 flex flex-col overflow-y-auto">
        <div class="p-4 space-y-1">
          <button 
            v-for="cat in categories" 
            :key="cat.id"
            @click="activeCategoryId = cat.id"
            class="w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 transition-all duration-200"
            :class="activeCategoryId === cat.id ? 'bg-indigo-50 text-indigo-700 font-bold ring-1 ring-indigo-200' : 'text-slate-600 hover:bg-slate-50'"
          >
            <span class="text-xl">{{ cat.icon }}</span>
            <div>
              <div class="text-sm">{{ cat.label }}</div>
              <div class="text-[10px] opacity-70 font-normal truncate max-w-[120px]">{{ cat.description }}</div>
            </div>
          </button>
        </div>
      </div>

      <!-- Middle Panel: Form -->
      <div class="flex-1 overflow-y-auto p-8 bg-slate-50">
        <div class="max-w-3xl mx-auto space-y-6">
          
          <!-- General Settings -->
          <div v-if="activeCategoryId === 'general'" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h2 class="text-lg font-bold text-slate-800 mb-6">🏢 Site Kimliği</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Site Adı</label>
                  <input v-model="settings.general.site_name" type="text" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Site URL</label>
                  <input v-model="settings.general.site_url" type="url" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div class="col-span-2 space-y-2">
                  <label class="text-sm font-medium text-slate-700">Açıklama (Meta Description)</label>
                  <textarea v-model="settings.general.site_description" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"></textarea>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h2 class="text-lg font-bold text-slate-800 mb-6">📞 İletişim Bilgileri</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Destek E-posta</label>
                  <input v-model="settings.general.support_email" type="email" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Destek Telefon</label>
                  <input v-model="settings.general.support_phone" type="tel" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h2 class="text-lg font-bold text-slate-800 mb-6">🖼️ Görseller</h2>
              <div class="flex gap-8">
                <div class="space-y-3">
                  <label class="text-sm font-medium text-slate-700">Logo</label>
                  <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:bg-slate-50 transition cursor-pointer" @click="uploadLogo">
                    <img :src="settings.general.logo_url" class="h-12 mx-auto mb-2" alt="Logo">
                    <span class="text-xs text-slate-500">Değiştirmek için tıklayın</span>
                  </div>
                </div>
                <div class="space-y-3">
                  <label class="text-sm font-medium text-slate-700">Favicon</label>
                  <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:bg-slate-50 transition cursor-pointer w-32" @click="uploadLogo">
                    <img :src="settings.general.favicon_url" class="h-8 w-8 mx-auto mb-2" alt="Favicon">
                    <span class="text-xs text-slate-500">Değiştir</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Security Settings -->
          <div v-if="activeCategoryId === 'security'" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h2 class="text-lg font-bold text-slate-800 mb-6">🛡️ Erişim Güvenliği</h2>
              <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                  <div>
                    <div class="font-bold text-slate-700">Yönetici 2FA Zorunluluğu</div>
                    <div class="text-xs text-slate-500">Tüm admin kullanıcıları için iki faktörlü doğrulamayı zorunlu kıl.</div>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="settings.security.force_2fa_admin" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                  </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                  <div>
                    <div class="font-bold text-red-700">Debug Modu</div>
                    <div class="text-xs text-red-500">Hata ayıklama modunu açar. Canlı ortamda kapalı olmalıdır!</div>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="settings.security.debug_mode" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Settings -->
          <div v-if="activeCategoryId === 'payment'" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h2 class="text-lg font-bold text-slate-800 mb-6">💰 Finansal Ayarlar</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Para Birimi</label>
                  <select v-model="settings.payment.currency" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                    <option value="TRY">Türk Lirası (₺)</option>
                    <option value="USD">Amerikan Doları ($)</option>
                    <option value="EUR">Euro (€)</option>
                  </select>
                </div>
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Varsayılan KDV Oranı (%)</label>
                  <input v-model.number="settings.payment.vat_rate" type="number" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div class="space-y-2">
                  <label class="text-sm font-medium text-slate-700">Min. Sipariş Tutarı</label>
                  <input v-model.number="settings.payment.min_order_amount" type="number" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
              </div>
            </div>
          </div>

          <!-- Other tabs placeholders -->
          <div v-if="['shipping', 'email', 'integrations'].includes(activeCategoryId)" class="bg-white rounded-2xl border border-slate-200 p-10 shadow-sm text-center">
            <div class="text-4xl mb-4">🚧</div>
            <h3 class="text-lg font-bold text-slate-800">Yapım Aşamasında</h3>
            <p class="text-slate-500">Bu ayar grubu için modern arayüz hazırlanıyor.</p>
          </div>

        </div>
      </div>

      <!-- Right Panel: AI Advisor -->
      <div class="w-80 bg-white border-l border-slate-200 p-6 overflow-y-auto">
        <h3 class="font-bold text-slate-800 mb-4">Sistem Sağlığı</h3>
        <SettingsAIAdvisor />
      </div>

    </div>
  </div>
</template>
