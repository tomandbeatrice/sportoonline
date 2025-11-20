import { ref, Ref } from 'vue'

interface UseAsyncOptions {
  immediate?: boolean
  onSuccess?: (data: any) => void
  onError?: (error: Error) => void
}

export function useAsync<T>(
  asyncFn: (...args: any[]) => Promise<T>,
  options: UseAsyncOptions = {}
) {
  const { immediate = false, onSuccess, onError } = options

  const data = ref<T | null>(null) as Ref<T | null>
  const error = ref<Error | null>(null)
  const loading = ref(false)
  const isReady = ref(false)

  const execute = async (...args: any[]): Promise<T | null> => {
    loading.value = true
    error.value = null
    isReady.value = false

    try {
      const result = await asyncFn(...args)
      data.value = result
      isReady.value = true
      onSuccess?.(result)
      return result
    } catch (err) {
      error.value = err as Error
      onError?.(err as Error)
      return null
    } finally {
      loading.value = false
    }
  }

  const reset = () => {
    data.value = null
    error.value = null
    loading.value = false
    isReady.value = false
  }

  if (immediate) {
    execute()
  }

  return {
    data,
    error,
    loading,
    isReady,
    execute,
    reset
  }
}
