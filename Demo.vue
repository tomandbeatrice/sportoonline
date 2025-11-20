<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Demo Ortamı – Satıcı & Alıcı Testi</h1>

    <section class="mb-8">
      <h2 class="text-xl font-semibold mb-2">Demo Ürünler</h2>
      <DemoProductList />
    </section>

    <section class="mb-8">
      <h2 class="text-xl font-semibold mb-2">Demo Sipariş</h2>
      <DemoCheckout />
    </section>

    <section>
      <h2 class="text-xl font-semibold mb-2">Demo Yorumlar</h2>
      <DemoReview />
    </section>
  </div>
</template>

<script setup>
import DemoProductList from '@/components/demo/DemoProductList.vue'
import DemoCheckout from '@/components/demo/DemoCheckout.vue'
import DemoReview from '@/components/demo/DemoReview.vue'
</script>