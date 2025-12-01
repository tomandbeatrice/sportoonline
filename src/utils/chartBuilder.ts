export function buildTrendChartData(data: ModulTrendData[]) {
  const labels = data[0]?.logs.map(log => log.date) || []

  const datasets = data.map(modul => ({
    label: modul.modulName,
    data: modul.logs.map(log => log.count),
    borderColor: getColor(modul.modulName),
    fill: false,
    tension: 0.3,
  }))

  return {
    data: { labels, datasets },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' },
        tooltip: { mode: 'index', intersect: false },
      },
      scales: {
        x: { title: { display: true, text: 'Tarih' } },
        y: { title: { display: true, text: 'Log Sayısı' }, beginAtZero: true },
      },
    },
  }
}