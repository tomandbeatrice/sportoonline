<template>
  <section class="relative overflow-hidden rounded-3xl bg-market-gradient p-8 text-white shadow-2xl">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
      <div class="space-y-4">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/70">Mobil deneyim</p>
        <h3 class="text-3xl font-black leading-tight">Sportoonline uygulamasını indir, kampanyalara ilk sen eriş</h3>
        <p class="text-white/80 text-sm max-w-2xl">Bildirimi aç, satıcı sohbetine bağlan, sipariş akışını canlı takip et. Tüm pazaryeri cockpit'i cebinde.</p>
        <div class="flex flex-wrap gap-3">
          <button class="rounded-2xl bg-white px-5 py-3 font-semibold text-slate-900 shadow-lg">App Store</button>
          <button class="rounded-2xl border border-white/70 px-5 py-3 font-semibold text-white">Google Play</button>
        </div>
      </div>
      <div class="relative mt-6 lg:mt-0">
        <div class="blur-circle -left-10 top-0 h-32 w-32 bg-white/40"></div>
        <img src="https://dummyimage.com/260x520/0f172a/ffffff&text=Sportoonline" alt="App Mockup" class="relative rounded-[32px] border border-white/30 shadow-2xl">
      </div>
    </div>
  </section>
  <div class="fixed inset-x-4 bottom-4 z-40 md:hidden">
    <Alert title="Uygulamayı indir" variant="orange">
      Kampanya push’larını kaçırma.
      <template #action>
        <button class="rounded-2xl bg-slate-900 px-4 py-2 text-[0.65rem] font-black uppercase tracking-[0.35em] text-white">
          İndir
        </button>
      </template>
    </Alert>
  </div>
</template>

<script setup>
import { Alert } from '@/components/ui/alert'
</script>
