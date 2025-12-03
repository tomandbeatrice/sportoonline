<template>
  <div class="system-brain-panel">
    <h3>🧠 SystemBrain Paneli</h3>

    <section>
      <h4>⏱️ Komut Durumu</h4>
      <ul>
        <li v-for="cmd in status.commands" :key="cmd.name">
          {{ cmd.name }} → Son çalıştırma: {{ cmd.lastRun || 'Henüz çalıştırılmadı' }}
        </li>
      </ul>
    </section>

    <section>
      <h4>📡 Log Durumu</h4>
      <ul>
        <li v-for="log in status.logs" :key="log.timestamp">
          {{ log.level }} → {{ log.message }} ({{ log.timestamp }})
        </li>
      </ul>
    </section>

    <section>
      <h4>📊 Son Exportlar</h4>
      <ul>
        <li v-for="exp in status.exports" :key="exp.filename">
          Segment #{{ exp.segmentId }} → {{ exp.filename }} ({{ exp.timestamp }})
        </li>
      </ul>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return { status: { commands: [], logs: [], exports: [] } };
  },
  mounted() {
    fetch('/admin/systembrain-status')
      .then(res => res.json())
      .then(data => this.status = data);
  }
}
</script>