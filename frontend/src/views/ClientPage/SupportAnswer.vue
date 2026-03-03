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
        <small v-if="ticket.manager && ticket.manager.user" class="manager">
          Answered by: {{ ticket.manager.user.name }}
        </small>
        <small v-if="ticket.manager && ticket.manager.user" class="manager">
          Email: {{ ticket.manager.user.email }}
        </small>
      </div>

      <div v-else class="no-response">
        <p>This ticket has not been answered yet.</p>
      </div>

      <div v-if="ticket.manager" class="report-button">
        <button v-if="user.role!=='admin'" @click="showModal = true">Report Manager</button>
      </div>

      <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal">
          <h3>Report Manager</h3>
          <textarea v-model="reportDescription" placeholder="Describe the issue..." rows="4"></textarea>
          <div class="modal-actions">
            <button @click="submitReport">Submit</button>
            <button @click="showModal = false" class="cancel">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="empty">Ticket not found.</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'

const route = useRoute()
const ticketId = route.params.id
const notifications = useNotificationStore()

const ticket = ref(null)
const loading = ref(true)
const showModal = ref(false)
const reportDescription = ref('')

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

const submitReport = async () => {
  if (!ticket.value?.manager) return

  try {
    await api.post('/reports', {
      reported_user_id: ticket.value.manager.user.id,
      ticket_id: ticket.value.id,
      reason: 'Issue with manager',
      description: reportDescription.value
    })
    notifications.success('Report submitted successfully!')
    reportDescription.value = ''
    showModal.value = false
  } catch (err) {
    console.error('Failed to submit report:', err)
    notifications.error('Failed to submit report')
  }
}

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

.report-button {
  margin-top: 20px;
  text-align: right;
}

.report-button button {
  background: #ff4d4f;
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.report-button button:hover {
  background: #d9363e;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: #fff;
  padding: 24px;
  border-radius: 12px;
  max-width: 400px;
  width: 100%;
}

.modal textarea {
  width: 100%;
  padding: 10px;
  margin: 12px 0;
  border-radius: 8px;
  border: 1px solid #ccc;
  resize: none;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.modal-actions button {
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
  border: none;
}

.modal-actions button.cancel {
  background: #ccc;
}

.modal-actions button:not(.cancel) {
  background: #ff4d4f;
  color: #fff;
}

.modal-actions button:not(.cancel):hover {
  background: #d9363e;
}
</style>
