<template>
  <section class="p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold text-indigo-700">📨 Davet Kodları</h2>

    <div class="mt-4">
      <button @click="generateCode" class="px-4 py-2 bg-indigo-600 text-white rounded">
        Kod Oluştur
      </button>
      <p v-if="code" class="mt-2 text-sm text-gray-700">Oluşturulan Kod: <strong>{{ code }}</strong></p>
    </div>

    <div class="mt-6">
      <input v-model="redeemCode" placeholder="Davet Kodunu Gir" class="border px-3 py-2 rounded w-full" />
      <button @click="redeem" class="mt-2 px-4 py-2 bg-green-600 text-white rounded">
        Kodu Kullan
      </button>
      <p v-if="redeemStatus" class="mt-2 text-sm text-green-700">{{ redeemStatus }}</p>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const code = ref(null)
const redeemCode = ref('')
const redeemStatus = ref('')

async function generateCode() {
  try {
    const res = await axios.post('/api/invitation/store')
    code.value = res.data.code
  } catch (err) {
    console.error('Kod oluşturulamadı:', err)
  }
}

async function redeem() {
  try {
    const res = await axios.post('/api/invitation/redeem', { code: redeemCode.value })
    redeemStatus.value = 'Kod başarıyla kullanıldı ✅'
  } catch (err) {
    redeemStatus.value = 'Kod geçersiz veya daha önce kullanılmış ❌'
  }
}
</script>