<template>
  <div class="bg-white text-gray-800">
    <section class="text-center py-16">
      <h1 class="text-4xl font-bold">Her satıcı kendi vitriniyle burada.</h1>
      <p class="mt-4 text-lg">Ürününü ekle, satışa başla. Komisyon modeliyle büyü.</p>

      <div class="mt-6 flex flex-col md:flex-row justify-center gap-4">
        <router-link
          to="/register?seller=true"
          class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition"
        >
          Hemen Mağazanı Aç
        </router-link>

        <router-link
          to="/demo"
          class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition"
        >
          Platformu Dene
        </router-link>
      </div>
    </section>

    <FeatureSection />
    <PricingSection />
    <DemoAccess />
    <TrustSection />
    <Footer />
  </div>
</template>

<script setup>
import FeatureSection from './FeatureSection.vue'
import PricingSection from './PricingSection.vue'
import DemoAccess from './DemoAccess.vue'
import TrustSection from './TrustSection.vue'
import Footer from './Footer.vue'

// 🧠 SEO Başlıkları ve Meta Etiketleri
import { useHead } from '@vueuse/head'

useHead({
  title: 'Sportoonline – Çok Satıcılı E-Ticaret Platformu',
  meta: [
    {
      name: 'description',
      content: 'Satıcılar için modüler, hızlı ve güvenli C2C pazaryeri. Hemen mağazanı aç.'
    },
    {
      name: 'keywords',
      content: 'C2C, e-ticaret, çok satıcılı, pazaryeri, ürün satışı, yorum sistemi'
    },
    {
      property: 'og:title',
      content: 'Sportoonline – Her Satıcı Kendi Vitriniyle Burada'
    },
    {
      property: 'og:url',
      content: 'https://sportoonline.com'
    },
    {
      property: 'og:image',
      content: 'https://sportoonline.com/og-image.png'
    }
  ]
})
</script>