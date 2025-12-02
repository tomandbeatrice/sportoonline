import { ref } from 'vue'

export function useErrorHandler() {
  const error = ref<string | null>(null)
  const loading = ref(false)

  const handleError = (err: any, customMessage?: string) => {
    console.warn('Error:', err.message || err)
    
    if (err.message === 'Network Error' || err.code === 'ERR_NETWORK') {
      error.value = customMessage || 'Backend bağlantısı kurulamadı. Laravel backend sunucusunu başlatın.'
    } else {
      error.value = customMessage || 'Bir hata oluştu. Lütfen tekrar deneyin.'
    }
  }

  const clearError = () => {
    error.value = null
  }

  const withLoading = async <T>(fn: () => Promise<T>): Promise<T | null> => {
    try {
      loading.value = true
      clearError()
      return await fn()
    } catch (err) {
      handleError(err)
      return null
    } finally {
      loading.value = false
    }
  }

  return {
    error,
    loading,
    handleError,
    clearError,
    withLoading
  }
}
