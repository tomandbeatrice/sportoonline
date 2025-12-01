const compareRows = timelineData.map(s => ({
  sprint: s.label,
  modules: s.moduleCount,
  errors: s.errorCount,
  success: s.successRate
}))