<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const password = ref('')
const passwordConfirmation = ref('')
const success = ref(false)

const token = route.query.token as string

const handleSubmit = async () => {
  if (password.value !== passwordConfirmation.value) {
    authStore.error = 'Şifreler eşleşmiyor.'
    return
  }

  const result = await authStore.resetPassword(token, password.value, passwordConfirmation.value)
  if (result) {
    success.value = true
    setTimeout(() => router.push('/login'), 3000)
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
        <div v-if="success" class="text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl">✅</span>
          </div>
          <h2 class="text-xl font-bold text-slate-900 mb-2">Şifre Değiştirildi!</h2>
          <p class="text-slate-600 mb-6">
            Şifreniz başarıyla güncellendi. Giriş sayfasına yönlendiriliyorsunuz...
          </p>
        </div>

        <!-- No Token -->
        <div v-else-if="!token" class="text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl">⚠️</span>
          </div>
          <h2 class="text-xl font-bold text-slate-900 mb-2">Geçersiz Bağlantı</h2>
          <p class="text-slate-600 mb-6">
            Bu şifre sıfırlama bağlantısı geçersiz veya süresi dolmuş.
          </p>
          <router-link 
            to="/forgot-password" 
            class="inline-block px-6 py-3 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transition"
          >
            Yeni Bağlantı İste
          </router-link>
        </div>

        <!-- Form -->
        <div v-else>
          <h2 class="text-2xl font-bold text-slate-900 text-center mb-2">Yeni Şifre Belirle</h2>
          <p class="text-slate-500 text-center mb-8">
            Yeni şifrenizi girin.
          </p>

          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Yeni Şifre</label>
              <input 
                v-model="password"
                type="password" 
                required
                minlength="6"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                placeholder="En az 6 karakter"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Şifre Tekrar</label>
              <input 
                v-model="passwordConfirmation"
                type="password" 
                required
                minlength="6"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                placeholder="Şifrenizi tekrar girin"
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
              <span v-if="authStore.loading">Değiştiriliyor...</span>
              <span v-else>Şifreyi Değiştir</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
