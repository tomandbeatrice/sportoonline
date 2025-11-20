// services/ReportMailer.ts

import axios from 'axios'

interface MailOptions {
  to: string[]
  subject: string
  body: string
  attachment?: Blob
}

export async function sendSprintReport(options: MailOptions): Promise<boolean> {
  try {
    const formData = new FormData()
    formData.append('subject', options.subject)
    formData.append('body', options.body)
    options.to.forEach(email => formData.append('to[]', email))
    if (options.attachment) {
      formData.append('attachment', options.attachment, 'Sprint-Raporu.pdf')
    }

    await axios.post('/api/send-report', formData)
    return true
  } catch (error) {
    console.error('Rapor gönderimi başarısız:', error)
    return false
  }
}