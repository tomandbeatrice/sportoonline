import { ref, Ref } from 'vue'

interface ImageLoadOptions {
  placeholder?: string
  fallback?: string
  lazy?: boolean
}

export function useImageLoader(options: ImageLoadOptions = {}) {
  const {
    placeholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect fill="%23e2e8f0" width="400" height="300"/%3E%3C/svg%3E',
    fallback = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect fill="%23e2e8f0" width="400" height="300"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" fill="%2394a3b8" font-size="16"%3EGörsel Yüklenemedi%3C/text%3E%3C/svg%3E',
    lazy = true
  } = options

  const loadImage = (src: string): Promise<string> => {
    return new Promise((resolve, reject) => {
      const img = new Image()
      img.onload = () => resolve(src)
      img.onerror = () => reject(new Error('Image load failed'))
      img.src = src
    })
  }

  const createImageRef = (initialSrc: string = '') => {
    const currentSrc = ref(placeholder)
    const isLoading = ref(false)
    const hasError = ref(false)

    const load = async (src: string) => {
      if (!src) {
        currentSrc.value = placeholder
        return
      }

      isLoading.value = true
      hasError.value = false
      currentSrc.value = placeholder

      try {
        await loadImage(src)
        currentSrc.value = src
      } catch (error) {
        hasError.value = true
        currentSrc.value = fallback
      } finally {
        isLoading.value = false
      }
    }

    if (initialSrc && !lazy) {
      load(initialSrc)
    }

    return {
      src: currentSrc,
      isLoading,
      hasError,
      load
    }
  }

  return {
    createImageRef,
    loadImage
  }
}
