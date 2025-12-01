<script setup>
import { ref } from 'vue'
import axios from 'axios'

const joined = ref({})
const loading = ref(false)
const error = ref(null)

const join = async (code) => {
  try {
    loading.value = true
    error.value = null

    const { data: user } = await axios.get('/api/user')
    const { data: res } = await axios.post(`/api/seller/${user.id}/join-discount-campaign`, {
      code,
      discount: 15
    })

    joined.value[code] = res.newCommissionRate
  } catch (err) {
    error.value = 'Katılım başarısız oldu.'
  } finally {
    loading.value = false
  }
}
</script>