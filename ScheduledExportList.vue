<template>
  <div class="scheduled-export-table">
    <h2>🧹 Planlanmış Exportlar</h2>
    <table>
      <thead>
        <tr>
          <th>Segment</th>
          <th>Gün</th>
          <th>Saat</th>
          <th>İşlem</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in exports" :key="item.segmentId + item.time">
          <td>#{{ item.segmentId }}</td>
          <td>{{ item.day }}</td>
          <td>{{ item.time }}</td>
          <td>
            <button @click="deletePlan(item)" class="delete-btn">🗑️ Sil</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-if="!exports.length" class="empty-message">Henüz planlanmış export bulunamadı.</p>
  </div>
</template>

<script>
export default {
  name: 'ScheduledExportTable',
  data() {
    return {
      exports: [],
    }
  },
  mounted() {
    this.fetchPlans()
  },
  methods: {
    async fetchPlans() {
      try {
        const res = await fetch('/admin/scheduled-exports')
        this.exports = await res.json()
      } catch (err) {
        console.error('Export planları alınamadı:', err)
      }
    },
    async deletePlan(item) {
      try {
        await fetch('/admin/scheduled-exports', {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            segmentId: item.segmentId,
            time: item.time,
            day: item.day,
          }),
        })
        this.fetchPlans()
      } catch (err) {
        console.error('Silme işlemi başarısız:', err)
      }
    },
  },
}
</script>

<style scoped>
.scheduled-export-table {
  padding: 1rem;
  background-color: #fdfdfd;
  border-radius: 8px;
  box-shadow: 0 0 4px rgba(0, 0, 0, 0.05);
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}
th, td {
  padding: 8px;
  border-bottom: 1px solid #ccc;
  text-align: left;
}
.delete-btn {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 6px 12px;
  cursor: pointer;
  border-radius: 4px;
}
.empty-message {
  margin-top: 1rem;
  color: #888;
  font-style: italic;
}
</style>