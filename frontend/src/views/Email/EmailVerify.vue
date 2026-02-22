<template>
  <div class="verify-email-page">
    <h1>Check Your Email</h1>
    <p>Please check your email to confirm your account.</p>
    <button @click="resendVerification" :disabled="loading">
      {{ loading ? 'Sending...' : 'Resend Email' }}
    </button>
    <p v-if="message">{{ message }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/axios'

const loading = ref(false)
const message = ref('')
const route = useRoute()
const email = ref('')

onMounted(() => {
  email.value = route.query.email || ''
})

const resendVerification = async () => {
  if (!email.value) {
    message.value = 'Email not found. Please register again.'
    return
  }
  loading.value = true
  message.value = ''
  try {
    await api.post('/email/verification-notification', { email: email.value })
    message.value = 'Email resent successfully!'
  } catch {
    message.value = 'Error sending email. Please try again later.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.verify-email-page {
  text-align: center;
  padding: 2rem;
}

button {
  padding: 0.5rem 1rem;
  margin-top: 1rem;
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  color: white;
  border-radius: 10px;
  cursor: pointer;
}

button:hover {
  background: white;
  color: black;
}
</style>
