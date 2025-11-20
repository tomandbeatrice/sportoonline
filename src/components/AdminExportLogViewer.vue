<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

type ExportLog = {
  id: number
  filename: string
  admin_id: string
  exported_at: string
  filters: Record<string, any>
}

const logs = ref<ExportLog[]>([])
const search = ref('')
const dateFrom = ref('')
const dateTo = ref('')

const fetchLogs = async () => {
  const params = {
    search: search.value,
    date_from: dateFrom.value,
    date_to: dateTo.value
  }

  const res = await axios.get('/api/admin/payment-export-logs', { params })
  logs.value = res.data.data || res.data
}

const formatDate = (dateStr: string) => {
  const d = new Date(dateStr)
  return d.toLocaleString('tr-TR')
}

fetchLogs()
</script>