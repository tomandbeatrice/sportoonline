<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()
const toast = useToast()

const password = ref('')
const passwordConfirmation = ref('')
const success = ref(false)
const errors = ref<Record<string, string>>({})

const token = route.query.token as string

const validatePassword = () => {
  errors.value = {}
  let isValid = true

  if (!password.value || password.value.length === 0) {
    errors.value.password = 'Şifre zorunludur'
    isValid = false
  } else if (password.value.length < 8) {
    errors.value.password = 'Şifre en az 8 karakter olmalıdır'
    isValid = false
  } else if (!/[A-Z]/.test(password.value)) {
    errors.value.password = 'Şifre en az bir büyük harf içermelidir'
    isValid = false
  } else if (!/[a-z]/.test(password.value)) {
    errors.value.password = 'Şifre en az bir küçük harf içermelidir'
    isValid = false
  } else if (!/[0-9]/.test(password.value)) {
    errors.value.password = 'Şifre en az bir rakam içermelidir'
    isValid = false
  } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password.value)) {
    errors.value.password = 'Şifre en az bir özel karakter içermelidir'
    isValid = false
  }

  if (!passwordConfirmation.value) {
    errors.value.passwordConfirmation = 'Şifre tekrarı zorunludur'
    isValid = false
  } else if (password.value !== passwordConfirmation.value) {
    errors.value.passwordConfirmation = 'Şifreler eşleşmiyor'
    isValid = false
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validatePassword()) {
    return
  }

  const result = await authStore.resetPassword(token, password.value, passwordConfirmation.value)
  if (result) {
    success.value = true
    toast.success('Şifreniz başarıyla güncellendi!')
    setTimeout(() => router.push('/login'), 3000)
  } else {
    toast.error(authStore.error || 'Şifre sıfırlama başarısız.')
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
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                :class="{ 'border-red-500': errors.password }"
                placeholder="En az 8 karakter"
              />
              <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
              <div class="mt-2 text-xs text-slate-500 space-y-1">
                <p>✓ En az 8 karakter</p>
                <p>✓ Büyük ve küçük harf</p>
                <p>✓ En az bir rakam</p>
                <p>✓ En az bir özel karakter (!@#$%)</p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Şifre Tekrar</label>
              <input 
                v-model="passwordConfirmation"
                type="password" 
                required
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                :class="{ 'border-red-500': errors.passwordConfirmation }"
                placeholder="Şifrenizi tekrar girin"
              />
              <p v-if="errors.passwordConfirmation" class="text-red-500 text-sm mt-1">{{ errors.passwordConfirmation }}</p>
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
