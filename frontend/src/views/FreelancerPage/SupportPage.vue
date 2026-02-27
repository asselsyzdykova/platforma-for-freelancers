<template>
  <div class="page-layout">
    <SidebarMenu />

    <div class="content">
      <h1>Support</h1>

      <div class="support-card">
        <p class="subtitle">
          If you have any issues or questions, contact our support team.
        </p>
        <form @submit.prevent="sendSupportMessage" class="support-form">
          <label>
            Subject
            <input v-model="subject" required />
          </label>

          <label>
            Message
            <textarea v-model="description" rows="6" required></textarea>
          </label>

          <div class="actions">
            <button class="primary-btn" @click="sendTicket">Send</button>
          </div>

          <p v-if="sent" class="notice">Thanks! We'll reply soon.</p>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'
export default {
  name: 'FreelancerSupport',
  components: { SidebarMenu },
  data() {
    return { subject: '', description: '', sent: false, notifications: useNotificationStore() }
  },
  methods: {
    async sendTicket() {
      try {
        await api.post('/tickets', { subject: this.subject, description: this.description })
        this.sent = true
        this.subject = ''
        this.description = ''
      } catch (e) {
        console.error('Failed to send support message', e)
        this.notifications.error('Failed to send message')
      }
    },
  },
}
</script>

<style scoped>
.page-layout {
  display: flex;
  min-height: 100vh;
  background: #f7f6ff;
}

.content {
  flex: 1;
  padding: 40px;
}

h1 {
  font-size: 32px;
  margin-bottom: 16px;
}

.subtitle {
  margin-bottom: 20px;
  font-size: 14px;
  color: #555;
}

.support-card {
  background: #e9e3ff;
  border-radius: 30px;
  padding: 40px;
  max-width: 600px;
}

.support-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.support-form input,
.support-form textarea {
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.primary-btn {
  padding: 10px 14px;
  border-radius: 8px;
  background: #5b4b8a;
  color: white;
  border: none;
  cursor: pointer;
}

.notice {
  color: #22c55e;
  margin-top: 12px;
}
</style>
