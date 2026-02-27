<template>
  <div class="answer-page">
    <h2>Support Ticket Response</h2>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else-if="ticket">
      <h3 class="ticket-title">{{ ticket.subject }}</h3>
      <p class="ticket-status">Status: <strong>{{ ticket.status }}</strong></p>

      <div class="response" v-if="ticket.response">
        <h4>Support Response:</h4>
        <p>{{ ticket.response }}</p>
        <small class="time">Answered at: {{ formatDate(ticket.updated_at) }}</small>
        <small v-if="ticket.manager&& ticket.manager.user" class="manager">
          Answered by: {{ ticket.manager.user.name }}
        </small>
        <small class="manager">email: {{ ticket.manager.user.email }}</small>
      </div>

      <div v-else class="no-response">
        <p>This ticket has not been answered yet.</p>
      </div>
    </div>

    <div v-else class="empty">Ticket not found.</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/axios'

const route = useRoute()
const ticketId = route.params.id

const ticket = ref(null)
const loading = ref(true)

const loadTicket = async () => {
  try {
    const res = await api.get(`/tickets/${ticketId}`)
    ticket.value = res.data
  } catch (err) {
    console.error('Failed to load ticket:', err)
  } finally {
    loading.value = false
  }
}

const formatDate = (date) => new Date(date).toLocaleString()

onMounted(() => {
  loadTicket()
})
</script>

<style scoped>
.answer-page {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 24px;
}

.ticket-title {
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 8px;
}

.ticket-status {
  font-size: 14px;
  color: #555;
  margin-bottom: 24px;
}

.response {
  background: #f9f9f9;
  padding: 20px;
  border-radius: 12px;
}

.response h4 {
  font-size: 18px;
  margin-bottom: 10px;
}

.response p {
  font-size: 16px;
  color: #333;
}

.time {
  display: block;
  margin-top: 12px;
  font-size: 12px;
  color: #888;
}

.manager {
  display: block;
  margin-top: 6px;
  font-size: 12px;
  color: #555;
}

.no-response {
  background: #fff3cd;
  padding: 16px;
  border-radius: 12px;
  color: #856404;
}

.loading,
.empty {
  text-align: center;
  color: #777;
  font-size: 16px;
  margin-top: 40px;
}
</style>
