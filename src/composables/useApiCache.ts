import { ref } from 'vue'

export function useApiCache() {
  const cache = ref(new Map())
  
  const get = (key: string) => cache.value.get(key)
  const set = (key: string, value: any) => cache.value.set(key, value)
  const has = (key: string) => cache.value.has(key)
  const clear = () => cache.value.clear()
  
  return {
    get,
    set,
    has,
    clear
  }
}
