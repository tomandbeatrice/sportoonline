<template>
  <div class="auth-box">
    <h2>ZAMANLAYICI MODÜLÜ AKTİF</h2>
    <p v-if="countdown > 0">Oturum süresi: {{ countdown }} saniye</p>
    <p v-else>Oturum süresi doldu</p>

    <input v-model="password" placeholder="Parolayı girin" />
    <button @click="verifyPassword">Doğrula</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      password: '',
      countdown: 30,
      timer: null
    }
  },
  mounted() {
    this.timer = setInterval(() => {
      this.countdown--
      if (this.countdown <= 0) {
        clearInterval(this.timer)
        this.$router.push('/timeout')
      }
    }, 1000)
  },
  methods: {
    verifyPassword() {
      if (this.password === 'Vural') {
        clearInterval(this.timer)
        this.$router.push('/dashboard')
      } else {
        alert('Parola hatalı')
      }
    }
  }
}
</script>

<style scoped>
.auth-box {
  max-width: 400px;
  margin: 100px auto;
  padding: 20px;
  border: 2px solid #ccc;
  border-radius: 8px;
  text-align: center;
}
input {
  width: 80%;
  padding: 10px;
  margin-bottom: 10px;
}
button {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  cursor: pointer;
}
</style>