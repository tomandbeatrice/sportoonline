<template>
  <section class="rounded-3xl bg-gradient-to-r from-emerald-500 to-teal-500 p-8 text-white shadow-2xl">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
      <div class="space-y-3">
        <p class="text-sm uppercase tracking-[0.35em] text-white/70">Satıcı Merkezi</p>
        <h2 class="text-3xl font-black leading-tight">Mağazanı sportoonline'da dakikalar içinde aç</h2>
        <p class="text-sm text-white/80">Anlık performans panelleri, kampanya önerileri ve güvenli ödeme altyapısıyla satışlarını büyüt.</p>
      </div>
      <div class="flex flex-wrap gap-4">
        <RouterLink
          to="/apply-seller"
          class="rounded-2xl bg-white px-6 py-3 font-semibold text-emerald-600 shadow-lg"
        >
          Satıcı Başvurusu
        </RouterLink>
        <RouterLink
          to="/seller/dashboard"
          class="rounded-2xl border border-white/60 px-6 py-3 font-semibold text-white"
        >
          Paneli İncele
        </RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>
