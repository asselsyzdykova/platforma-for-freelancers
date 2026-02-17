<template>
  <div class="admin-chats-page">
    <header class="admin-header">
      <div>
        <h1>Manager: Chats</h1>
      </div>
    </header>

    <section class="chats-wrapper">
      <div v-if="loading" class="empty">Loading...</div>

      <div v-else>
        <template v-if="conversations.length">
          <div class="list">
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

          <div class="pagination" v-if="totalPages > 1">
            <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>
            <button
              v-for="page in totalPages"
              :key="page"
              :class="{ active: page === currentPage }"
              @click="currentPage = page"
            >
              {{ page }}
            </button>
            <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
          </div>
        </template>

        <p v-else class="empty">No conversations yet</p>
      </div>
    </section>
  </div>
</template>

<script>
import api from '@/services/axios'

export default {
  name: 'ManagerChats',
  data() {
    return {
      conversations: [],
      loading: true,
      currentPage: 1,
      totalPages: 1,
      pageSize: 10,
    }
  },
  async mounted() {
    this.loadConversations()
  },
  methods: {
    async loadConversations() {
      this.loading = true
      try {
        const res = await api.get('/conversations', {
          params: { page: this.currentPage, per_page: this.pageSize },
        })
        this.conversations = res.data?.data || []
        this.totalPages = res.data?.meta?.last_page || 1
      } catch (e) {
        console.error('Failed to load conversations', e)
        this.conversations = []
        this.totalPages = 1
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
      return date ? new Date(date).toLocaleString() : ''
    },
  },
  watch: {
    currentPage() {
      this.loadConversations()
    },
  },
}
</script>

<style scoped>
.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  padding: 20px 24px;
}
.subtitle {
  color: #6b7280;
  margin-top:6px
}
.chats-wrapper {
  padding: 16px 24px
}
.list {
  display:flex;
  flex-direction:column;
  gap:10px
}
.conversation {
  display:grid;
  grid-template-columns:48px 1fr auto;
  gap:12px;
  align-items:center;
  padding:12px;
  border-radius:12px;
  background:#fff;
  cursor:pointer;
  border:1px solid #f1f5f9
}
.avatar-placeholder {
  width:48px;
  height:48px;
  background:#eee;
  border-radius:8px;
  display:grid;
  place-items:center;
  font-weight:700
}
.title {
  font-weight:600
}
.body {
  color:#555;
  font-size:14px
}
.meta {
  display:flex;
  flex-direction:column;
  align-items:flex-end;
  gap:6px
}
.unread-count {
  background:#ef4444;
  color:#fff;
  padding:4px 8px;
  border-radius:10px;
  font-size:12px
}
.time {
  font-size:12px;
  color:#9ca3af
}
.empty {
  color:#777;
  text-align:center;
  margin-top:40px
}
.pagination {
  margin-top:14px;
  display:flex;
  gap:8px;
  justify-content:center
}
.pagination button {
  padding:6px 10px;
  border-radius:8px;
  border:1px solid #e5e7eb;
  background:#fff
}
.pagination button.active {
  background:#5b3df5;
  color:#fff
  }
</style>
