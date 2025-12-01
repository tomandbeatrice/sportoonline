<template>
  <div class="card">
    <!-- Campaign Badge -->
    <div v-if="activeCampaign" class="campaign-badge">
      <IconTicket cls="w-4 h-4 text-white" />
      <span class="badge-text">{{ discountText }}</span>
    </div>

    <!-- Countdown Timer -->
    <div v-if="activeCampaign && timeRemaining" class="countdown-timer">
      {{ timeRemaining }}
    </div>

    <div class="card-image">
      <picture v-if="imageState === 'loaded'">
        <source :srcset="imageSrcSet" type="image/webp" />
        <img
          :src="imageSrc"
          :alt="product.name"
          loading="lazy"
          decoding="async"
          :srcset="imageSrcSet"
          sizes="(max-width: 640px) 100vw, 33vw"
        />
      </picture>
      <div v-else-if="imageState === 'loading'" class="skeleton-loader"></div>
      <div v-else-if="imageState === 'error'" class="error-placeholder">
        <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
    </div>

    <h3>{{ product.name }}</h3>
    
    <!-- Price with Discount -->
    <div class="price-section">
      <div v-if="activeCampaign" class="discounted-price">
        <span class="old-price">₺{{ formatPrice(product.price) }}</span>
        <span class="new-price">₺{{ formatPrice(discountedPrice) }}</span>
      </div>
      <p v-else class="regular-price">₺{{ formatPrice(product.price) }}</p>
    </div>

    <!-- Add to Cart Button -->
    <button @click="$emit('add-to-cart', product)" class="add-to-cart-btn">
      <IconCart cls="w-5 h-5 inline-block mr-2 text-white" /> Sepete Ekle
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useImageLoader } from '@/composables/useImageLoader'
import IconTicket from '@/components/icons/IconTicket.vue'
import IconCart from '@/components/icons/IconCart.vue'

const props = defineProps<{ 
  product: { 
    id: number
    name: string
    price: number
    image: string
    image_url?: string
    active_campaign?: {
      id: number
      name: string
      type: string
      discount_value: number
      end_date: string
    }
  } 
}>()

defineEmits(['add-to-cart'])

const imageUrl = computed(() => props.product.image_url || props.product.image)
const { imageSrc, imageState, load } = useImageLoader(imageUrl.value, {
  placeholder: '/placeholder.png',
  lazy: true
})

const imageSrcSet = computed(() => {
  const src = imageSrc && (imageSrc as any).value ? (imageSrc as any).value : ''
  if (!src) return ''

  const makeWebpPath = (p: string) => {
    // If path has extension, replace with .webp; otherwise append .webp
    const m = p.match(/(.+)\.(jpg|jpeg|png|webp)(?:[?#].*)?$/i)
    if (m) return p.replace(/\.(jpg|jpeg|png|webp)(?:$|(?=[?#]))/i, '.webp')
    return p + '.webp'
  }

  const build = (base: string, width?: number, asWebp = false) => {
    const [path, query] = base.split('?')
    const basePath = asWebp ? makeWebpPath(path) : path
    const q = query ? `?${query}` : ''
    const sep = q ? '&' : '?'
    return width ? `${basePath}${q}${sep}w=${width}` : `${basePath}${q}`
  }

  const webp400 = build(src, 400, true)
  const webp800 = build(src, 800, true)
  const webp1200 = build(src, 1200, true)

  return `${webp400} 400w, ${webp800} 800w, ${webp1200} 1200w`
})

const activeCampaign = computed(() => props.product.active_campaign)

const discountedPrice = computed(() => {
  if (!activeCampaign.value) return props.product.price

  const campaign = activeCampaign.value
  if (campaign.type === 'percentage') {
    return props.product.price * (1 - campaign.discount_value / 100)
  } else if (campaign.type === 'fixed') {
    return Math.max(0, props.product.price - campaign.discount_value)
  }
  return props.product.price
})

const discountText = computed(() => {
  if (!activeCampaign.value) return ''

  const campaign = activeCampaign.value
  if (campaign.type === 'percentage') {
    return `%${campaign.discount_value} İndirim`
  } else if (campaign.type === 'fixed') {
    return `₺${campaign.discount_value} İndirim`
  }
  return 'Kampanya'
})

const timeRemaining = ref('')
let countdownInterval: number | null = null

const updateCountdown = () => {
  if (!activeCampaign.value) return

  const endDate = new Date(activeCampaign.value.end_date)
  const now = new Date()
  const diff = endDate.getTime() - now.getTime()

  if (diff <= 0) {
    timeRemaining.value = ''
    if (countdownInterval) clearInterval(countdownInterval)
    return
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))

  if (days > 0) {
    timeRemaining.value = `${days}g ${hours}s kaldı`
  } else if (hours > 0) {
    timeRemaining.value = `${hours}s ${minutes}dk kaldı`
  } else {
    timeRemaining.value = `${minutes}dk kaldı`
  }
}

onMounted(() => {
  load()
  
  if (activeCampaign.value) {
    updateCountdown()
    countdownInterval = window.setInterval(updateCountdown, 60000) // Update every minute
  }
})

onUnmounted(() => {
  if (countdownInterval) clearInterval(countdownInterval)
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(price)
}
</script>

<style scoped>
.card {
  position: relative;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1rem;
  text-align: center;
  transition: all 0.3s ease;
  background: white;
}

.card:hover {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  transform: translateY(-4px);
}

.campaign-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: bold;
  z-index: 10;
  box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
  display: flex;
  align-items: center;
  gap: 4px;
}

.badge-icon {
  font-size: 1rem;
}

.countdown-timer {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(0, 0, 0, 0.75);
  color: white;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.7rem;
  font-weight: 600;
  z-index: 10;
}

.card-image {
  position: relative;
  width: 100%;
  aspect-ratio: 1;
  overflow: hidden;
  background: #f3f4f6;
  border-radius: 8px;
  margin-bottom: 12px;
}

.card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 8px;
  min-height: 2.5em;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.price-section {
  margin-bottom: 12px;
}

.regular-price {
  font-size: 1.25rem;
  font-weight: bold;
  color: #1f2937;
}

.discounted-price {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.old-price {
  font-size: 0.875rem;
  color: #9ca3af;
  text-decoration: line-through;
}

.new-price {
  font-size: 1.5rem;
  font-weight: bold;
  color: #ef4444;
}

.add-to-cart-btn {
  width: 100%;
  padding: 10px 16px;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.875rem;
}

.add-to-cart-btn:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  transform: scale(1.02);
}

.add-to-cart-btn:active {
  transform: scale(0.98);
}

.skeleton-loader {
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

.error-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f9fafb;
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>