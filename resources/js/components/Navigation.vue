<template>
  <nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <router-link to="/" class="flex items-center space-x-2">
          <span class="text-2xl font-bold text-blue-600">🛍️ Market</span>
        </router-link>

        <!-- Arama Çubuğu -->
        <div class="hidden md:flex flex-1 max-w-2xl mx-8">
          <div class="w-full">
            <input
              type="text"
              placeholder="Ürün ara..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex items-center space-x-6">
          <router-link
            to="/products"
            class="text-gray-700 hover:text-blue-600 font-medium transition-colors"
          >
            Ürünler
          </router-link>

          <router-link
            to="/cart"
            class="relative text-gray-700 hover:text-blue-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ cartCount }}
            </span>
          </router-link>

          <!-- Kullanıcı Menüsü -->
          <div v-if="isAuthenticated" class="relative">
            <button
              @click="showUserMenu = !showUserMenu"
              class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <span class="hidden md:inline font-medium">{{ user?.name || 'Hesabım' }}</span>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="showUserMenu"
              class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-50"
            >
              <router-link
                to="/orders"
                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600"
                @click="showUserMenu = false"
              >
                📦 Siparişlerim
              </router-link>
              <router-link
                v-if="user?.role === 'seller'"
                to="/seller/dashboard"
                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600"
                @click="showUserMenu = false"
              >
                🏪 Satıcı Paneli
              </router-link>
              <router-link
                v-if="user?.role === 'admin'"
                to="/admin"
                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600"
                @click="showUserMenu = false"
              >
                ⚙️ Admin Paneli
              </router-link>
              <hr class="my-2" />
              <button
                @click="logout"
                class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50"
              >
                🚪 Çıkış Yap
              </button>
            </div>
          </div>

          <!-- Giriş/Kayıt -->
          <div v-else class="flex items-center space-x-3">
            <router-link
              to="/login"
              class="text-gray-700 hover:text-blue-600 font-medium transition-colors"
            >
              Giriş Yap
            </router-link>
            <router-link
              to="/register"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
            >
              Kayıt Ol
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Search -->
    <div class="md:hidden px-4 pb-3">
      <input
        type="text"
        placeholder="Ürün ara..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
      />
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const showUserMenu = ref(false);
const user = ref<any>(null);
const cartCount = ref(0);

const isAuthenticated = computed(() => {
  return !!localStorage.getItem('token');
});

async function loadUser() {
  if (!isAuthenticated.value) return;

  try {
    const response = await axios.get('/api/me');
    user.value = response.data;
  } catch (error) {
    console.error('Kullanıcı bilgisi alınamadı:', error);
  }
}

async function loadCartCount() {
  if (!isAuthenticated.value) return;

  try {
    const response = await axios.get('/api/cart');
    cartCount.value = response.data.length;
  } catch (error) {
    console.error('Sepet sayısı alınamadı:', error);
  }
}

function logout() {
  localStorage.removeItem('token');
  user.value = null;
  showUserMenu.value = false;
  router.push('/login');
}

onMounted(() => {
  loadUser();
  loadCartCount();

  // Click outside to close menu
  document.addEventListener('click', (e) => {
    const target = e.target as HTMLElement;
    if (!target.closest('.relative')) {
      showUserMenu.value = false;
    }
  });
});
</script>

<style scoped>
/* Dropdown animasyonu */
</style>
