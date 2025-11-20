<template>
  <div class="home-page bg-gray-50">
    <!-- Top Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
      <div class="container mx-auto px-4">
        <!-- Top Bar -->
        <div class="flex items-center justify-between py-2 border-b border-gray-100">
          <div class="flex items-center gap-6 text-sm text-gray-600">
            <a href="#" class="hover:text-blue-600">Satıcı Ol</a>
            <a href="#" class="hover:text-blue-600">Yardım & Destek</a>
            <a href="#" class="hover:text-blue-600">Mobil Uygulama</a>
          </div>
          <div class="flex items-center gap-4">
            <!-- Language Selector -->
            <div class="relative group">
              <button class="flex items-center gap-1 text-sm text-gray-600 hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                </svg>
                <span>TR</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div class="hidden group-hover:block absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-100 py-1">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                  <span class="w-5 h-5 rounded-full overflow-hidden flex items-center justify-center bg-red-500 text-white text-xs font-bold">TR</span>
                  Türkçe
                </a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                  <span class="w-5 h-5 rounded-full overflow-hidden flex items-center justify-center bg-blue-500 text-white text-xs font-bold">EN</span>
                  English
                </a>
              </div>
            </div>
            <!-- User Menu -->
            <div class="text-sm text-gray-600">
              <router-link to="/login" class="hover:text-blue-600">Giriş Yap</router-link>
              <span class="mx-2">|</span>
              <router-link to="/register" class="hover:text-blue-600">Kayıt Ol</router-link>
            </div>
          </div>
        </div>

        <!-- Main Navbar -->
        <div class="flex items-center justify-between py-4">
          <!-- Logo -->
          <router-link to="/" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-xl">S</span>
            </div>
            <span class="text-2xl font-bold text-gray-900">SportoOnline</span>
          </router-link>

          <!-- Search Bar -->
          <div class="flex-1 max-w-2xl mx-8">
            <div class="relative">
              <input
                v-model="searchQuery"
                @keyup.enter="performSearch"
                type="text"
                placeholder="Ürün, marka veya kategori ara..."
                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <button @click="performSearch" class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-4">
            <button class="relative hover:text-blue-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </button>
            <router-link to="/cart" class="relative hover:text-blue-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span v-if="cartStore.itemCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {{ cartStore.itemCount }}
              </span>
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Banners -->
    <section class="container mx-auto px-4 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Main Hero Banner -->
        <div class="lg:col-span-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 h-[500px]">
          <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=1200')] bg-cover bg-center opacity-20"></div>
          <div class="relative h-full flex flex-col justify-center p-12">
            <div class="inline-block mb-4">
              <span class="bg-white/10 backdrop-blur-md text-white px-4 py-2 rounded-full text-sm font-medium border border-white/20">
                Yeni Sezon
              </span>
            </div>
            <h1 class="text-5xl font-bold text-white mb-4 leading-tight">
              Profesyonel Spor<br/>Ekipmanları
            </h1>
            <p class="text-xl text-white/80 mb-8 max-w-md">
              Türkiye'nin lider spor pazaryerinde binlerce ürün ve güvenli alışveriş deneyimi
            </p>
            <div class="flex gap-4">
              <button @click="scrollToProducts" class="bg-blue-600 text-white px-8 py-4 rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl">
                Ürünleri Keşfet
              </button>
              <button class="bg-white/10 backdrop-blur-md text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/20 transition-all border border-white/20">
                Kampanyalar
              </button>
            </div>
          </div>
        </div>

        <!-- Side Cards -->
        <div class="lg:col-span-4 grid grid-rows-2 gap-6">
          <!-- Premium Card -->
          <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-400 to-orange-600 p-8 flex flex-col justify-between">
            <div>
              <div class="inline-block bg-black/10 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-white mb-3">
                VIP
              </div>
              <h3 class="text-2xl font-bold text-white mb-2">Premium Üyelik</h3>
              <p class="text-white/90 text-sm">Özel indirimler ve erken erişim</p>
            </div>
            <button class="mt-4 bg-white text-orange-600 px-6 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-all text-sm">
              Detayları Gör
            </button>
          </div>

          <!-- Express Delivery Card -->
          <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-600 p-8 flex flex-col justify-between">
            <div>
              <div class="inline-block bg-black/10 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-white mb-3">
                Express
              </div>
              <h3 class="text-2xl font-bold text-white mb-2">Hızlı Teslimat</h3>
              <p class="text-white/90 text-sm">200₺ üzeri siparişlerde ücretsiz</p>
            </div>
            <button class="mt-4 bg-white text-emerald-600 px-6 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-all text-sm">
              Şartları Gör
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Trust Badges -->
    <section class="container mx-auto px-4 py-6">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="flex items-center gap-3 bg-white rounded-xl p-4 border border-gray-100">
          <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <div class="font-semibold text-gray-900 text-sm">Güvenli Alışveriş</div>
            <div class="text-xs text-gray-500">SSL Sertifikalı</div>
          </div>
        </div>

        <div class="flex items-center gap-3 bg-white rounded-xl p-4 border border-gray-100">
          <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div>
            <div class="font-semibold text-gray-900 text-sm">Orijinal Ürün</div>
            <div class="text-xs text-gray-500">%100 Garantili</div>
          </div>
        </div>

        <div class="flex items-center gap-3 bg-white rounded-xl p-4 border border-gray-100">
          <div class="w-12 h-12 bg-purple-50 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
          </div>
          <div>
            <div class="font-semibold text-gray-900 text-sm">Kolay Ödeme</div>
            <div class="text-xs text-gray-500">Taksit Seçenekleri</div>
          </div>
        </div>

        <div class="flex items-center gap-3 bg-white rounded-xl p-4 border border-gray-100">
          <div class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
            </svg>
          </div>
          <div>
            <div class="font-semibold text-gray-900 text-sm">Kolay İade</div>
            <div class="text-xs text-gray-500">14 Gün İçinde</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Popüler Kategoriler</h2>
      </div>
      <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
        <div 
          v-for="category in visibleCategories" 
          :key="category.id"
          @click="filterByCategory(category.id)"
          class="group bg-white rounded-2xl p-6 text-center cursor-pointer hover:shadow-xl transition-all border border-gray-100 hover:border-blue-200"
          :class="{ 'ring-2 ring-blue-500 border-blue-500': selectedCategory === category.id }"
        >
          <div class="mb-3 group-hover:scale-110 transition-transform flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h3.5a1 1 0 01.8.4L12 8l2.7-2.6a1 1 0 01.8-.4H18a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 13h10M7 9h.01M17 9h.01" />
            </svg>
          </div>
          <div class="font-semibold text-sm text-gray-900 mb-1">{{ category.name }}</div>
          <div v-if="category.products_count > 0" class="text-xs text-blue-600 font-medium">
            {{ category.products_count }}+ Ürün
          </div>
        </div>
      </div>
    </section>

    <!-- Turbo Mod Panel (Minimal) -->
    <section class="container mx-auto px-4 py-4">
      <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold mb-1">Turbo Mod Yarışması</h3>
            <p class="text-sm opacity-90">En çok alışveriş yapanlara özel ödüller</p>
          </div>
          <router-link to="/admin/turbo-winners" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm px-6 py-3 rounded-xl font-semibold transition-all border border-white/20">
            Detayları Gör
          </router-link>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="container mx-auto px-4 py-12 bg-white rounded-3xl" id="products">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Öne Çıkan Ürünler</h2>
          <p class="text-gray-600">En popüler ve çok satanlar</p>
        </div>
        <div>
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Öne Çıkan Ürünler</h2>
          <p class="text-gray-600">En popüler ve çok satanlar</p>
        </div>
        <div class="flex gap-3">
          <select 
            v-model="filters.sort" 
            @change="fetchProducts"
            class="px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white font-medium"
          >
            <option value="newest">Yeni Ürünler</option>
            <option value="price_asc">Artan Fiyat</option>
            <option value="price_desc">Azalan Fiyat</option>
            <option value="popular">Popüler</option>
          </select>
          <button 
            v-if="selectedCategory" 
            @click="clearFilters"
            class="px-4 py-2.5 text-sm bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-medium flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Filtre Temizle
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
        <div v-for="i in 10" :key="i" class="bg-white rounded-2xl overflow-hidden border border-gray-100">
          <div class="aspect-square bg-gray-200 animate-pulse"></div>
          <div class="p-4 space-y-3">
            <div class="h-4 bg-gray-200 rounded animate-pulse"></div>
            <div class="h-4 bg-gray-200 rounded w-2/3 animate-pulse"></div>
            <div class="h-6 bg-gray-200 rounded w-1/2 animate-pulse"></div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="products.length === 0" class="text-center py-20">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">Ürün Bulunamadı</h3>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">Aradığınız kriterlere uygun ürün bulunmamaktadır. Lütfen filtreleri değiştirerek tekrar deneyin.</p>
        <button @click="clearFilters" class="px-8 py-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold">
          Tüm Ürünleri Göster
        </button>
      </div>

      <!-- Products Grid -->
      <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
        <div
          v-for="product in products"
          :key="product.id"
          class="bg-white rounded-xl overflow-hidden border border-gray-100 hover:shadow-lg transition-all group"
        >
          <div class="aspect-square overflow-hidden bg-gray-100 relative cursor-pointer" @click="router.push(`/products/${product.id}`)">
            <img
              v-if="product.image_url"
              :src="product.image_url"
              :alt="product.name"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
              <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            
            <!-- Discount Badge -->
            <div v-if="product.discount_percent" class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-md">
              -{{ product.discount_percent }}%
            </div>

            <!-- Wishlist Button -->
            <button class="absolute top-2 right-2 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white">
              <svg class="w-5 h-5 text-gray-600 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </button>
          </div>

          <div class="p-3">
            <h3 class="font-medium text-sm text-gray-900 mb-1 line-clamp-2 cursor-pointer hover:text-blue-600" @click="router.push(`/products/${product.id}`)">
              {{ product.name }}
            </h3>
            
            <div class="flex items-center gap-1 mb-2">
                <div class="flex items-center gap-1 text-xs">
                  <BadgeIcon v-for="i in 5" :key="i" :name="'star'" :cls="i <= (product.rating || 5) ? 'w-3 h-3 text-yellow-400' : 'w-3 h-3 text-gray-300'" />
                </div>
              <span class="text-xs text-gray-400">({{ product.reviews_count || 0 }})</span>
            </div>

            <div class="flex items-baseline gap-2 mb-3">
              <span class="text-lg font-bold text-gray-900">{{ formatPrice(product.price) }}</span>
              <span v-if="product.old_price" class="text-xs text-gray-400 line-through">{{ formatPrice(product.old_price) }}</span>
            </div>

            <button 
              @click="addToCart(product)" 
              class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Sepete Ekle
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-8">
        <button 
          v-for="page in totalPages" 
          :key="page"
          @click="changePage(page)"
          class="min-w-[40px] h-10 rounded-lg text-sm font-medium transition-colors"
          :class="currentPage === page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
        >
          {{ page }}
        </button>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-16">
      <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
          <!-- About -->
          <div>
            <h3 class="text-white font-bold text-lg mb-4">SportoOnline</h3>
            <p class="text-sm mb-4">Türkiye'nin en büyük spor ürünleri pazaryeri. Güvenli alışveriş, hızlı teslimat.</p>
            <div class="flex gap-3">
              <a href="#" class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <a href="#" class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
              </a>
              <a href="#" class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-pink-600 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="text-white font-bold mb-4">Hızlı Linkler</h3>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-white">Hakkımızda</a></li>
              <li><a href="#" class="hover:text-white">Kariyer</a></li>
              <li><a href="#" class="hover:text-white">Blog</a></li>
              <li><a href="#" class="hover:text-white">İletişim</a></li>
            </ul>
          </div>

          <!-- Help -->
          <div>
            <h3 class="text-white font-bold mb-4">Yardım</h3>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-white">SSS</a></li>
              <li><a href="#" class="hover:text-white">Sipariş Takibi</a></li>
              <li><a href="#" class="hover:text-white">İptal & İade</a></li>
              <li><a href="#" class="hover:text-white">Kargo Bilgileri</a></li>
            </ul>
          </div>

          <!-- Newsletter -->
          <div>
            <h3 class="text-white font-bold mb-4">Bülten</h3>
            <p class="text-sm mb-4">Kampanya ve fırsatlardan haberdar olun</p>
            <div class="flex gap-2">
              <input type="email" placeholder="E-posta adresiniz" class="flex-1 px-3 py-2 rounded-lg bg-gray-800 border border-gray-700 text-sm focus:ring-2 focus:ring-blue-500" />
              <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Abone Ol</button>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm">
          <p>&copy; 2025 SportoOnline. Tüm hakları saklıdır.</p>
          <div class="flex gap-6 mt-4 md:mt-0">
            <a href="#" class="hover:text-white">Kullanım Koşulları</a>
            <a href="#" class="hover:text-white">Gizlilik Politikası</a>
            <a href="#" class="hover:text-white">Çerez Politikası</a>
          </div>
        </div>
      </div>
    </footer>

    <OnboardingTour />
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'
import { useSEO } from '@/composables'
import OnboardingTour from '@/components/marketplace/OnboardingTour.vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

useSEO({
  title: 'SportoOnline - Türkiye\'nin En Büyük Spor Ürünleri Pazaryeri',
  description: 'Spor malzemeleri, ekipmanlar ve aksesuarlar için Türkiye\'nin lider C2C pazaryeri.',
  keywords: 'spor, spor malzemeleri, fitness, outdoor, marketplace',
  image: '/og-image.jpg',
  type: 'website'
})

const router = useRouter()
const toast = useToast()
const cartStore = useCartStore()

const loading = ref(false)
const stats = ref({
  total_products: 0,
  total_sellers: 0,
  total_orders: 0,
  total_customers: 0
})

const categories = ref<any[]>([
  { id: 1, name: 'Ayakkabı', icon: '👟', products_count: 0 },
  { id: 2, name: 'Giyim', icon: '👕', products_count: 0 },
  { id: 3, name: 'Ekipman', icon: '🎒', products_count: 0 },
  { id: 4, name: 'Aksesuar', icon: '🧢', products_count: 0 },
  { id: 5, name: 'Beslenme', icon: '🥤', products_count: 0 },
  { id: 6, name: 'Teknoloji', icon: '⌚', products_count: 0 }
])

const products = ref<any[]>([])
const currentPage = ref(1)
const totalPages = ref(1)
const selectedCategory = ref<number | null>(null)
const searchQuery = ref('')
const filters = ref({
  sort: 'newest'
})

// Computed: Only show categories with products
const visibleCategories = computed(() => {
  return categories.value.filter(cat => cat.products_count > 0)
})

function scrollToProducts() {
  document.getElementById('products')?.scrollIntoView({ behavior: 'smooth' })
}

function performSearch() {
  if (searchQuery.value.trim()) {
    selectedCategory.value = null
    fetchProducts(1)
    scrollToProducts()
  }
}

async function fetchStats() {
  try {
    const { data } = await axios.get('/api/marketplace/stats')
    stats.value = data
  } catch (error) {
    console.error('Stats error:', error)
  }
}

async function fetchCategories() {
  try {
    const { data } = await axios.get('/api/categories')
        if (data.data) {
      categories.value = data.data.map((cat: any) => ({
        id: cat.id,
        name: cat.name,
        icon: cat.icon || 'ticket',
        products_count: cat.products_count || 0
      }))
    }
  } catch (error) {
    console.error('Categories error:', error)
  }
}

async function fetchProducts(page = 1) {
  loading.value = true
  try {
    const params: any = { page, sort: filters.value.sort }
    if (selectedCategory.value) params.category_id = selectedCategory.value
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    
    const { data } = await axios.get('/api/products', { params })
    products.value = data.data || []
    currentPage.value = data.current_page || 1
    totalPages.value = data.last_page || 1
  } catch (error) {
    console.error('Products error:', error)
  } finally {
    loading.value = false
  }
}

function filterByCategory(categoryId: number) {
  selectedCategory.value = selectedCategory.value === categoryId ? null : categoryId
  searchQuery.value = ''
  fetchProducts(1)
  scrollToProducts()
}

function clearFilters() {
  selectedCategory.value = null
  searchQuery.value = ''
  fetchProducts(1)
}

function changePage(page: number) {
  currentPage.value = page
  fetchProducts(page)
  scrollToProducts()
}

function addToCart(product: any) {
  cartStore.addToCart(product)
  toast.success(`${product.name} sepete eklendi`, { timeout: 2000, hideProgressBar: true })
}

function formatPrice(value: number) {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value || 0)
}

onMounted(() => {
  fetchStats()
  fetchCategories()
  fetchProducts()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
