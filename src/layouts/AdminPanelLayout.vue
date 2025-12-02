<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
      <div class="p-6 text-2xl font-bold">Admin Paneli</div>
      <nav>
        <ul>
          <li>
            <router-link to="/admin" class="flex items-center px-6 py-3 hover:bg-gray-700">
              <span class="mr-3">📊</span>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/orders" class="flex items-center px-6 py-3 hover:bg-gray-700">
              <span class="mr-3">📦</span>
              <span>Siparişler</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/sellers" class="flex items-center px-6 py-3 hover:bg-gray-700">
              <span class="mr-3">🏪</span>
              <span>Satıcılar</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/customers" class="flex items-center px-6 py-3 hover:bg-gray-700">
              <span class="mr-3">👥</span>
              <span>Müşteriler</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/products" class="flex items-center px-6 py-3 hover:bg-gray-700">
              <span class="mr-3">📦</span>
              <span>Ürünler</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/categories" class="flex items-center px-6 py-3 hover:bg-gray-700">
              <span class="mr-3">📂</span>
              <span>Kategoriler</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/settings" class="flex items-center px-6 py-3 hover:bg-gray-700">
              <span class="mr-3">⚙️</span>
              <span>Ayarlar</span>
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
// Admin panel layout
</script>

<style scoped>
.router-link-exact-active {
  background-color: #4a5568;
  border-left: 4px solid #4299e1;
}
</style>
