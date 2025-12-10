<template>
  <footer class="mt-16 bg-slate-950 text-white">
    <div class="parallax-background">
      <div class="mx-auto max-w-6xl px-4 py-16">
        <div class="grid gap-10 lg:grid-cols-[1.2fr,0.8fr]">
          <div class="glass-card floating-shadow p-8 text-slate-900">
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-400">Bülten</p>
            <h3 class="mt-2 text-2xl font-black text-slate-900">Haftalık kampanya radarını kaçırma</h3>
            <p class="mt-2 text-sm text-slate-500">Sadece satıcı performansı, müşteri fırsatları ve ürün trendleri özetini paylaşıyoruz.</p>
            <form class="mt-6 flex flex-col gap-3 sm:flex-row" @submit.prevent>
              <input
                type="email"
                required
                placeholder="E-posta adresin"
                class="flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600 focus:border-blue-500 focus:outline-none"
              />
              <button class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-lg">Abone ol</button>
            </form>
            <div class="mt-6 flex flex-wrap gap-4 text-xs font-semibold text-slate-400">
              <span class="inline-flex items-center gap-2"><BadgeIcon name="check" cls="w-4 h-4 text-emerald-500" />Spam yok</span>
              <span class="inline-flex items-center gap-2"><BadgeIcon name="check" cls="w-4 h-4 text-emerald-500" />Satıcı içgörüleri</span>
              <span class="inline-flex items-center gap-2"><BadgeIcon name="check" cls="w-4 h-4 text-emerald-500" />Haftalık trend raporu</span>
            </div>
          </div>

          <div class="rounded-3xl border border-white/10 bg-white/5 p-8 text-white backdrop-blur-xl">
            <h4 class="text-sm font-semibold uppercase tracking-[0.35em] text-white/70">Destek</h4>
            <ul class="mt-6 space-y-3 text-sm text-white/80">
              <li class="flex items-center justify-between">
                <router-link class="hover:text-white" to="/about">Hakkımızda</router-link>
                <span class="text-white/50">Kimliğimiz</span>
              </li>
              <li class="flex items-center justify-between">
                <router-link class="hover:text-white" to="/contact">İletişim & Destek</router-link>
                <span class="text-white/50">7/24</span>
              </li>
              <li class="flex items-center justify-between">
                <router-link class="hover:text-white" to="/faq">SSS</router-link>
                <span class="text-white/50">Güncel</span>
              </li>
              <li class="flex items-center justify-between">
                <router-link class="hover:text-white" to="/seller/guide">Satıcı rehberi</router-link>
                <span class="text-white/50">PDF</span>
              </li>
            </ul>
            <div class="mt-8 grid grid-cols-2 gap-4 text-center text-xs">
              <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                <p class="text-2xl font-black">45K+</p>
                <p class="text-white/60">Aktif satıcı</p>
              </div>
              <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                <p class="text-2xl font-black">98%</p>
                <p class="text-white/60">Mutlu müşteri</p>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-14 grid gap-8 border-t border-white/10 pt-10 text-sm text-white/70 lg:grid-cols-3">
          <div>
            <p class="text-xs uppercase tracking-[0.35em] text-white/50">Kimlik</p>
            <p class="mt-2 text-lg font-semibold text-white">sportoonline MultiPrice</p>
            <p class="text-white/50">Lifestyle Hub • Veri odaklı satıcı paneli • Yapay zekâ kampanyaları</p>
          </div>
          <div class="flex items-center gap-4">
            <span class="text-xs uppercase tracking-[0.35em] text-white/50">Ödeme</span>
            <div class="flex gap-2">
              <span class="rounded-xl border border-white/20 px-3 py-1">Mastercard</span>
              <span class="rounded-xl border border-white/20 px-3 py-1">Visa</span>
              <span class="rounded-xl border border-white/20 px-3 py-1">Troy</span>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 text-white/70">
            <a href="#" aria-label="Instagram" class="hover:text-white">IG</a>
            <a href="#" aria-label="LinkedIn" class="hover:text-white">IN</a>
            <a href="#" aria-label="YouTube" class="hover:text-white">YT</a>
          </div>
        </div>

        <div class="mt-8 border-t border-white/10 pt-6 text-center text-xs text-white/50">
          &copy; {{ currentYear }} Sportoonline Dijital Hizmetler AŞ. Tüm hakları saklıdır.
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const currentYear = new Date().getFullYear()
</script>
