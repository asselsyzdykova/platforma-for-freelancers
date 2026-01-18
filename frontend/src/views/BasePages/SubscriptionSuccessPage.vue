<template>
  <div class="success-page">
    <div class="card">
      <div class="icon">✅</div>
      <h1>Subscription Successful</h1>
<p>Thank you! Your payment has been processed, and your subscription will be activated within a minute.</p>
      <p v-if="sessionId" class="session">Session: {{ sessionId }}</p>
      <div class="actions">
        <RouterLink class="btn" to="/subscriptions">Back to plans</RouterLink>
        <RouterLink class="btn primary" to="/freelancer-profile">Go to profile</RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const sessionId = computed(() => route.query.session_id || '')
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
  background: #4f46e5;
  color: #fff;
  border-color: #4f46e5;
}
</style>
