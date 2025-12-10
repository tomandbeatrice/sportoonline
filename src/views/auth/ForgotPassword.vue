<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const email = ref('')
const submitted = ref(false)

const handleSubmit = async () => {
  const success = await authStore.forgotPassword(email.value)
  if (success) {
    submitted.value = true
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 px-4">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-xl p-8">
        <!-- Logo -->
        <div class="text-center mb-8">
          <router-link to="/" class="inline-block">
            <h1 class="text-3xl font-bold text-slate-900">Sporto<span class="text-orange-500">Online</span></h1>
          </router-link>
        </div>

        <!-- Success Message -->
        <div v-if="submitted" class="text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl">✉️</span>
          </div>
          <h2 class="text-xl font-bold text-slate-900 mb-2">E-posta Gönderildi!</h2>
          <p class="text-slate-600 mb-6">
            Şifre sıfırlama bağlantısı <strong>{{ email }}</strong> adresine gönderildi.
            Lütfen e-postanızı kontrol edin.
          </p>
          <router-link 
            to="/login" 
            class="inline-block px-6 py-3 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transition"
          >
            Giriş Sayfasına Dön
          </router-link>
        </div>

        <!-- Form -->
        <div v-else>
          <h2 class="text-2xl font-bold text-slate-900 text-center mb-2">Şifremi Unuttum</h2>
          <p class="text-slate-500 text-center mb-8">
            E-posta adresinizi girin, şifre sıfırlama bağlantısı gönderelim.
          </p>

          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">E-posta Adresi</label>
              <input 
                v-model="email"
                type="email" 
                required
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                placeholder="ornek@email.com"
              />
            </div>

            <div v-if="authStore.error" class="p-4 bg-red-50 text-red-700 rounded-xl text-sm">
              {{ authStore.error }}
            </div>

            <button 
              type="submit"
              :disabled="authStore.loading"
              class="w-full py-3 bg-orange-600 text-white rounded-xl font-bold hover:bg-orange-700 transition disabled:opacity-50"
            >
              <span v-if="authStore.loading">Gönderiliyor...</span>
              <span v-else>Sıfırlama Bağlantısı Gönder</span>
            </button>
          </form>

          <p class="mt-6 text-center text-slate-500">
            Şifrenizi hatırladınız mı?
            <router-link to="/login" class="text-orange-600 font-semibold hover:underline">
              Giriş Yap
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
