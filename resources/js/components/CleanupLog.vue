<template>
  <div class="cleanup-log">
    <h2>🧹 Son Export Temizliği</h2>

    <div v-if="loading" class="loading-spinner">⏳ Yükleniyor...</div>
    <div v-if="error" class="error">{{ error }}</div>

    <div v-if="log">
      <p><strong>Tarih:</strong> {{ log.last_cleanup }}</p>
      <p :class="countClass">
        <strong>Silinen Dosya Sayısı:</strong> {{ log.deleted_count }}
        <span v-if="log.deleted_count > 50">⚠️</span>
      </p>
      <p><strong>Toplam Boyut:</strong>
        <span :class="sizeClass">{{ log.total_size_mb }} MB</span>
      </p>

      <button @click="downloadLog" class="download-btn">📥 Log Dosyasını İndir</button>

      <table>
        <thead>
          <tr>
            <th>Dosya</th>
            <th>Boyut (KB)</th>
            <th>Yaş (gün)</th>
            <th>Silinme Zamanı</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="file in log.files" :key="file.file">
            <td class="truncate" data-label="Dosya">{{ file.file }}</td>
            <td data-label="Boyut (KB)">{{ file.size_kb }}</td>
            <td data-label="Yaş (gün)">{{ file.age_days }}</td>
            <td data-label="Silinme Zamanı">{{ file.deleted_at }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      log: null,
      loading: true,
      error: null,
    };
  },
  computed: {
    countClass() {
      if (!this.log) return '';
      return this.log.deleted_count > 50 ? 'alert' : '';
    },
    sizeClass() {
      if (!this.log) return 'badge';
      return this.log.total_size_mb > 500 ? 'badge danger' : 'badge';
    }
  },
  methods: {
    downloadLog() {
      const blob = new Blob([JSON.stringify(this.log, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'cleanup_log.json';
      link.click();
      URL.revokeObjectURL(url);
    }
  },
  mounted() {
    fetch('/api/exports/cleanup-log')
      .then(res => {
        if (!res.ok) throw new Error('Log alınamadı');
        return res.json();
      })
      .then(data => {
        this.log = data;
        this.loading = false;
      })
      .catch(err => {
        this.error = err.message;
        this.loading = false;

        // Test için fallback veri
        this.log = {
          last_cleanup: '2025-08-09',
          deleted_count: 72,
          total_size_mb: 812,
          files: [
            { file: 'export_2025_08_01.csv', size_kb: 1200, age_days: 9, deleted_at: '2025-08-09 03:12' },
            { file: 'export_2025_07_25.csv', size_kb: 980, age_days: 15, deleted_at: '2025-08-09 03:12' }
          ]
        };
      });
  },
};
</script>

<style scoped>
.cleanup-log {
  font-family: sans-serif;
  max-width: 800px;
  margin: auto;
  padding: 1em;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1em;
}
th, td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
}
.download-btn {
  margin: 1em 0;
  padding: 8px 12px;
  background-color: #007bff;
  color: white;
  border: none;
  cursor: pointer;
}
.download-btn:hover {
  background-color: #0056b3;
}
.badge {
  background-color: #28a745;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
}
.badge.danger {
  background-color: #dc3545;
}
.alert {
  color: #dc3545;
  font-weight: bold;
}
.truncate {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.loading-spinner {
  animation: pulse 1s infinite;
}
@keyframes pulse {
  0% { opacity: 0.5; }
  50% { opacity: 1; }
  100% { opacity: 0.5; }
}
.error {
  color: #dc3545;
  font-weight: bold;
}
@media (max-width: 600px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }
  th {
    background: #f8f8f8;
  }
  td {
    border: none;
    padding: 6px;
    position: relative;
  }
  td::before {
    content: attr(data-label);
    font-weight: bold;
    display: block;
    margin-bottom: 4px;
  }
}
</style>