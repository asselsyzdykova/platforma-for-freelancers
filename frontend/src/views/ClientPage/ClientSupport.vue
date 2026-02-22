<template>
  <div class="profile-layout">
    <ClientSidebar />

    <div class="support-page">
      <h1>Support</h1>
      <p class="subtitle">Need help? Send us a message and we'll get back to you.</p>

      <form @submit.prevent="sendSupportMessage" class="support-form">
        <label>
          Subject
          <input v-model="subject" required />
        </label>

        <label>
          Message
          <textarea v-model="message" rows="6" required></textarea>
        </label>

        <div class="actions">
          <button class="primary-btn">Send</button>
        </div>

        <p v-if="sent" class="notice">Thanks! We'll reply soon.</p>
      </form>
    </div>
  </div>
</template>

<script>
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'
import ClientSidebar from '@/components/ClientPageMenu/SidebarMenu.vue'

export default {
  name: 'ClientSupport',
  components: { ClientSidebar },
  data() {
    return { subject: '', message: '', sent: false, notifications: useNotificationStore() }
  },
  methods: {
    async sendSupportMessage() {
      try {
        await api.post('/notifications', { title: this.subject, body: this.message })
        this.sent = true
        this.subject = ''
        this.message = ''
      } catch (e) {
        console.error('Failed to send support message', e)
        this.notifications.error('Failed to send message')
      }
    },
  },
}
</script>

<style scoped>
.profile-layout {
  display: flex;
  min-height: 100vh;
}

.support-page {
  width: 60%;
  padding: 30px;
  margin: 0 auto;
}

.subtitle {
  color: #666;
  margin-bottom: 20px;
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
}

.notice {
  color: #22c55e;
  margin-top: 12px;
}
</style>
