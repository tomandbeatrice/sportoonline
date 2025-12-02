<template>
  <section class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-6">Fiyatlandırma</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <PricingCard
          v-for="plan in plans"
          :key="plan.name"
          :name="plan.name"
          :description="plan.description"
          :price="plan.price"
          :link="plan.link"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import PricingCard from './PricingCard.vue'

const plans = [
  {
    name: 'Free',
    description: '5 ürün, temel panel, yorum sistemi',
    price: '₺0',
    link: '/register?plan=free'
  },
  {
    name: 'Pro',
    description: 'Sınırsız ürün, varyant, export paneli',
    price: '₺149/ay',
    link: '/register?plan=pro'
  },
  {
    name: 'Enterprise',
    description: 'API entegrasyonu, özel destek',
    price: 'Özel teklif',
    link: '/contact'
  }
]
</script>