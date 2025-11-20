<template>
  <div class="sprint-status">
    <div v-if="warning" class="alert alert-warning">
      {{ warning }}
    </div>

    <div v-if="log" class="log-panel">
      <h3>🧠 Sprint Log Detayları</h3>
      <pre v-html="log"></pre>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SprintStatus',
  data() {
    return {
      warning: null,
      log: null
    }
  },
  mounted() {
    fetch('/api/sprint/status')
      .then(res => res.json())
      .then(data => {
        if (!data.ok) {
          this.warning = '⚠️ Sistem reflekslerinde hata var. Sprint loglarını kontrol edin.';
        }

        // Son 10 satırı al, renklendir
        const lines = data.log.split('\n').slice(-10);
        const colored = lines.map(line =>
          line
            .replace(/✔/g, '<span style="color:green">✔</span>')
            .replace(/❌/g, '<span style="color:red">❌</span>')
            .replace(/⚠️/g, '<span style="color:orange">⚠️</span>')
        );
        this.log = colored.join('\n');
      });
  }
}
</script>

<style scoped>
.sprint-status {
  padding: 20px;
  background: #f9f9f9;
  border: 1px solid #ddd;
  margin-top: 20px;
}
.alert {
  padding: 10px;
  background-color: #fff3cd;
  border: 1px solid #ffeeba;
  color: #856404;
  font-weight: bold;
}
.log-panel {
  margin-top: 15px;
  background: #fff;
  border: 1px solid #ccc;
  padding: 15px;
  font-family: monospace;
  white-space: pre-wrap;
}
</style>