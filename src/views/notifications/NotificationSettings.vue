<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
    <div class="max-w-2xl mx-auto px-4">
      <!-- Header -->
      <div class="flex items-center gap-4 mb-8">
        <router-link 
          to="/notifications"
          class="p-2 bg-white rounded-xl border border-slate-200 hover:bg-slate-50 transition"
        >
          ← 
        </router-link>
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Bildirim Ayarları</h1>
          <p class="text-slate-500">Hangi bildirimleri almak istediğinizi özelleştirin</p>
        </div>
      </div>

      <!-- Notification Channels -->
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100">
          <h2 class="font-bold text-slate-900">Bildirim Kanalları</h2>
          <p class="text-sm text-slate-500">Bildirimleri nasıl almak istediğinizi seçin</p>
        </div>
        
        <div class="divide-y divide-slate-100">
          <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-xl">📱</div>
              <div>
                <div class="font-medium text-slate-900">Push Bildirimleri</div>
                <div class="text-sm text-slate-500">Tarayıcı bildirimleri alın</div>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="settings.push" class="sr-only peer">
              <div class="w-11 h-6 bg-slate-200 peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>
          
          <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-xl">✉️</div>
              <div>
                <div class="font-medium text-slate-900">E-posta Bildirimleri</div>
                <div class="text-sm text-slate-500">Önemli güncellemeleri e-posta ile alın</div>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="settings.email" class="sr-only peer">
              <div class="w-11 h-6 bg-slate-200 peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>
          
          <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-xl">📲</div>
              <div>
                <div class="font-medium text-slate-900">SMS Bildirimleri</div>
                <div class="text-sm text-slate-500">Kritik güncellemeleri SMS ile alın</div>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="settings.sms" class="sr-only peer">
              <div class="w-11 h-6 bg-slate-200 peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>
        </div>
      </div>

      <!-- Notification Categories -->
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100">
          <h2 class="font-bold text-slate-900">Bildirim Kategorileri</h2>
          <p class="text-sm text-slate-500">Hangi tür bildirimleri almak istediğinizi seçin</p>
        </div>
        
        <div class="divide-y divide-slate-100">
          <div v-for="category in categories" :key="category.id" class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4">
              <div 
                class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                :class="category.bgClass"
              >
                {{ category.icon }}
              </div>
              <div>
                <div class="font-medium text-slate-900">{{ category.name }}</div>
                <div class="text-sm text-slate-500">{{ category.description }}</div>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="settings.categories[category.id]" class="sr-only peer">
              <div class="w-11 h-6 bg-slate-200 peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>
        </div>
      </div>

      <!-- Email Preferences -->
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100">
          <h2 class="font-bold text-slate-900">E-posta Sıklığı</h2>
          <p class="text-sm text-slate-500">E-posta özetlerini ne sıklıkta almak istersiniz?</p>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-3 gap-3">
            <button 
              v-for="freq in frequencies"
              :key="freq.id"
              @click="settings.emailFrequency = freq.id"
              class="p-4 rounded-xl border-2 transition text-center"
              :class="settings.emailFrequency === freq.id 
                ? 'border-indigo-600 bg-indigo-50' 
                : 'border-slate-200 hover:border-slate-300'"
            >
              <div class="text-2xl mb-2">{{ freq.icon }}</div>
              <div class="font-medium text-slate-900">{{ freq.name }}</div>
              <div class="text-xs text-slate-500">{{ freq.desc }}</div>
            </button>
          </div>
        </div>
      </div>

      <!-- Quiet Hours -->
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
          <div>
            <h2 class="font-bold text-slate-900">Sessiz Saatler</h2>
            <p class="text-sm text-slate-500">Bu saatler arasında bildirim almayın</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" v-model="settings.quietHours.enabled" class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
          </label>
        </div>
        
        <div v-if="settings.quietHours.enabled" class="p-6">
          <div class="flex items-center gap-4">
            <div class="flex-1">
              <label class="block text-sm font-medium text-slate-700 mb-2">Başlangıç</label>
              <input 
                type="time" 
                v-model="settings.quietHours.start"
                class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              >
            </div>
            <div class="text-slate-400 pt-6">→</div>
            <div class="flex-1">
              <label class="block text-sm font-medium text-slate-700 mb-2">Bitiş</label>
              <input 
                type="time" 
                v-model="settings.quietHours.end"
                class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="flex gap-3">
        <button 
          @click="saveSettings"
          :disabled="saving"
          class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition disabled:opacity-50"
        >
          {{ saving ? 'Kaydediliyor...' : '💾 Ayarları Kaydet' }}
        </button>
        <button 
          @click="resetToDefaults"
          class="px-6 py-3 bg-white border border-slate-200 rounded-xl font-medium text-slate-700 hover:bg-slate-50 transition"
        >
          Varsayılana Dön
        </button>
      </div>

      <!-- Success Message -->
      <Transition name="fade">
        <div v-if="showSuccess" class="fixed bottom-8 right-8 px-6 py-3 bg-green-600 text-white rounded-xl shadow-lg flex items-center gap-2">
          ✓ Ayarlar kaydedildi
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import axios from 'axios'

interface NotificationSettings {
  push: boolean
  email: boolean
  sms: boolean
  emailFrequency: 'instant' | 'daily' | 'weekly'
  categories: Record<string, boolean>
  quietHours: {
    enabled: boolean
    start: string
    end: string
  }
}

const saving = ref(false)
const showSuccess = ref(false)

const settings = reactive<NotificationSettings>({
  push: true,
  email: true,
  sms: false,
  emailFrequency: 'instant',
  categories: {
    orders: true,
    payments: true,
    messages: true,
    promos: true,
    stock: true,
    reviews: true,
    system: true
  },
  quietHours: {
    enabled: false,
    start: '22:00',
    end: '08:00'
  }
})

const categories = [
  { id: 'orders', name: 'Sipariş Bildirimleri', description: 'Sipariş durumu güncellemeleri', icon: '📦', bgClass: 'bg-blue-100' },
  { id: 'payments', name: 'Ödeme Bildirimleri', description: 'Ödeme onayları ve fatura bildirimleri', icon: '💳', bgClass: 'bg-emerald-100' },
  { id: 'messages', name: 'Mesaj Bildirimleri', description: 'Satıcı ve destek mesajları', icon: '💬', bgClass: 'bg-green-100' },
  { id: 'promos', name: 'Kampanya Bildirimleri', description: 'İndirimler ve özel teklifler', icon: '🏷️', bgClass: 'bg-orange-100' },
  { id: 'stock', name: 'Stok Bildirimleri', description: 'Takip ettiğiniz ürünler stoğa girdiğinde', icon: '📊', bgClass: 'bg-cyan-100' },
  { id: 'reviews', name: 'Değerlendirme Bildirimleri', description: 'Yorum hatırlatıcıları', icon: '⭐', bgClass: 'bg-yellow-100' },
  { id: 'system', name: 'Sistem Bildirimleri', description: 'Güvenlik ve hesap güncellemeleri', icon: '⚙️', bgClass: 'bg-slate-100' },
]

const frequencies = [
  { id: 'instant', name: 'Anında', desc: 'Her bildirimde', icon: '⚡' },
  { id: 'daily', name: 'Günlük', desc: 'Günde bir özet', icon: '📅' },
  { id: 'weekly', name: 'Haftalık', desc: 'Haftada bir özet', icon: '📆' },
]

const saveSettings = async () => {
  saving.value = true
  try {
    await axios.put('/api/notification-preferences', {
      push: settings.push,
      email: settings.email,
      sms: settings.sms,
      emailFrequency: settings.emailFrequency,
      categories: settings.categories,
      quietHours: settings.quietHours
    })
    showSuccess.value = true
    setTimeout(() => showSuccess.value = false, 3000)
  } catch (error) {
    console.error('Settings save failed:', error)
  } finally {
    saving.value = false
  }
}

const resetToDefaults = () => {
  settings.push = true
  settings.email = true
  settings.sms = false
  settings.emailFrequency = 'instant'
  Object.keys(settings.categories).forEach(key => {
    settings.categories[key] = true
  })
  settings.quietHours.enabled = false
  settings.quietHours.start = '22:00'
  settings.quietHours.end = '08:00'
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
