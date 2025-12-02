pdf.text(`Sprint: ${s.label}`, 10, y)
pdf.text(`Başarı: %${s.successRate} | Modül: ${s.moduleCount} | Hata: ${s.errorCount}`, 10, y + 10)