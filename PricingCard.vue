<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div v-for="plan in plans" :key="plan.name" class="border p-6 rounded shadow">
      <h2 class="text-xl font-bold">{{ plan.name }}</h2>
      <p class="mt-2 text-gray-600">{{ plan.description }}</p>
      <p class="mt-4 text-2xl font-semibold">{{ plan.price }}</p>
      <router-link :to="plan.link" class="mt-6 inline-block bg-green-600 text-white px-4 py-2 rounded">
        Seç
      </router-link>
    </div>
  </div>
</template>

<script setup>
const plans = [
  { name: 'Free', description: '5 ürün, temel panel, yorum sistemi', price: '₺0', link: '/register?plan=free' },
  { name: 'Pro', description: 'Sınırsız ürün, varyant, export paneli', price: '₺149/ay', link: '/register?plan=pro' },
  { name: 'Enterprise', description: 'API entegrasyonu, özel destek', price: 'Özel teklif', link: '/contact' }
]
</script>