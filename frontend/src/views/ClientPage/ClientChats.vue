<template>
  <div class="profile-layout">
    <ClientSidebar />

    <div class="chats-page">
      <h1>Chats</h1>
      <p class="subtitle">Your conversations with freelancers</p>

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
    </div>
  </div>
</template>

<script>
import api from '@/services/axios'
import ClientSidebar from '@/components/ClientPageMenu/SidebarMenu.vue'

export default {
  name: 'ClientChats',

  components: {
    ClientSidebar,
  },

  data() {
    return {
      conversations: [],
      loading: true,
      currentPage: 1,
      totalPages: 1,
      pageSize: 8,
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
      return new Date(date).toLocaleString()
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
.profile-layout {
  display: flex;
  min-height: 100vh;
}
.chats-page {
  width: 60%;
  padding: 30px;
  margin: 0 auto;
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
