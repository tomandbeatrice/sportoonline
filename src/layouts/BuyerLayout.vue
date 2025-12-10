<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
          <router-link to="/" class="text-2xl font-bold text-slate-900">SportoOnline</router-link>
          <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">ALICI</span>
        </div>
        <nav class="hidden md:flex items-center gap-2">
          <router-link to="/" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">🏠 Ana Sayfa</router-link>
          <router-link to="/products" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">🛍️ Ürünler</router-link>
          <router-link to="/cart" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">🛒 Sepet</router-link>
        </nav>
        <div class="flex items-center gap-3">
          <button class="relative p-2 text-slate-600 hover:bg-slate-100 rounded-lg">🔔</button>
          <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
            <slot name="user" />
          </div>
        </div>
      </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <router-view />
    </main>
  </div>
</template>
