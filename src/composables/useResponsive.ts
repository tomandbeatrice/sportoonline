import { ref, onMounted, onUnmounted, computed } from 'vue'

export type Breakpoint = 'xs' | 'sm' | 'md' | 'lg' | 'xl' | '2xl'

interface BreakpointConfig {
  xs: number
  sm: number
  md: number
  lg: number
  xl: number
  '2xl': number
}

const defaultBreakpoints: BreakpointConfig = {
  xs: 0,
  sm: 640,
  md: 768,
  lg: 1024,
  xl: 1280,
  '2xl': 1536,
}

export function useResponsive(customBreakpoints?: Partial<BreakpointConfig>) {
  const breakpoints = { ...defaultBreakpoints, ...customBreakpoints }
  
  const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024)
  const windowHeight = ref(typeof window !== 'undefined' ? window.innerHeight : 768)

  const currentBreakpoint = computed<Breakpoint>(() => {
    const width = windowWidth.value
    if (width >= breakpoints['2xl']) return '2xl'
    if (width >= breakpoints.xl) return 'xl'
    if (width >= breakpoints.lg) return 'lg'
    if (width >= breakpoints.md) return 'md'
    if (width >= breakpoints.sm) return 'sm'
    return 'xs'
  })

  // Boolean helpers
  const isMobile = computed(() => windowWidth.value < breakpoints.md)
  const isTablet = computed(() => windowWidth.value >= breakpoints.md && windowWidth.value < breakpoints.lg)
  const isDesktop = computed(() => windowWidth.value >= breakpoints.lg)
  const isLargeDesktop = computed(() => windowWidth.value >= breakpoints.xl)

  // Specific breakpoint checks
  const isXs = computed(() => currentBreakpoint.value === 'xs')
  const isSm = computed(() => currentBreakpoint.value === 'sm')
  const isMd = computed(() => currentBreakpoint.value === 'md')
  const isLg = computed(() => currentBreakpoint.value === 'lg')
  const isXl = computed(() => currentBreakpoint.value === 'xl')
  const is2xl = computed(() => currentBreakpoint.value === '2xl')

  // Breakpoint comparison helpers
  const isSmAndUp = computed(() => windowWidth.value >= breakpoints.sm)
  const isMdAndUp = computed(() => windowWidth.value >= breakpoints.md)
  const isLgAndUp = computed(() => windowWidth.value >= breakpoints.lg)
  const isXlAndUp = computed(() => windowWidth.value >= breakpoints.xl)
  const is2xlAndUp = computed(() => windowWidth.value >= breakpoints['2xl'])

  const isSmAndDown = computed(() => windowWidth.value < breakpoints.md)
  const isMdAndDown = computed(() => windowWidth.value < breakpoints.lg)
  const isLgAndDown = computed(() => windowWidth.value < breakpoints.xl)
  const isXlAndDown = computed(() => windowWidth.value < breakpoints['2xl'])

  // Orientation
  const isLandscape = computed(() => windowWidth.value > windowHeight.value)
  const isPortrait = computed(() => windowHeight.value >= windowWidth.value)

  // Aspect ratio helpers
  const aspectRatio = computed(() => windowWidth.value / windowHeight.value)
  const isWideScreen = computed(() => aspectRatio.value > 1.6)
  const isTallScreen = computed(() => aspectRatio.value < 0.75)

  // Touch device detection
  const isTouchDevice = ref(false)

  // Resize handler
  let resizeTimeout: ReturnType<typeof setTimeout> | null = null
  
  const handleResize = () => {
    if (resizeTimeout) {
      clearTimeout(resizeTimeout)
    }
    
    resizeTimeout = setTimeout(() => {
      windowWidth.value = window.innerWidth
      windowHeight.value = window.innerHeight
    }, 100) // Debounce for performance
  }

  // Check if touch device
  const checkTouchDevice = () => {
    isTouchDevice.value = 'ontouchstart' in window || navigator.maxTouchPoints > 0
  }

  onMounted(() => {
    if (typeof window !== 'undefined') {
      windowWidth.value = window.innerWidth
      windowHeight.value = window.innerHeight
      window.addEventListener('resize', handleResize, { passive: true })
      checkTouchDevice()
    }
  })

  onUnmounted(() => {
    if (typeof window !== 'undefined') {
      window.removeEventListener('resize', handleResize)
      if (resizeTimeout) {
        clearTimeout(resizeTimeout)
      }
    }
  })

  // Helper function for responsive values
  const responsive = <T>(values: Partial<Record<Breakpoint, T>>, defaultValue: T): T => {
    const bp = currentBreakpoint.value
    const breakpointOrder: Breakpoint[] = ['xs', 'sm', 'md', 'lg', 'xl', '2xl']
    const currentIndex = breakpointOrder.indexOf(bp)

    // Find the closest defined value at or below current breakpoint
    for (let i = currentIndex; i >= 0; i--) {
      const key = breakpointOrder[i]
      if (values[key] !== undefined) {
        return values[key] as T
      }
    }

    return defaultValue
  }

  // Grid columns helper
  const gridCols = (config: Partial<Record<Breakpoint, number>>, defaultCols = 1) => {
    return computed(() => responsive(config, defaultCols))
  }

  // Container width helper
  const containerWidth = computed(() => {
    return responsive({
      xs: '100%',
      sm: '640px',
      md: '768px',
      lg: '1024px',
      xl: '1280px',
      '2xl': '1536px',
    }, '100%')
  })

  return {
    // Dimensions
    windowWidth,
    windowHeight,
    aspectRatio,
    
    // Current breakpoint
    currentBreakpoint,
    
    // Device type
    isMobile,
    isTablet,
    isDesktop,
    isLargeDesktop,
    isTouchDevice,
    
    // Specific breakpoints
    isXs,
    isSm,
    isMd,
    isLg,
    isXl,
    is2xl,
    
    // Breakpoint ranges (and up)
    isSmAndUp,
    isMdAndUp,
    isLgAndUp,
    isXlAndUp,
    is2xlAndUp,
    
    // Breakpoint ranges (and down)
    isSmAndDown,
    isMdAndDown,
    isLgAndDown,
    isXlAndDown,
    
    // Orientation
    isLandscape,
    isPortrait,
    isWideScreen,
    isTallScreen,
    
    // Helpers
    responsive,
    gridCols,
    containerWidth,
    breakpoints,
  }
}

// Utility for responsive class generation
export function useResponsiveClasses() {
  const { isMobile, isTablet, isDesktop, currentBreakpoint } = useResponsive()

  const responsiveClass = (baseClass: string, mobileClass?: string, tabletClass?: string, desktopClass?: string) => {
    if (isMobile.value && mobileClass) return mobileClass
    if (isTablet.value && tabletClass) return tabletClass
    if (isDesktop.value && desktopClass) return desktopClass
    return baseClass
  }

  const hideOnMobile = computed(() => isMobile.value ? 'hidden' : '')
  const hideOnDesktop = computed(() => isDesktop.value ? 'hidden' : '')
  const showOnlyMobile = computed(() => !isMobile.value ? 'hidden' : '')
  const showOnlyDesktop = computed(() => !isDesktop.value ? 'hidden' : '')

  return {
    currentBreakpoint,
    responsiveClass,
    hideOnMobile,
    hideOnDesktop,
    showOnlyMobile,
    showOnlyDesktop,
  }
}

// Safe area insets for mobile devices (notch, home indicator)
export function useSafeArea() {
  const safeAreaInsets = ref({
    top: 0,
    right: 0,
    bottom: 0,
    left: 0,
  })

  onMounted(() => {
    if (typeof window !== 'undefined' && CSS.supports('padding-top', 'env(safe-area-inset-top)')) {
      const computeSafeAreas = () => {
        const style = getComputedStyle(document.documentElement)
        safeAreaInsets.value = {
          top: parseInt(style.getPropertyValue('--sat') || '0', 10),
          right: parseInt(style.getPropertyValue('--sar') || '0', 10),
          bottom: parseInt(style.getPropertyValue('--sab') || '0', 10),
          left: parseInt(style.getPropertyValue('--sal') || '0', 10),
        }
      }

      // Set CSS variables for safe area
      document.documentElement.style.setProperty('--sat', 'env(safe-area-inset-top)')
      document.documentElement.style.setProperty('--sar', 'env(safe-area-inset-right)')
      document.documentElement.style.setProperty('--sab', 'env(safe-area-inset-bottom)')
      document.documentElement.style.setProperty('--sal', 'env(safe-area-inset-left)')

      computeSafeAreas()
    }
  })

  return {
    safeAreaInsets,
  }
}
