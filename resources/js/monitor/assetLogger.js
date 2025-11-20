window.addEventListener('load', () => {
  const entries = performance.getEntriesByType('resource')
    .filter(entry => entry.name.includes('/build/assets/'));

  const assetLogs = entries.map(entry => ({
    name: entry.name.split('/').pop(),
    duration: Math.round(entry.duration),
    size: Math.round(entry.transferSize / 1024) + ' KB'
  }));

  fetch('/api/cockpit/asset-log', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ assets: assetLogs })
  });
});
window.addEventListener('load', () => {
  const entries = performance.getEntriesByType('resource')
    .filter(entry => entry.name.includes('/build/assets/'));

  const assetLogs = entries.map(entry => ({
    name: entry.name.split('/').pop(),
    duration: Math.round(entry.duration),
    size: Math.round(entry.transferSize / 1024) + ' KB'
  }));

  fetch('/api/cockpit/asset-log', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ assets: assetLogs })
  });
});