import { ref, watch, Ref } from 'vue'

interface UseLocalStorageOptions<T> {
  serializer?: {
    read: (value: string) => T
    write: (value: T) => string
  }
  onError?: (error: Error) => void
}

export function useLocalStorage<T>(
  key: string,
  defaultValue: T,
  options: UseLocalStorageOptions<T> = {}
): Ref<T> {
  const {
    serializer = {
      read: (v: string) => JSON.parse(v),
      write: (v: T) => JSON.stringify(v)
    },
    onError = (e) => console.error(e)
  } = options

  const data = ref<T>(defaultValue) as Ref<T>

  // Read from localStorage
  const read = () => {
    try {
      const rawValue = localStorage.getItem(key)
      if (rawValue !== null) {
        data.value = serializer.read(rawValue)
      }
    } catch (error) {
      onError(error as Error)
    }
  }

  // Write to localStorage
  const write = () => {
    try {
      if (data.value === null || data.value === undefined) {
        localStorage.removeItem(key)
      } else {
        localStorage.setItem(key, serializer.write(data.value))
      }
    } catch (error) {
      onError(error as Error)
    }
  }

  // Initialize
  read()

  // Watch for changes
  watch(
    data,
    () => write(),
    { deep: true }
  )

  // Listen to storage events from other tabs
  window.addEventListener('storage', (e) => {
    if (e.key === key && e.newValue !== null) {
      try {
        data.value = serializer.read(e.newValue)
      } catch (error) {
        onError(error as Error)
      }
    }
  })

  return data
}

export function useSessionStorage<T>(
  key: string,
  defaultValue: T,
  options: UseLocalStorageOptions<T> = {}
): Ref<T> {
  const {
    serializer = {
      read: (v: string) => JSON.parse(v),
      write: (v: T) => JSON.stringify(v)
    },
    onError = (e) => console.error(e)
  } = options

  const data = ref<T>(defaultValue) as Ref<T>

  const read = () => {
    try {
      const rawValue = sessionStorage.getItem(key)
      if (rawValue !== null) {
        data.value = serializer.read(rawValue)
      }
    } catch (error) {
      onError(error as Error)
    }
  }

  const write = () => {
    try {
      if (data.value === null || data.value === undefined) {
        sessionStorage.removeItem(key)
      } else {
        sessionStorage.setItem(key, serializer.write(data.value))
      }
    } catch (error) {
      onError(error as Error)
    }
  }

  read()

  watch(
    data,
    () => write(),
    { deep: true }
  )

  return data
}
