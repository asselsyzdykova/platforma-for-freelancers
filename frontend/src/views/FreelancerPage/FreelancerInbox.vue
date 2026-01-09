<template>
  <div class="profile-layout">
    <SidebarMenu v-if="user" :userName="user.name" />
    <div class="inbox-page">
      <h1>Inbox</h1>
      <p class="subtitle">Messages from clients and project updates</p>

      <div v-if="notifications.length" class="list">
        <div
          class="notification"
          v-for="note in notifications"
          :key="note.id"
          :class="{ unread: !note.is_read }"
          @click="openNotification(note)"
        >
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

      <p v-else class="empty">Inbox is empty</p>
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
        const res = await api.get('/freelancer/notifications')
        this.notifications = res.data.sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at),
        )
      } catch (e) {
        console.error('Failed to load freelancer inbox', e)
      }
    },

    async openNotification(note) {
      try {
        if (!note.is_read) {
          await api.post(`/freelancer/notifications/${note.id}/read`)
          note.is_read = true
        }

        if (note.link) {
          const resolved = this.$router.resolve(note.link)
          if (resolved && resolved.matched && resolved.matched.length) {
            this.$router.push(note.link)
          } else if (note.link.startsWith('/projects/')) {
            this.$router.push({ name: 'projects' })
          } else {
            this.$router.push({ name: 'home' })
          }
        }
      } catch (e) {
        console.error('Failed to open notification', e)
      }
    },

    formatDate(date) {
      return new Date(date).toLocaleString()
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
</style>
