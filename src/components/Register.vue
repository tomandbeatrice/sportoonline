<template>
  <section class="py-16 bg-white text-center">
    <div class="max-w-xl mx-auto px-4">
      <h1 class="text-3xl font-bold mb-4">Kayıt Ol</h1>
      <p class="text-gray-600 mb-6">
        Sportoonline’da alışverişe başla veya mağazanı aç. Rolünü seç, avantajları yakala.
      </p>

      <div v-if="isValidCode" class="mb-4 bg-green-100 text-green-800 p-4 rounded">
        🎉 Davet kodun geçerli! %0 komisyon avantajı aktif.
      </div>

      <form @submit.prevent="submit">
        <input v-model="form.name" type="text" placeholder="Ad Soyad" class="input" required />
        <input v-model="form.email" type="email" placeholder="E-posta" class="input" required />
        <input v-model="form.password" type="password" placeholder="Şifre" class="input" required />

        <select v-model="form.role" class="input">
          <option value="buyer">Alıcı</option>
          <option value="seller">Satıcı</option>
        </select>

        <input type="hidden" :value="inviteCode" name="code" />

        <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
          Kayıt Ol
        </button>
      </form>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const inviteCode = ref('')
const isValidCode = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'buyer'
})

onMounted(() => {
  inviteCode.value = route.query.code || ''
  isValidCode.value = inviteCode.value === 'SPORTOON100'
})

const submit = async () => {
  try {
    await axios.post('/api/register', {
      ...form.value,
      code: inviteCode.value
    })
    alert('Kayıt başarılı!')
    router.push(form.value.role === 'seller' ? '/seller/dashboard' : '/buyer/home')
  } catch (error) {
    console.error('Kayıt hatası:', error)
    alert('Kayıt sırasında bir hata oluştu.')
  }
}
</script>

<style scoped>
.input {
  display: block;
  width: 100%;
  padding: 12px;
  margin-bottom: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
}
</style>