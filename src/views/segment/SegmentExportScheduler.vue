<template>
  <div class="segment-export-scheduler">
    <h3>🕒 Export Planlama</h3>

    <select v-model="segmentId">
      <option disabled value="">Segment Seçin</option>
      <option v-for="id in segments" :key="id" :value="id">Segment #{{ id }}</option>
    </select>

    <input type="time" v-model="time" />
    <select v-model="day">
      <option value="daily">Her Gün</option>
      <option value="monday">Pazartesi</option>
      <option value="friday">Cuma</option>
    </select>

    <button @click="scheduleExport">Planla</button>
    <p v-if="message">{{ message }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      segmentId: '',
      time: '02:00',
      day: 'daily',
      segments: [1, 2, 3, 4, 5],
      message: ''
    };
  },
  methods: {
    scheduleExport() {
      fetch('/admin/schedule-segment-export', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          segmentId: this.segmentId,
          time: this.time,
          day: this.day
        })
      })
      .then(res => res.json())
      .then(data => this.message = data.message);
    }
  }
}
</script>