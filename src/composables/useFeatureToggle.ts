import { ref, onMounted } from 'vue'

const toggles = ref<Record<string, boolean>>({})
const loaded = ref(false)

async function fetchToggles() {
  try {
    const res = await fetch('/api/features')
    const data = await res.json()
    toggles.value = data
    loaded.value = true
  } catch {
    // fallback
    toggles.value = {
      new_search: true,
      chat_widget: true,
      coupon_system: true,
    }
    loaded.value = true
  }
}

export function useFeatureToggle(key: string) {
  if (!loaded.value) onMounted(fetchToggles)
  return ref(() => !!toggles.value[key])
}
