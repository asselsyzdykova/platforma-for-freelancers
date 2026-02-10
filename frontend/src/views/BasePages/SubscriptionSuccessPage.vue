<template>
  <div class="success-page">
    <div class="card">
      <div class="icon">✅</div>
      <h1>Subscription Successful</h1>
      <p v-if="status === 'processing'">Thank you! We are confirming your subscription…</p>
      <p v-else-if="status === 'success'">
        Thank you! Your payment has been processed, and your subscription is active.
      </p>
      <p v-else>
        {{ errorMessage || 'We could not confirm the subscription. Please contact support.' }}
      </p>
      <p v-if="sessionId" class="session">Session: {{ sessionId }}</p>
      <div class="actions">
        <RouterLink class="btn" to="/subscriptions">Back to plans</RouterLink>
        <RouterLink class="btn primary" to="/freelancer-profile">Go to profile</RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/axios'
import { useUserStore } from '@/stores/userStore'

const route = useRoute()
const sessionId = computed(() => route.query.session_id || '')
const userStore = useUserStore()
const status = ref('processing')
const errorMessage = ref('')

onMounted(async () => {
  if (!sessionId.value) {
    status.value = 'error'
    errorMessage.value = 'Missing session ID.'
    return
  }

  try {
    await api.post('/subscriptions/confirm', {
      session_id: sessionId.value,
    })
    await userStore.loadUser()
    status.value = 'success'
  } catch (error) {
    status.value = 'error'
    errorMessage.value = error?.response?.data?.error || 'Confirmation failed.'
  }
})
</script>

<style scoped>
.success-page {
  min-height: 70vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}

.card {
  max-width: 520px;
  width: 100%;
  background: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 16px;
  padding: 32px;
  text-align: center;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

.icon {
  font-size: 40px;
  margin-bottom: 12px;
}

h1 {
  margin-bottom: 8px;
}

.session {
  margin-top: 12px;
  color: #666;
  font-size: 13px;
  word-break: break-all;
}

.actions {
  margin-top: 24px;
  display: flex;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
}

.btn {
  padding: 10px 16px;
  border-radius: 8px;
  border: 1px solid #ddd;
  text-decoration: none;
  color: #111;
}

.btn.primary {
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  color: #fff;
  border-color: linear-gradient(135deg, #5D3A9B, #7c3aed);
}
</style>
