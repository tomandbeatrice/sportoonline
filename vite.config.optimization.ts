import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// Optimized Vite Configuration for 4-Service Platform
export default defineConfig({
  plugins: [vue()],
  
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },

  // Build Optimizations
  build: {
    target: 'es2015',
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true, // Remove console.log in production
        drop_debugger: true,
      },
    },
    
    // Code Splitting Strategy
    rollupOptions: {
      output: {
        manualChunks: {
          // Core Vue dependencies
          'vue-vendor': ['vue', 'vue-router', 'pinia'],
          
          // UI libraries
          'ui-vendor': ['vue-toastification', 'lucide-vue-next'],
          
          // Service-specific chunks
          'food-service': [
            './src/views/services/FoodHome.vue',
            './src/views/services/food/RestaurantList.vue',
            './src/views/services/food/RestaurantDetail.vue',
            './src/views/services/food/FoodCategory.vue',
          ],
          'hotel-service': [
            './src/views/services/HotelHome.vue',
            './src/views/services/hotels/HotelList.vue',
            './src/views/services/hotels/HotelDetail.vue',
            './src/views/services/hotels/HotelSearch.vue',
          ],
          'product-service': [
            './src/views/product/ProductList.vue',
            './src/views/product/ProductListEnhanced.vue',
            './src/views/marketplace/ProductDetail.vue',
          ],
          'professional-service': [
            './src/views/services/ServicesHome.vue',
            './src/views/services/service/ServiceList.vue',
          ],
          
          // Admin Panel chunk
          'admin-panel': [
            './src/views/admin/AdminLayout.vue',
            './src/views/admin/AdminDashboardNew.vue',
            './src/components/admin/AdminSidebar.vue',
          ],
          
          // Seller Panel chunk
          'seller-panel': [
            './src/views/seller/SellerDashboard.vue',
            './src/views/seller/ApplyAsSeller.vue',
            './src/views/seller/SellerProductsEnhanced.vue',
          ],
          
          // Cart & Checkout
          'checkout-flow': [
            './src/views/cart/Cart.vue',
            './src/views/cart/CartEnhanced.vue',
            './src/views/cart/Checkout.vue',
            './src/views/cart/CheckoutEnhanced.vue',
          ],
        },
      },
    },
    
    // Chunk size warnings
    chunkSizeWarningLimit: 1000, // 1MB
    
    // CSS code splitting
    cssCodeSplit: true,
    
    // Source maps for production debugging
    sourcemap: false,
  },

  // Development Server
  server: {
    port: 3000,
    open: true,
    cors: true,
  },

  // Performance optimizations
  optimizeDeps: {
    include: [
      'vue',
      'vue-router',
      'pinia',
      'axios',
      'vue-toastification',
      'lucide-vue-next',
    ],
    exclude: ['@vueuse/core'], // Let Vite handle these on-demand
  },

  // CSS preprocessing
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@import "@/styles/variables.scss";`,
      },
    },
  },
})
