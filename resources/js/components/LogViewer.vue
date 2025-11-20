<template>
  <div class="log-viewer">
    <h3>📡 Canlı Log Takibi</h3>
    <div v-for="(line, index) in logs" :key="index" :class="logClass(line)" class="log-line">
      {{ line }}
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return { logs: [] };
  },
  methods: {
    fetchLogs() {
      fetch('/admin/logs/live')
        .then(res => res.json())
        .then(data => this.logs = data);
    },
    logClass(line) {
      if (line.includes('ERROR')) return 'text-red-600';
      if (line.includes('WARNING')) return 'text-yellow-600';
      return 'text-gray-600';
    }
  },
  mounted() {
    this.fetchLogs();
    setInterval(this.fetchLogs, 5000);
  }
}
</script>

<style scoped>
.log-line {
  font-family: monospace;
  padding: 4px 0;
}
</style>