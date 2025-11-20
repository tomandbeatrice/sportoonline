import { ref, onMounted, onUnmounted } from 'vue'

export function useKeyboardNavigation(options: {
  onEnter?: () => void
  onEscape?: () => void
  onArrowUp?: () => void
  onArrowDown?: () => void
  onArrowLeft?: () => void
  onArrowRight?: () => void
  enabled?: boolean
} = {}) {
  const enabled = ref(options.enabled !== false)

  const handleKeyDown = (event: KeyboardEvent) => {
    if (!enabled.value) return

    switch (event.key) {
      case 'Enter':
        options.onEnter?.()
        break
      case 'Escape':
        options.onEscape?.()
        break
      case 'ArrowUp':
        event.preventDefault()
        options.onArrowUp?.()
        break
      case 'ArrowDown':
        event.preventDefault()
        options.onArrowDown?.()
        break
      case 'ArrowLeft':
        options.onArrowLeft?.()
        break
      case 'ArrowRight':
        options.onArrowRight?.()
        break
    }
  }

  onMounted(() => {
    window.addEventListener('keydown', handleKeyDown)
  })

  onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown)
  })

  const enable = () => { enabled.value = true }
  const disable = () => { enabled.value = false }

  return { enabled, enable, disable }
}

export function useFocusTrap(containerRef: any) {
  const focusableElements = ref<HTMLElement[]>([])
  const firstFocusable = ref<HTMLElement | null>(null)
  const lastFocusable = ref<HTMLElement | null>(null)

  const getFocusableElements = () => {
    if (!containerRef.value) return

    const elements = containerRef.value.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    )
    
    focusableElements.value = Array.from(elements)
    firstFocusable.value = focusableElements.value[0] || null
    lastFocusable.value = focusableElements.value[focusableElements.value.length - 1] || null
  }

  const handleKeyDown = (event: KeyboardEvent) => {
    if (event.key !== 'Tab') return

    getFocusableElements()

    if (event.shiftKey) {
      if (document.activeElement === firstFocusable.value) {
        event.preventDefault()
        lastFocusable.value?.focus()
      }
    } else {
      if (document.activeElement === lastFocusable.value) {
        event.preventDefault()
        firstFocusable.value?.focus()
      }
    }
  }

  const activate = () => {
    getFocusableElements()
    firstFocusable.value?.focus()
    document.addEventListener('keydown', handleKeyDown)
  }

  const deactivate = () => {
    document.removeEventListener('keydown', handleKeyDown)
  }

  onMounted(() => {
    activate()
  })

  onUnmounted(() => {
    deactivate()
  })

  return { activate, deactivate }
}

export function useScreenReader() {
  const announce = (message: string, priority: 'polite' | 'assertive' = 'polite') => {
    const announcement = document.createElement('div')
    announcement.setAttribute('role', 'status')
    announcement.setAttribute('aria-live', priority)
    announcement.setAttribute('aria-atomic', 'true')
    announcement.className = 'sr-only'
    announcement.textContent = message

    document.body.appendChild(announcement)

    setTimeout(() => {
      document.body.removeChild(announcement)
    }, 1000)
  }

  return { announce }
}

// Focus management
export function useFocusManagement() {
  const previousFocus = ref<HTMLElement | null>(null)

  const saveFocus = () => {
    previousFocus.value = document.activeElement as HTMLElement
  }

  const restoreFocus = () => {
    previousFocus.value?.focus()
  }

  const setFocus = (element: HTMLElement | null) => {
    element?.focus()
  }

  return {
    saveFocus,
    restoreFocus,
    setFocus
  }
}
