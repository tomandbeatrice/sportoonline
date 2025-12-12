import axios from 'axios'

interface ExchangeRates {
  base: string
  timestamp: number
  rates: Record<string, number>
}

const CACHE_DURATION = 60 * 60 * 1000 // 1 saat
const CACHE_KEY = 'exchange_rates_cache'
const API_ENDPOINT = 'https://api.exchangerate-api.com/v4/latest'

let cachedRates: ExchangeRates | null = null
let cacheTime: number = 0

// Para birimleri
export const SUPPORTED_CURRENCIES = ['TRY', 'USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SAR']
export const CURRENCY_SYMBOLS: Record<string, string> = {
  'TRY': '₺',
  'USD': '$',
  'EUR': '€',
  'GBP': '£',
  'JPY': '¥',
  'CNY': '¥',
  'AED': 'د.إ',
  'SAR': 'ر.س'
}

export async function getExchangeRates(baseCurrency: string = 'TRY'): Promise<ExchangeRates> {
  const now = Date.now()

  // Cache kontrolü
  if (cachedRates?.base === baseCurrency && (now - cacheTime) < CACHE_DURATION) {
    return cachedRates
  }

  try {
    const response = await axios.get<any>(`${API_ENDPOINT}/${baseCurrency}`)
    cachedRates = {
      base: response.data.base,
      timestamp: now,
      rates: response.data.rates
    }
    cacheTime = now
    return cachedRates
  } catch (error) {
    console.error('Exchange rate API hatası:', error)
    // Fallback: mock rates
    return {
      base: baseCurrency,
      timestamp: now,
      rates: getMockRates(baseCurrency)
    }
  }
}

function getMockRates(baseCurrency: string): Record<string, number> {
  // TRY tabanlı mock oranlar
  const tryRates: Record<string, number> = {
    'TRY': 1,
    'USD': 0.032,
    'EUR': 0.030,
    'GBP': 0.025,
    'JPY': 4.72,
    'CNY': 0.23,
    'AED': 0.117,
    'SAR': 0.120
  }

  if (baseCurrency === 'TRY') {
    return tryRates
  }

  // Diğer para birimleri için ters hesaplama
  const rates: Record<string, number> = {}
  const baseRate = tryRates[baseCurrency] || 1

  SUPPORTED_CURRENCIES.forEach(cur => {
    rates[cur] = tryRates[cur] / baseRate
  })

  return rates
}

export function convertCurrency(amount: number, fromCurrency: string, toCurrency: string, rates: ExchangeRates): number {
  if (fromCurrency === toCurrency) return amount
  if (!rates.rates[toCurrency]) return amount
  const baseRate = rates.rates[fromCurrency] || 1
  const targetRate = rates.rates[toCurrency] || 1
  return (amount / baseRate) * targetRate
}

export function clearCache(): void {
  cachedRates = null
  cacheTime = 0
}
