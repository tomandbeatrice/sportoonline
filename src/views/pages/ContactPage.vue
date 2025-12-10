<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-green-600 via-emerald-600 to-teal-700 text-white py-16">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-black mb-4">İletişim & Destek</h1>
        <p class="text-xl text-green-100">7/24 yanınızdayız</p>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Contact Cards -->
        <div class="lg:col-span-1 space-y-6">
          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl mb-4">📞</div>
            <h3 class="font-bold text-slate-800 mb-2">Telefon</h3>
            <p class="text-green-600 font-semibold text-lg">0850 123 45 67</p>
            <p class="text-sm text-slate-500 mt-1">7/24 Müşteri Hizmetleri</p>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl mb-4">✉️</div>
            <h3 class="font-bold text-slate-800 mb-2">E-posta</h3>
            <p class="text-blue-600 font-semibold">destek@sportoonline.com</p>
            <p class="text-sm text-slate-500 mt-1">24 saat içinde yanıt</p>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl mb-4">💬</div>
            <h3 class="font-bold text-slate-800 mb-2">Canlı Destek</h3>
            <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
              Sohbeti Başlat
            </button>
            <p class="text-sm text-slate-500 mt-2">Ortalama yanıt: 2 dk</p>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-2xl mb-4">📍</div>
            <h3 class="font-bold text-slate-800 mb-2">Adres</h3>
            <p class="text-slate-600 text-sm">
              Maslak Mahallesi, Büyükdere Cad.<br>
              No: 123, Sarıyer / İstanbul
            </p>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Bize Yazın</h2>
            
            <form @submit.prevent="submitForm" class="space-y-6">
              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Adınız *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Adınız Soyadınız"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">E-posta *</label>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="ornek@email.com"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Konu *</label>
                <select
                  v-model="form.subject"
                  required
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">Konu seçin...</option>
                  <option value="order">Sipariş Sorunu</option>
                  <option value="payment">Ödeme Sorunu</option>
                  <option value="refund">İade & İptal</option>
                  <option value="seller">Satıcı Başvurusu</option>
                  <option value="technical">Teknik Destek</option>
                  <option value="suggestion">Öneri & Şikayet</option>
                  <option value="other">Diğer</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Sipariş Numarası (Varsa)</label>
                <input
                  v-model="form.orderNumber"
                  type="text"
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  placeholder="SP-2025XXXXX"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Mesajınız *</label>
                <textarea
                  v-model="form.message"
                  required
                  rows="5"
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                  placeholder="Mesajınızı buraya yazın..."
                ></textarea>
              </div>

              <div class="flex items-center gap-3">
                <input type="checkbox" v-model="form.privacy" id="privacy" class="w-4 h-4 text-green-600 rounded" required />
                <label for="privacy" class="text-sm text-slate-600">
                  <router-link to="/privacy" class="text-green-600 hover:underline">Gizlilik politikasını</router-link> okudum ve kabul ediyorum
                </label>
              </div>

              <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-bold text-lg transition-colors"
              >
                Gönder
              </button>
            </form>
          </div>

          <!-- FAQ Quick Links -->
          <div class="mt-8 bg-slate-100 rounded-2xl p-6">
            <h3 class="font-bold text-slate-800 mb-4">Sık Sorulan Sorular</h3>
            <div class="grid md:grid-cols-2 gap-3">
              <router-link to="/faq#shipping" class="bg-white px-4 py-3 rounded-xl text-sm text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
                📦 Kargo süreleri ne kadar?
              </router-link>
              <router-link to="/faq#return" class="bg-white px-4 py-3 rounded-xl text-sm text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
                🔄 Nasıl iade yapabilirim?
              </router-link>
              <router-link to="/faq#payment" class="bg-white px-4 py-3 rounded-xl text-sm text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
                💳 Hangi ödeme yöntemleri?
              </router-link>
              <router-link to="/faq#seller" class="bg-white px-4 py-3 rounded-xl text-sm text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
                🏪 Satıcı nasıl olurum?
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'

const form = reactive({
  name: '',
  email: '',
  subject: '',
  orderNumber: '',
  message: '',
  privacy: false
})

const submitForm = () => {
  console.log('Form submitted:', form)
  alert('Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağız.')
}
</script>
