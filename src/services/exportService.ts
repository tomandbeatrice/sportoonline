// Export service stub
export const exportData = async (data: any, format: string) => {
  console.log('Exporting data:', data, format)
  return Promise.resolve({ success: true })
}

export const sendExportToExternalSystem = async (data: any) => {
  console.log('Sending export to external system:', data)
  return Promise.resolve({ success: true })
}

export default {
  exportData,
  sendExportToExternalSystem
}
