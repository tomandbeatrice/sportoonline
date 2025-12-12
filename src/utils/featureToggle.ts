// Simple feature toggle helper with safe defaults
// Priority: runtime overrides (localStorage) > env config > default false

export type FeatureFlag =
  | 'seller.newModule'
  | 'campaign.autoPublish'
  | 'payment.newFlow'
  | 'homepage.experimentalWidgets'
  | 'admin.observability'
  | 'admin.services.restaurants'
  | 'admin.services.foodOrders'
  | 'admin.services.hotels'
  | 'admin.services.reservations'

const ENV_FLAGS: Record<string, boolean> = {
  'seller.newModule': false,
  'campaign.autoPublish': true,
  'payment.newFlow': false,
  'homepage.experimentalWidgets': false,
  'admin.observability': true,
  'admin.services.restaurants': true,
  'admin.services.foodOrders': true,
  'admin.services.hotels': true,
  'admin.services.reservations': true,
}

export function isFeatureEnabled(flag: FeatureFlag): boolean {
  try {
    const ls = localStorage.getItem(`feature:${flag}`)
    if (ls !== null) return ls === 'true'
  } catch (_) {
    // ignore server-side/localStorage unavailability
  }
  const envKey = `VITE_FEATURE_${flag.replace(/\./g, '_').toUpperCase()}`
  const envVal = import.meta?.env?.[envKey]
  if (typeof envVal !== 'undefined') {
    if (typeof envVal === 'boolean') return envVal
    if (typeof envVal === 'string') return envVal.toLowerCase() === 'true'
  }
  return ENV_FLAGS[flag] ?? false
}

export function setRuntimeFeature(flag: FeatureFlag, enabled: boolean) {
  try {
    localStorage.setItem(`feature:${flag}`, enabled ? 'true' : 'false')
  } catch (_) {
    // ignore
  }
}
