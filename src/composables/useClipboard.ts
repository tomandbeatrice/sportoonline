import { ref } from 'vue'

export function useClipboard(options: { timeout?: number } = {}) {
  const { timeout = 2000 } = options

  const copied = ref(false)
  const error = ref<Error | null>(null)

  let timeoutId: ReturnType<typeof setTimeout> | null = null

  const copy = async (text: string): Promise<boolean> => {
    if (!navigator.clipboard) {
      error.value = new Error('Clipboard API desteklenmiyor')
      return false
    }

    try {
      await navigator.clipboard.writeText(text)
      copied.value = true
      error.value = null

      if (timeoutId) clearTimeout(timeoutId)
      
      timeoutId = setTimeout(() => {
        copied.value = false
      }, timeout)

      return true
    } catch (err) {
      error.value = err as Error
      copied.value = false
      return false
    }
  }

  const read = async (): Promise<string> => {
    if (!navigator.clipboard) {
      throw new Error('Clipboard API desteklenmiyor')
    }

    try {
      const text = await navigator.clipboard.readText()
      return text
    } catch (err) {
      error.value = err as Error
      throw err
    }
  }

  return {
    copied,
    error,
    copy,
    read
  }
}
