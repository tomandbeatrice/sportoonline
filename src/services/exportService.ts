export async function sendExportToExternalSystem(payload: {
  summary: string
  imageUrl: string
}) {
  const response = await fetch('https://api.external-system.dev/export', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })

  return await response.json()
}