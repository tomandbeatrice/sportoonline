<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-indigo-600 text-white flex-shrink-0">
      <div class="p-6 text-2xl font-bold">Satıcı Paneli</div>
      <nav>
        <ul>
          <li>
            <router-link to="/seller" class="flex items-center px-6 py-3 hover:bg-indigo-700">
              <span class="mr-3">📊</span>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/seller/products" class="flex items-center px-6 py-3 hover:bg-indigo-700">
              <span class="mr-3">📦</span>
              <span>Ürünlerim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/seller/products/add" class="flex items-center px-6 py-3 hover:bg-indigo-700">
              <span class="mr-3">➕</span>
              <span>Ürün Ekle</span>
            </router-link>
          </li>
          <li>
            <router-link to="/seller/orders" class="flex items-center px-6 py-3 hover:bg-indigo-700">
              <span class="mr-3">📋</span>
              <span>Siparişler</span>
            </router-link>
          </li>
          <li>
            <router-link to="/seller/shop" class="flex items-center px-6 py-3 hover:bg-indigo-700">
              <span class="mr-3">🏪</span>
              <span>Mağazam</span>
            </router-link>
          </li>
          <li>
            <router-link to="/seller/analytics" class="flex items-center px-6 py-3 hover:bg-indigo-700">
              <span class="mr-3">📈</span>
              <span>Analitik</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white shadow p-4 flex justify-between items-center">
        <h1 class="text-xl font-semibold">Satıcı Paneli</h1>
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-600">Satıcı</span>
          <button class="px-4 py-2 text-sm bg-red-500 text-white rounded hover:bg-red-600">
            Çıkış
          </button>
        </div>
      </header>
      <div class="flex-1 p-6 overflow-y-auto">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
// Seller panel layout
</script>

<style scoped>
.router-link-exact-active {
  background-color: #4338ca;
  border-left: 4px solid #818cf8;
}
</style>
