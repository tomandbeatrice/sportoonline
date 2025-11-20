import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'
import { getExportData } from '@/services/exportService'

export async function generatePDF(filters) {
  const doc = new jsPDF()
  const data = await getExportData(filters)

  doc.text('Sprint Raporu', 14, 20)
  doc.text(`Roadmap İlerleme: ${data.progress}%`, 14, 30)

  autoTable(doc, {
    startY: 40,
    head: [['Modül', 'Risk Skoru', 'Öneri']],
    body: data.suggestions.map(s => [s.module, s.risk, s.tip])
  })

  doc.save('sprint-raporu.pdf')
}