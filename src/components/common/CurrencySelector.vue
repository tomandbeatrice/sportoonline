<template>
  <div class="flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg border border-slate-200">
    <label class="text-sm font-medium text-slate-700">Para Birimi:</label>
    <select 
      :value="currencyStore.selectedCurrency"
      @change="(e) => currencyStore.setSelectedCurrency((e.target as HTMLSelectElement).value)"
      class="flex-1 px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
    >
      <option v-for="currency in SUPPORTED_CURRENCIES" :key="currency" :value="currency">
        {{ currency }} ({{ getSymbol(currency) }})
      </option>
    </select>
    <span v-if="currencyStore.loading" class="text-xs text-slate-500">Güncelleniyor...</span>
    <button
      @click="refreshRates"
      :disabled="currencyStore.loading"
      class="px-3 py-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
      title="Kurları yenile"
    >
      🔄
    </button>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useCurrencyStore } from '@/stores/currency'
import { SUPPORTED_CURRENCIES, CURRENCY_SYMBOLS } from '@/services/exchangeRateService'

const currencyStore = useCurrencyStore()

const getSymbol = (currency: string) => CURRENCY_SYMBOLS[currency] || currency

const refreshRates = async () => {
  await currencyStore.fetchExchangeRates(currencyStore.selectedCurrency)
}

onMounted(() => {
  // İlk yüklemede kurları al
  currencyStore.fetchExchangeRates(currencyStore.selectedCurrency)
})
</script>

<style scoped>
select:disabled {
  cursor: not-allowed;
}
</style>
