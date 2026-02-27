<template>


  <div class="inbox-page">
    <h1>Inbox</h1>
    <p class="subtitle">Messages and notifications from freelancers</p>

    <template v-if="notifications.length">
      <div class="list">
        <div class="notification" v-for="note in notifications" :key="note.id" :class="{ unread: !note.is_read }"
          @click="openNotification(note)">
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
        </div>
      </div>

      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>
        <button v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }"
          @click="currentPage = page">
          {{ page }}
        </button>
        <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
      </div>
    </template>

    <p v-else class="empty">Inbox is empty</p>
  </div>

</template>

<script>
import api from '@/services/axios'

export default {
  name: 'ClientInbox',


  data() {
    return {
      notifications: [],
      currentPage: 1,
      totalPages: 1,
      pageSize: 10,
    }
  },

  mounted() {
    this.loadNotifications()
  },

  methods: {
    async loadNotifications() {
      try {
        const res = await api.get('/notifications', {
          params: { page: this.currentPage, per_page: this.pageSize },
        })
        this.notifications = res.data?.data || []
        this.totalPages = res.data?.meta?.last_page || 1
      } catch (e) {
        console.error('Failed to load inbox', e)
      }
    },

    async openNotification(note) {
      try {
        if (!note.is_read) {
          await api.post(`/notifications/${note.id}/read`)
          note.is_read = true
        }

        if (note.link) {
          if (note.link.startsWith('/application-details/') && note.related_id) {
            this.$router.push({ name: 'ApplicationDetails', params: { id: note.related_id } })
            return
          }

          const resolved = this.$router.resolve(note.link)
          if (resolved && resolved.matched && resolved.matched.length) {
            this.$router.push(note.link)
            return
          }

          if (note.link.startsWith('/projects/')) {
            this.$router.push({ name: 'projects' })
            return
          }
        }

        if (note.type === 'project_application') {
          if (note.related_id) {
            this.$router.push({ name: 'ApplicationDetails', params: { id: note.related_id } })
            return
          }
        }

        console.warn('No route for this notification')
      } catch (e) {
        console.error('Failed to open notification', e)
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
.inbox-page {
  max-width: 1100px;
  margin: 0 auto;
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
  grid-template-columns: 20px 1fr auto;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  border-radius: 14px;
  background: #f9f9f9;
  cursor: pointer;
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
</style>
