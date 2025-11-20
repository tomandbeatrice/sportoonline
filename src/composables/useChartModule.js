import { ref, watch } from 'vue'
import Chart from 'chart.js/auto'

export function useChartModule(selectedCategory) {
  const chartCanvas = ref(null)
  const chartInstance = ref(null)
  const categories = ref([])

  function updateChart(data) {
    if (!chartInstance.value) return

    const filtered = selectedCategory.value
      ? data.filter(item => item.category === selectedCategory.value)
      : data

    chartInstance.value.data.labels = filtered.map(item => item.timestamp)
    chartInstance.value.data.datasets[0].data = filtered.map(item => item.count)
    chartInstance.value.update()
  }

  async function fetchAndUpdateChart() {
    const response = await fetch('/api/chart-data')
    const data = await response.json()

    categories.value = [...new Set(data.map(item => item.category))]
    updateChart(data)
  }

  function initChart() {
    chartInstance.value = new Chart(chartCanvas.value, {
      type: 'line',
      data: {
        labels: [],
        datasets: [{
          label: 'Veri Akışı',
          data: [],
          borderColor: '#42A5F5',
          backgroundColor: 'rgba(66,165,245,0.2)',
          fill: true
        }]
      },
      options: {
        responsive: true,
        scales: {
          x: { title: { display: true, text: 'Zaman' } },
          y: { title: { display: true, text: 'Sayı' } }
        }
      }
    })
  }

  watch(selectedCategory, fetchAndUpdateChart)

  return {
    chartCanvas,
    categories,
    initChart,
    fetchAndUpdateChart
  }
}