import { ModulTrendData } from '@/types'
import { getColor } from '@/utils/colorScale'

export function buildTrendChartData(data: ModulTrendData[]) {
  const labels = data[0]?.logs.map(log => log.date) || []

  const datasets = data.map(modul => ({
    label: modul.modulName,
    data: modul.logs.map(log => log.count),
    borderColor: getColor(modul.modulName),
    backgroundColor: 'transparent',
    pointRadius: 3,
    tension: 0.4,
  }))

  return {
    data: { labels, datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' },
        tooltip: { mode: 'index', intersect: false },
      },
      scales: {
        x: {
          title: { display: true, text: 'Tarih' },
          ticks: { maxRotation: 45, minRotation: 0 },
        },
        y: {
          title: { display: true, text: 'Log Sayısı' },
          beginAtZero: true,
        },
      },
    },
  }
}