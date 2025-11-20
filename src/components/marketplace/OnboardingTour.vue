<template>
  <!-- This component doesn't render anything visible, it just manages the driver.js instance -->
  <div class="hidden"></div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

const TOUR_KEY = 'sportoonline_onboarding_seen';

onMounted(() => {
  const hasSeenTour = localStorage.getItem(TOUR_KEY);

  if (!hasSeenTour) {
    // Give a small delay to ensure elements are rendered
    setTimeout(() => {
      startTour();
    }, 1000);
  }
});

const startTour = () => {
  const driverObj = driver({
    showProgress: true,
    animate: true,
    doneBtnText: 'Tamamla',
    nextBtnText: 'İleri',
    prevBtnText: 'Geri',
    steps: [
      {
        element: '#tour-navbar-search',
        popover: {
          title: 'Hızlı Arama',
          description: 'İstediğiniz ürünü, markayı veya satıcıyı buradan hızlıca arayabilirsiniz.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '#tour-cart-btn',
        popover: {
          title: 'Sepetiniz',
          description: 'Eklediğiniz ürünleri burada görebilir ve siparişinizi tamamlayabilirsiniz.',
          side: 'bottom',
          align: 'end'
        }
      },
      {
        element: '#discovery-lab',
        popover: {
          title: 'Keşif Laboratuvarı',
          description: 'Detaylı filtreleme ve sıralama seçenekleri ile aradığınız ürünü kolayca bulun.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: '#tour-filters-sidebar',
        popover: {
          title: 'Filtreler',
          description: 'Renk, beden, marka ve kategori filtrelerini kullanarak sonuçları daraltın.',
          side: 'right',
          align: 'start'
        }
      },
      {
        element: '#tour-product-grid',
        popover: {
          title: 'Ürünler',
          description: 'Ürünleri inceleyin, hızlıca sepete ekleyin veya favorilere alın.',
          side: 'top',
          align: 'center'
        }
      }
    ],
    onDestroyed: () => {
      localStorage.setItem(TOUR_KEY, 'true');
    }
  });

  driverObj.drive();
};
</script>

<style>
/* Custom styles for driver.js popover if needed */
.driver-popover.driverjs-theme {
  background-color: #ffffff;
  color: #1e293b;
  border-radius: 1rem;
  padding: 1rem;
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

.driver-popover.driverjs-theme .driver-popover-title {
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #0f172a;
}

.driver-popover.driverjs-theme .driver-popover-description {
  font-size: 0.9rem;
  line-height: 1.5;
  color: #475569;
}

.driver-popover.driverjs-theme button {
  background-color: #2563eb;
  color: white;
  border: none;
  border-radius: 0.5rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.driver-popover.driverjs-theme button:hover {
  background-color: #1d4ed8;
}

.driver-popover.driverjs-theme button.driver-popover-prev-btn {
  background-color: #f1f5f9;
  color: #475569;
}

.driver-popover.driverjs-theme button.driver-popover-prev-btn:hover {
  background-color: #e2e8f0;
}
</style>