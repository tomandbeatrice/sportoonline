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
            <router-link to="/admin/seller-applications" class="flex items-center px-6 py-3 hover:bg-gray-700">
              <span class="mr-3">📬</span>
              <span>Satıcı Başvuruları</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/financial-report" class="flex items-center px-6 py-3 hover:bg-gray-700">
              <span class="mr-3">💰</span>
              <span>Finans Raporu</span>
            </router-link>
          </li>
          <!-- Add other admin links here -->
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white shadow p-4">
        <h1 class="text-xl font-semibold">Yönetim</h1>
      </header>
      <div class="flex-1 p-6 overflow-y-auto">
        <router-view></router-view> <!-- Child routes will be rendered here -->
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
