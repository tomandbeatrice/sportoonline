watch(sprint.status, (val) => {
  if (val === 'completed') {
    generatePDF()
    sendToSlack(reportUrl.value)
  }
})