<template>
  <section class="py-12 bg-gray-100">
    <div class="max-w-5xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-6">Neden Sportoonline?</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded shadow">
          <h3 class="text-xl font-semibold mb-2">🛍️ Ürün Listeleme</h3>
          <p class="text-gray-600">Satıcı panelinden kolayca ürün ekleyin, varyant tanımlayın ve stok yönetin.</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
          <h3 class="text-xl font-semibold mb-2">💳 Güvenli Ödeme</h3>
          <p class="text-gray-600">Stripe veya iyzico ile entegre ödeme altyapısı. Siparişler otomatik oluşur.</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
          <h3 class="text-xl font-semibold mb-2 inline-flex items-center gap-2"><IconStar cls="w-5 h-5 text-yellow-400" :filled="true" /> Yorum & Puanlama</h3>
          <p class="text-gray-600">Alıcılar sipariş sonrası yorum bırakabilir, satıcı puanı oluşur.</p>
        </div>
      </div>

      <router-link
        to="/demo"
        class="mt-10 inline-block bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition"
      >
        Platformu Dene
      </router-link>
    </div>
  </section>
</template>

<script setup>
import IconStar from '@/components/icons/IconStar.vue'
</script>