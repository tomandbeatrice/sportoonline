<template>
  <div class="card group">
    <!-- Campaign Badge -->
    <div v-if="activeCampaign" class="campaign-badge">
      <IconTicket cls="w-4 h-4 text-white" />
      <span class="badge-text">{{ discountText }}</span>
    </div>

    <!-- Countdown Timer -->
    <div v-if="activeCampaign && timeRemaining" class="countdown-timer">
      {{ timeRemaining }}
    </div>

    <!-- Quick Actions (Hover) -->
    <div class="quick-actions">
      <button 
        @click.stop="toggleWishlist"
        :class="['action-btn', isInWishlist ? 'active' : '']"
        title="Favorilere ekle"
      >
        <svg class="h-5 w-5" :class="isInWishlist ? 'fill-current' : 'fill-none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </button>
      <button 
        @click.stop="$emit('quick-view', product)"
        class="action-btn"
        title="Hızlı görüntüle"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
      </button>
      <button 
        @click.stop="toggleCompare"
        :class="['action-btn', isInCompare ? 'active' : '']"
        title="Karşılaştır"
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
      </button>
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
          class="main-image"
        />
        <!-- Hover Image Swap -->
        <img
          v-if="product.hover_image || product.images?.[1]"
          :src="product.hover_image || product.images[1]"
          :alt="product.name"
          class="hover-image"
          loading="lazy"
        />
      </picture>
      <div v-else-if="imageState === 'loading'" class="skeleton-loader"></div>
      <div v-else-if="imageState === 'error'" class="error-placeholder">
        <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
    </div>

    <!-- Seller Info -->
    <div v-if="product.seller_name" class="seller-info">
      <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
      <span>{{ product.seller_name }}</span>
    </div>

    <h3>{{ product.name }}</h3>
    
    <!-- Rating Stars -->
    <div v-if="product.rating" class="rating-section">
      <div class="stars">
        <svg v-for="i in 5" :key="i" class="star" :class="i <= Math.floor(product.rating) ? 'filled' : 'empty'" viewBox="0 0 20 20">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
      </div>
      <span class="rating-count">({{ product.review_count || 0 }})</span>
    </div>
    
    <!-- Price with Discount -->
    <div class="price-section">
      <div v-if="activeCampaign" class="discounted-price">
        <span class="old-price">₺{{ formatPrice(product.price) }}</span>
        <span class="new-price">₺{{ formatPrice(discountedPrice) }}</span>
      </div>
      <p v-else class="regular-price">₺{{ formatPrice(product.price) }}</p>
    </div>

    <!-- Quick Add to Cart Button -->
    <button @click="handleQuickAdd" class="add-to-cart-btn" :disabled="isAdding">
      <IconCart v-if="!isAdding" cls="w-5 h-5 inline-block mr-2 text-white" />
      <svg v-else class="animate-spin h-5 w-5 inline-block mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      {{ isAdding ? 'Ekleniyor...' : 'Sepete Ekle' }}
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
    hover_image?: string
    images?: string[]
    seller_name?: string
    rating?: number
    review_count?: number
    active_campaign?: {
      id: number
      name: string
      type: string
      discount_value: number
      end_date: string
    }
  } 
}>()

const emit = defineEmits(['add-to-cart', 'quick-view', 'toggle-wishlist', 'toggle-compare'])

const isAdding = ref(false)
const isInWishlist = ref(false)
const isInCompare = ref(false)

const imageUrl = computed(() => props.product.image_url || props.product.image)
const { createImageRef } = useImageLoader({
  placeholder: '/placeholder.png',
  lazy: true
})
const imageRef = createImageRef(imageUrl.value)
const imageSrc = computed(() => imageRef.src.value)
const imageState = computed(() => {
  if (imageRef.isLoading.value) return 'loading'
  if (imageRef.hasError.value) return 'error'
  return 'loaded'
})

const imageSrcSet = computed(() => {
  const src = imageSrc && (imageSrc as any).value ? (imageSrc as any).value : ''
  if (!src) return ''

  const makeWebpPath = (p: string) => {
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

const handleQuickAdd = async () => {
  isAdding.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 500)) // Simulate API call
    emit('add-to-cart', props.product)
  } finally {
    isAdding.value = false
  }
}

const toggleWishlist = () => {
  isInWishlist.value = !isInWishlist.value
  emit('toggle-wishlist', props.product)
}

const toggleCompare = () => {
  isInCompare.value = !isInCompare.value
  emit('toggle-compare', props.product)
}

onMounted(() => {
  imageRef.load(imageUrl.value)
  
  if (activeCampaign.value) {
    updateCountdown()
    countdownInterval = window.setInterval(updateCountdown, 60000)
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
  overflow: hidden;
}

.card:hover {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  transform: translateY(-4px);
}

/* Quick Actions */
.quick-actions {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 20;
  display: flex;
  flex-direction: column;
  gap: 8px;
  opacity: 0;
  transform: translateX(10px);
  transition: all 0.3s ease;
}

.card:hover .quick-actions {
  opacity: 1;
  transform: translateX(0);
}

.action-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: none;
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  cursor: pointer;
  transition: all 0.2s ease;
  color: #64748b;
}

.action-btn:hover {
  background: #f1f5f9;
  transform: scale(1.1);
}

.action-btn.active {
  background: #ef4444;
  color: white;
}

.action-btn.active svg {
  fill: white;
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

.countdown-timer {
  position: absolute;
  top: 50px;
  left: 12px;
  background: rgba(0, 0, 0, 0.75);
  color: white;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.7rem;
  font-weight: 600;
  z-index: 10;
  backdrop-filter: blur(4px);
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

.card-image picture {
  position: relative;
  display: block;
  width: 100%;
  height: 100%;
}

.main-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: opacity 0.3s ease;
}

.hover-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.card:hover .hover-image {
  opacity: 1;
}

.seller-info {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-bottom: 8px;
  font-size: 0.75rem;
  color: #64748b;
  justify-content: center;
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

.rating-section {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin-bottom: 8px;
}

.stars {
  display: flex;
  gap: 2px;
}

.star {
  width: 14px;
  height: 14px;
}

.star.filled {
  fill: #fbbf24;
  color: #fbbf24;
}

.star.empty {
  fill: #e5e7eb;
  color: #e5e7eb;
}

.rating-count {
  font-size: 0.75rem;
  color: #9ca3af;
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
  display: flex;
  align-items: center;
  justify-content: center;
}

.add-to-cart-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  transform: scale(1.02);
}

.add-to-cart-btn:active:not(:disabled) {
  transform: scale(0.98);
}

.add-to-cart-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
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

@keyframes spin {
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>