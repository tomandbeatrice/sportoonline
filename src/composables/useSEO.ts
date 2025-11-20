import { ref, watch } from 'vue'
import { useHead } from '@vueuse/head'

interface SEOConfig {
  title?: string
  description?: string
  keywords?: string
  image?: string
  url?: string
  type?: string
  twitterCard?: 'summary' | 'summary_large_image' | 'app' | 'player'
  author?: string
  siteName?: string
  locale?: string
}

export function useSEO(config: SEOConfig = {}) {
  const defaultConfig = {
    siteName: 'SportoOnline',
    locale: 'tr_TR',
    type: 'website',
    twitterCard: 'summary_large_image' as const,
    author: 'SportoOnline',
    url: typeof window !== 'undefined' ? window.location.href : '',
  }

  const seoConfig = ref({ ...defaultConfig, ...config })

  const updateSEO = (newConfig: Partial<SEOConfig>) => {
    seoConfig.value = { ...seoConfig.value, ...newConfig }
  }

  watch(seoConfig, (newConfig) => {
    useHead({
      title: newConfig.title,
      meta: [
        // Basic meta tags
        { name: 'description', content: newConfig.description },
        { name: 'keywords', content: newConfig.keywords },
        { name: 'author', content: newConfig.author },
        
        // Open Graph
        { property: 'og:title', content: newConfig.title },
        { property: 'og:description', content: newConfig.description },
        { property: 'og:image', content: newConfig.image },
        { property: 'og:url', content: newConfig.url },
        { property: 'og:type', content: newConfig.type },
        { property: 'og:site_name', content: newConfig.siteName },
        { property: 'og:locale', content: newConfig.locale },
        
        // Twitter Card
        { name: 'twitter:card', content: newConfig.twitterCard },
        { name: 'twitter:title', content: newConfig.title },
        { name: 'twitter:description', content: newConfig.description },
        { name: 'twitter:image', content: newConfig.image },
        
        // Mobile
        { name: 'viewport', content: 'width=device-width, initial-scale=1, maximum-scale=5' },
        { name: 'theme-color', content: '#2563eb' },
        { name: 'apple-mobile-web-app-capable', content: 'yes' },
        { name: 'apple-mobile-web-app-status-bar-style', content: 'default' },
      ],
      link: [
        { rel: 'canonical', href: newConfig.url },
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'apple-touch-icon', href: '/apple-touch-icon.png' },
      ],
    })
  }, { immediate: true })

  return {
    seoConfig,
    updateSEO,
  }
}

// Structured Data helpers
export function generateProductSchema(product: any) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Product',
    name: product.name,
    image: product.image_url,
    description: product.description,
    sku: product.id,
    offers: {
      '@type': 'Offer',
      url: `${window.location.origin}/products/${product.id}`,
      priceCurrency: 'TRY',
      price: product.price,
      availability: product.stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
      seller: {
        '@type': 'Organization',
        name: 'SportoOnline',
      },
    },
  }
}

export function generateBreadcrumbSchema(items: Array<{ name: string; url: string }>) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((item, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      name: item.name,
      item: item.url,
    })),
  }
}

export function generateOrganizationSchema() {
  return {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: 'SportoOnline',
    url: window.location.origin,
    logo: `${window.location.origin}/logo.png`,
    contactPoint: {
      '@type': 'ContactPoint',
      telephone: '+90-XXX-XXX-XX-XX',
      contactType: 'customer service',
    },
    sameAs: [
      'https://www.facebook.com/sportoonline',
      'https://www.twitter.com/sportoonline',
      'https://www.instagram.com/sportoonline',
    ],
  }
}

export function injectStructuredData(data: any) {
  if (typeof document === 'undefined') return

  const script = document.createElement('script')
  script.type = 'application/ld+json'
  script.textContent = JSON.stringify(data)
  document.head.appendChild(script)

  return () => {
    document.head.removeChild(script)
  }
}
