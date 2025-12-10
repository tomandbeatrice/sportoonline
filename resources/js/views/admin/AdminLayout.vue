<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex-shrink-0 overflow-y-auto">
      <div class="p-6 text-2xl font-bold border-b border-gray-700">
        🏪 Admin Paneli
      </div>
      <nav class="py-4">
        <!-- Dashboard -->
        <div class="px-4 py-2 text-xs uppercase text-gray-400 font-semibold">Genel</div>
        <ul>
          <li>
            <router-link to="/admin" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">📊</span>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/seller-applications" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">📬</span>
              <span>Satıcı Başvuruları</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/financial-report" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">💰</span>
              <span>Finans Raporu</span>
            </router-link>
          </li>
        </ul>

        <!-- Restaurant Management -->
        <div class="px-4 py-2 mt-4 text-xs uppercase text-gray-400 font-semibold">Restoran Yönetimi</div>
        <ul>
          <li>
            <router-link to="/admin/restaurants" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">🍕</span>
              <span>Restoranlar</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/food-orders" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">🛒</span>
              <span>Yemek Siparişleri</span>
            </router-link>
          </li>
        </ul>

        <!-- Hotel Management -->
        <div class="px-4 py-2 mt-4 text-xs uppercase text-gray-400 font-semibold">Otel Yönetimi</div>
        <ul>
          <li>
            <router-link to="/admin/hotels" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">🏨</span>
              <span>Oteller</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/reservations" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">📅</span>
              <span>Rezervasyonlar</span>
            </router-link>
          </li>
        </ul>

        <!-- Transport Management -->
        <div class="px-4 py-2 mt-4 text-xs uppercase text-gray-400 font-semibold">Transfer Yönetimi</div>
        <ul>
          <li>
            <router-link to="/admin/vehicles" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">🚗</span>
              <span>Araçlar</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/drivers" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">👨‍✈️</span>
              <span>Sürücüler</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/transfers" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">🚐</span>
              <span>Transferler</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/routes" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">🗺️</span>
              <span>Rotalar</span>
            </router-link>
          </li>
        </ul>

        <!-- Blog Management -->
        <div class="px-4 py-2 mt-4 text-xs uppercase text-gray-400 font-semibold">Blog Yönetimi</div>
        <ul>
          <li>
            <router-link to="/admin/blog-posts" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">📝</span>
              <span>Blog Yazıları</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/blog-categories" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">📂</span>
              <span>Kategoriler</span>
            </router-link>
          </li>
        </ul>

        <!-- Campaign & AI -->
        <div class="px-4 py-2 mt-4 text-xs uppercase text-gray-400 font-semibold">Kampanya & AI</div>
        <ul>
          <li>
            <router-link to="/admin/campaign-ai" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
              <span class="mr-3">🤖</span>
              <span>Kampanya AI</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white shadow p-4 flex justify-between items-center">
        <h1 class="text-xl font-semibold">Yönetim Paneli</h1>
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-600">Admin</span>
          <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
            Çıkış
          </button>
        </div>
      </header>
      <div class="flex-1 p-6 overflow-y-auto bg-gray-50">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script setup>
// This can be expanded with user info, logout, etc.
</script>

<style scoped>
.router-link-exact-active {
  background-color: #4a5568; /* gray-700 */
  border-left: 4px solid #4299e1; /* blue-500 */
}
</style>
