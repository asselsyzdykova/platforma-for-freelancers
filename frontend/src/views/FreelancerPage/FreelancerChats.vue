<template>
  <div class="profile-layout">
    <SidebarMenu v-if="user" :userName="user.name" />

    <div class="chats-page">
      <h1>Chats</h1>
      <p class="subtitle">Your conversations with clients and freelancers</p>

      <div v-if="loading" class="empty">Loading...</div>

      <div v-else>
        <div v-if="conversations.length" class="list">
          <div
            class="conversation"
            v-for="conv in conversations"
            :key="conv.partner.id"
            @click="openChat(conv)"
          >
            <div class="left">
              <div class="avatar-placeholder">{{ initials(conv.partner.name) }}</div>
            </div>
            <div class="content">
              <p class="title">{{ conv.partner.name || conv.partner.email }}</p>
              <p class="body">{{ conv.last_message.body }}</p>
            </div>
            <div class="meta">
              <span v-if="conv.unread_count" class="unread-count">{{ conv.unread_count }}</span>
              <span class="time">{{ formatDate(conv.last_message.created_at) }}</span>
            </div>
          </div>
        </div>

        <p v-else class="empty">No conversations yet</p>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/axios'
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'

export default {
  name: 'FreelancerChats',

  components: {
    SidebarMenu,
  },

  data() {
    return {
      conversations: [],
      loading: true,
      user: null,
    }
  },

  async mounted() {
    await this.loadUser()
    this.loadConversations()
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

    async loadConversations() {
      this.loading = true
      try {
        const res = await api.get('/conversations')
        this.conversations = res.data
      } catch (e) {
        console.error('Failed to load conversations', e)
      } finally {
        this.loading = false
      }
    },

    async openChat(conv) {
      try {
        await api.post(`/messages/${conv.partner.id}/read`)
        conv.unread_count = 0
      } catch (e) {
        console.error('Failed to mark messages as read', e)
      }
      this.$router.push({ name: 'Chat', params: { id: conv.partner.id } })
    },

    initials(name) {
      if (!name) return 'U'
      return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase()
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
.chats-page {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 40px 24px;
  max-width: 80%;
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
.conversation {
  display: grid;
  grid-template-columns: 48px 1fr auto;
  align-items: center;
  gap: 14px;
  padding: 14px 18px;
  border-radius: 14px;
  background: #f9f9f9;
  cursor: pointer;
  transition: background 0.2s;
}
.conversation:hover {
  background: #f0f0f0;
}
.avatar-placeholder {
  width: 48px;
  height: 48px;
  background: #ddd;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}
.title {
  font-weight: 600;
  margin-bottom: 6px;
}
.body {
  font-size: 14px;
  color: #555;
}
.meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
}
.unread-count {
  background: #ff3b30;
  color: white;
  padding: 6px 8px;
  border-radius: 12px;
  font-size: 12px;
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
