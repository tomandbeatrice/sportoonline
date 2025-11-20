import { ref, onMounted, onUnmounted } from 'vue'

interface InfiniteScrollOptions {
  distance?: number // Distance from bottom to trigger load (in pixels)
  throttle?: number // Throttle delay in ms
  immediate?: boolean // Load immediately on mount
}

export function useInfiniteScroll(
  loadMore: () => Promise<void>,
  options: InfiniteScrollOptions = {}
) {
  const {
    distance = 300,
    throttle = 200,
    immediate = true
  } = options

  const loading = ref(false)
  const hasMore = ref(true)
  const scrollContainer = ref<HTMLElement | null>(null)
  let throttleTimeout: ReturnType<typeof setTimeout> | null = null

  const checkScroll = () => {
    if (!hasMore.value || loading.value) return

    const container = scrollContainer.value || window
    const scrollTop = container === window 
      ? window.scrollY 
      : (container as HTMLElement).scrollTop
    
    const scrollHeight = container === window
      ? document.documentElement.scrollHeight
      : (container as HTMLElement).scrollHeight
    
    const clientHeight = container === window
      ? window.innerHeight
      : (container as HTMLElement).clientHeight

    const distanceFromBottom = scrollHeight - (scrollTop + clientHeight)

    if (distanceFromBottom < distance) {
      handleLoadMore()
    }
  }

  const throttledScroll = () => {
    if (throttleTimeout) return

    throttleTimeout = setTimeout(() => {
      checkScroll()
      throttleTimeout = null
    }, throttle)
  }

  const handleLoadMore = async () => {
    if (loading.value || !hasMore.value) return

    loading.value = true
    try {
      await loadMore()
    } catch (error) {
      console.error('Error loading more items:', error)
    } finally {
      loading.value = false
    }
  }

  const setContainer = (element: HTMLElement | null) => {
    scrollContainer.value = element
  }

  const reset = () => {
    hasMore.value = true
    loading.value = false
  }

  const stop = () => {
    hasMore.value = false
  }

  onMounted(() => {
    const container = scrollContainer.value || window
    container.addEventListener('scroll', throttledScroll, { passive: true })

    if (immediate) {
      checkScroll()
    }
  })

  onUnmounted(() => {
    const container = scrollContainer.value || window
    container.removeEventListener('scroll', throttledScroll)
    
    if (throttleTimeout) {
      clearTimeout(throttleTimeout)
    }
  })

  return {
    loading,
    hasMore,
    setContainer,
    reset,
    stop,
    load: handleLoadMore
  }
}
