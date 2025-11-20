import { ref } from 'vue'

export function useDebounce<T extends (...args: any[]) => any>(
  func: T,
  delay: number = 300
) {
  let timeoutId: ReturnType<typeof setTimeout> | null = null
  const isPending = ref(false)

  const debouncedFunction = (...args: Parameters<T>) => {
    isPending.value = true
    
    if (timeoutId !== null) {
      clearTimeout(timeoutId)
    }

    timeoutId = setTimeout(() => {
      func(...args)
      isPending.value = false
      timeoutId = null
    }, delay)
  }

  const cancel = () => {
    if (timeoutId !== null) {
      clearTimeout(timeoutId)
      timeoutId = null
      isPending.value = false
    }
  }

  const flush = (...args: Parameters<T>) => {
    cancel()
    func(...args)
  }

  return {
    debouncedFunction,
    isPending,
    cancel,
    flush
  }
}

export function useThrottle<T extends (...args: any[]) => any>(
  func: T,
  limit: number = 300
) {
  let lastRun = 0
  let timeoutId: ReturnType<typeof setTimeout> | null = null
  const isThrottled = ref(false)

  const throttledFunction = (...args: Parameters<T>) => {
    const now = Date.now()

    if (now - lastRun >= limit) {
      func(...args)
      lastRun = now
    } else {
      isThrottled.value = true
      
      if (timeoutId !== null) {
        clearTimeout(timeoutId)
      }

      timeoutId = setTimeout(() => {
        func(...args)
        lastRun = Date.now()
        isThrottled.value = false
        timeoutId = null
      }, limit - (now - lastRun))
    }
  }

  const cancel = () => {
    if (timeoutId !== null) {
      clearTimeout(timeoutId)
      timeoutId = null
      isThrottled.value = false
    }
  }

  return {
    throttledFunction,
    isThrottled,
    cancel
  }
}
