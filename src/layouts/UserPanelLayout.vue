<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-600 text-white flex-shrink-0">
      <div class="p-6 text-2xl font-bold">Kullanıcı Paneli</div>
      <nav>
        <ul>
          <li>
            <router-link to="/user" class="flex items-center px-6 py-3 hover:bg-blue-700">
              <span class="mr-3">👤</span>
              <span>Profilim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/orders" class="flex items-center px-6 py-3 hover:bg-blue-700">
              <span class="mr-3">📦</span>
              <span>Siparişlerim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/favorites" class="flex items-center px-6 py-3 hover:bg-blue-700">
              <span class="mr-3">❤️</span>
              <span>Favorilerim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/addresses" class="flex items-center px-6 py-3 hover:bg-blue-700">
              <span class="mr-3">📍</span>
              <span>Adreslerim</span>
            </router-link>
          </li>
          <li>
            <router-link to="/user/settings" class="flex items-center px-6 py-3 hover:bg-blue-700">
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
        <h1 class="text-xl font-semibold">Hesabım</h1>
        <div class="flex items-center space-x-4">
          <router-link to="/" class="text-sm text-blue-600 hover:text-blue-800">
            Marketplace'e Dön
          </router-link>
          <span class="text-sm text-gray-600">Kullanıcı</span>
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
// User panel layout
</script>

<style scoped>
.router-link-exact-active {
  background-color: #2563eb;
  border-left: 4px solid #60a5fa;
}
</style>
