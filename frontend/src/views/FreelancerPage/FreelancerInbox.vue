<template>
  <div class="profile-layout">
    <SidebarMenu v-if="user" :userName="user.name" />
    <div class="inbox-page">
      <h1>Inbox</h1>
      <p class="subtitle">Messages from clients and project updates</p>
      <button v-if="notifications.some(n => !n.is_read)" @click="markAllAsRead" class="mark-read">
        Mark all as read
      </button>

      <div v-if="notifications.length" class="list">
        <div class="notification" v-for="note in notifications" :key="note.id" :class="{ unread: !note.is_read }">
          <div class="left">
            <span v-if="!note.is_read" class="dot"></span>
          </div>

          <div class="content">
            <p class="title">{{ note.title }}</p>
            <p class="body">{{ note.body }}</p>
          </div>

          <div class="time">
            {{ formatDate(note.created_at) }}
          </div>

          <button class="mark-read" v-if="!note.is_read" @click.stop="markAsRead(note)">
            Mark as read
          </button>
        </div>
      </div>

      <p v-else class="empty">Inbox is empty</p>

      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>
        <button v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }"
          @click="currentPage = page">
          {{ page }}
        </button>
        <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/axios'
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'

export default {
  name: 'FreelancerInbox',

  components: {
    SidebarMenu,
  },

  data() {
    return {
      notifications: [],
      user: null,
      currentPage: 1,
      totalPages: 1,
      pageSize: 10,
    }
  },

  async mounted() {
    await this.loadUser()
    this.loadNotifications()
  },

  methods: {
    async loadUser() {
      try {
        const res = await api.get('/me', {
          headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
        })
        this.user = res.data
      } catch (e) {
        console.error('Failed to load user', e)
      }
    },

    async loadNotifications() {
      try {
        const res = await api.get('/notifications', {
          params: { page: this.currentPage, per_page: this.pageSize },
        })
        this.notifications = res.data?.data || []
        this.totalPages = res.data?.meta?.last_page || 1
      } catch (e) {
        console.error('Failed to load freelancer inbox', e)
      }
    },

    async markAsRead(note) {
      try {
        await api.post(`/notifications/${note.id}/read`)
        note.is_read = true
      } catch (e) {
        console.error('Failed to mark notification as read', e)
      }
    },

    async markAllAsRead() {
      try {
        await api.post('/notifications/read-all')
        this.notifications.forEach(n => (n.is_read = true))
      } catch (e) {
        console.error('Failed to mark all notifications as read', e)
      }
    },

    formatDate(date) {
      return new Date(date).toLocaleString()
    },
  },

  watch: {
    currentPage() {
      this.loadNotifications()
    },
  },
}
</script>

<style scoped>
.profile-layout {
  display: flex;
  min-height: 100vh;
}

.inbox-page {
  max-width: 900px;
  padding: 40px 24px;
}

.subtitle {
  color: #666;
  margin-bottom: 30px;
}

.list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.notification {
  display: grid;
  grid-template-columns: 20px 1fr auto auto;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  border-radius: 14px;
  background: #f9f9f9;
  transition: background 0.2s;
}

.notification:hover {
  background: #f0f0f0;
}

.notification.unread {
  background: #eef6ff;
}

.dot {
  width: 10px;
  height: 10px;
  background: #22c55e;
  border-radius: 50%;
}

.content {
  display: flex;
  flex-direction: column;
}

.title {
  font-weight: 600;
  margin-bottom: 4px;
}

.body {
  font-size: 14px;
  color: #555;
}

.time {
  font-size: 12px;
  color: #999;
  white-space: nowrap;
}

.empty {
  color: #777;
  text-align: center;
  margin-top: 60px;
}

.pagination {
  margin-top: 24px;
  display: flex;
  gap: 8px;
  justify-content: center;
  align-items: center;
}

.pagination button {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
}

.pagination button.active {
  background: #5b3df5;
  color: #fff;
  border-color: #5b3df5;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.mark-read {
  padding: 6px 10px;
  border: none;
  border-radius: 8px;
  background: #5b3df5;
  color: #fff;
  cursor: pointer;
  font-size: 12px;
  white-space: nowrap;
}

.mark-read:hover {
  opacity: 0.9;
}
</style>
