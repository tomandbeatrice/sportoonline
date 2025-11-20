<template>
  <div class="export-panel">
    <button @click="generatePDF">📤 PDF Oluştur</button>
  </div>
</template>

<script setup lang="ts">
import jsPDF from 'jspdf'

const generatePDF = () => {
  const doc = new jsPDF()
  doc.text('Alışveriş Özeti', 10, 10)
  doc.text('Ürünler: Sepet verileri buraya gelecek', 10, 20)
  doc.text('Toplam Tutar: ₺XXX', 10, 30)
  doc.save('cart-summary.pdf')
}
</script>

<style scoped>
.export-panel button {
  background-color: #1976d2;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  font-weight: bold;
  cursor: pointer;
}
</style>