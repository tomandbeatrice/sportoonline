import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getExchangeRates, SUPPORTED_CURRENCIES, CURRENCY_SYMBOLS } from '@/services/exchangeRateService'

interface ExchangeRates {
  base: string
  timestamp: number
  rates: Record<string, number>
}

export const useCurrencyStore = defineStore('currency', () => {
  const selectedCurrency = ref<string>(
    (() => {
      try {
        return localStorage.getItem('selected_currency') || 'TRY'
      } catch {
        return 'TRY'
      }
    })()
  )

  const exchangeRates = ref<ExchangeRates | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const setSelectedCurrency = (currency: string) => {
    if (SUPPORTED_CURRENCIES.includes(currency)) {
      selectedCurrency.value = currency
      try {
        localStorage.setItem('selected_currency', currency)
      } catch (e) {
        console.warn('localStorage kullanılamaz:', e)
      }
    }
  }

  const fetchExchangeRates = async (baseCurrency: string = 'TRY') => {
    loading.value = true
    error.value = null
    try {
      exchangeRates.value = await getExchangeRates(baseCurrency)
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Kur alınırken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  const getSymbol = (currency: string) => CURRENCY_SYMBOLS[currency] || currency

  return {
    selectedCurrency,
    exchangeRates,
    loading,
    error,
    setSelectedCurrency,
    fetchExchangeRates,
    getSymbol
  }
})
