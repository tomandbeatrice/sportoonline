import { useCurrencyStore } from '@/stores/currency'
import { convertCurrency } from '@/services/exchangeRateService'

export function useConvertPrice(price: number, fromCurrency: string = 'TRY') {
  const currencyStore = useCurrencyStore()
  const { selectedCurrency, exchangeRates } = currencyStore

  if (!exchangeRates || !selectedCurrency) {
    return price
  }

  try {
    return convertCurrency(price, fromCurrency, selectedCurrency, exchangeRates)
  } catch (error) {
    console.error('Fiyat dönüştürmesi hatası:', error)
    return price
  }
}

export function formatCurrencyWithConversion(
  amount: number,
  baseCurrency: string = 'TRY',
  decimals: number = 2
): string {
  const currencyStore = useCurrencyStore()
  const { selectedCurrency, exchangeRates, getSymbol } = currencyStore

  let convertedAmount = amount
  if (exchangeRates && selectedCurrency) {
    try {
      convertedAmount = convertCurrency(amount, baseCurrency, selectedCurrency, exchangeRates)
    } catch (error) {
      console.error('Kur dönüştürme hatası:', error)
    }
  }

  const symbol = getSymbol(selectedCurrency || baseCurrency)
  return `${symbol}${convertedAmount.toFixed(decimals)}`
}

export function formatPriceRange(min: number, max: number, baseCurrency: string = 'TRY'): string {
  const currencyStore = useCurrencyStore()
  const { selectedCurrency, exchangeRates, getSymbol } = currencyStore

  let convertedMin = min
  let convertedMax = max

  if (exchangeRates && selectedCurrency) {
    try {
      convertedMin = convertCurrency(min, baseCurrency, selectedCurrency, exchangeRates)
      convertedMax = convertCurrency(max, baseCurrency, selectedCurrency, exchangeRates)
    } catch (error) {
      console.error('Kur dönüştürme hatası:', error)
    }
  }

  const symbol = getSymbol(selectedCurrency || baseCurrency)
  return `${symbol}${convertedMin.toFixed(2)} - ${symbol}${convertedMax.toFixed(2)}`
}
