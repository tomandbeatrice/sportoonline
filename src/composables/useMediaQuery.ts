import { ref, onMounted, onUnmounted } from 'vue'

export function useMediaQuery(query: string) {
  const matches = ref(false)
  let mediaQuery: MediaQueryList | null = null

  const updateMatches = (event: MediaQueryListEvent | MediaQueryList) => {
    matches.value = event.matches
  }

  onMounted(() => {
    mediaQuery = window.matchMedia(query)
    matches.value = mediaQuery.matches

    // Modern browsers
    if (mediaQuery.addEventListener) {
      mediaQuery.addEventListener('change', updateMatches)
    } else {
      // Legacy browsers
      mediaQuery.addListener(updateMatches)
    }
  })

  onUnmounted(() => {
    if (mediaQuery) {
      if (mediaQuery.removeEventListener) {
        mediaQuery.removeEventListener('change', updateMatches)
      } else {
        mediaQuery.removeListener(updateMatches)
      }
    }
  })

  return matches
}

// Preset breakpoints
export function useBreakpoints() {
  return {
    isMobile: useMediaQuery('(max-width: 640px)'),
    isTablet: useMediaQuery('(min-width: 641px) and (max-width: 1024px)'),
    isDesktop: useMediaQuery('(min-width: 1025px)'),
    isSm: useMediaQuery('(min-width: 640px)'),
    isMd: useMediaQuery('(min-width: 768px)'),
    isLg: useMediaQuery('(min-width: 1024px)'),
    isXl: useMediaQuery('(min-width: 1280px)'),
    is2Xl: useMediaQuery('(min-width: 1536px)'),
    prefersDark: useMediaQuery('(prefers-color-scheme: dark)'),
    prefersReducedMotion: useMediaQuery('(prefers-reduced-motion: reduce)')
  }
}
