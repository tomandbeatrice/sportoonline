<template>
  <div class="export-list">
    <h3>📅 Planlanmış Export Görevleri</h3>
    <table v-if="exports.length">
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
    <p v-else>Henüz planlanmış görev yok.</p>
  </div>
</template>

<script>
export default {
  data() {
    return { exports: [] }
  },
  mounted() {
    this.fetchPlans()
  },
  methods: {
    fetchPlans() {
      fetch('/admin/scheduled-exports')
        .then(res => res.json())
        .then(data => this.exports = data)
    },
    deletePlan(item) {
      fetch('/admin/scheduled-exports', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          segmentId: item.segmentId,
          time: item.time,
          day: item.day
        })
      })
      .then(() => this.fetchPlans())
    }
  }
}
</script>

<style scoped>
.export-list {
  margin-top: 2rem;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 8px;
  border: 1px solid #ccc;
  text-align: center;
}
.delete-btn {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 6px 12px;
  cursor: pointer;
  border-radius: 4px;
}
</style>